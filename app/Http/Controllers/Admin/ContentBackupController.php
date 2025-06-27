<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\ContentBackup;
use App\Models\Admin\Page;
use App\Models\Admin\SeoTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ContentBackupController extends Controller
{
    public function index()
    {
        $contentBackups = ContentBackup::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.dashboard.content_backup.index', compact('contentBackups'));
    }

    public function create(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'password' => 'required|string',
            ]);

            // Verify the user's password
            $user = Auth::user();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Invalid password. Please try again.'], 401);
            }

            DB::beginTransaction();

            // Get all table names from the database
            $tables = DB::select('SHOW TABLES');
            $database = env('DB_DATABASE');
            $tableKey = 'Tables_in_' . $database;
            $backupData = [];

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;

                if ($tableName === 'content_backups') {
                    continue;
                }

                $records = DB::table($tableName)->get();
                $sqlInserts = [];

                foreach ($records as $record) {
                    $columns = array_keys((array) $record);
                    // Escape column names with backticks
                    $escapedColumns = array_map(function($column) {
                        return '`' . $column . '`';
                    }, $columns);

                    $values = array_map(function($value) {
                        return is_null($value) ? 'NULL' : DB::getPdo()->quote($value);
                    }, (array) $record);

                    $sql = "INSERT INTO `$tableName` (" .
                        implode(', ', $escapedColumns) .
                        ") VALUES (" .
                        implode(', ', $values) .
                        ");";

                    $sqlInserts[] = $sql;
                }

                $backupData[$tableName] = $sqlInserts;
            }

            // Create new backup using the model
            ContentBackup::create([
                'script' => $backupData, // Model will auto-encode to JSON
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Backup created successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create backup: ' . $e->getMessage()], 500);
        }
    }
    public function restore(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'backup_id' => 'required|exists:content_backups,id',
                'password' => 'required|string',
            ]);

            // Verify the user's password
            $user = Auth::user();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Invalid password. Please try again.'], 401);
            }

            // Start transaction
            DB::beginTransaction();

            // Retrieve the backup using the model
            $backup = ContentBackup::findOrFail($request->backup_id);
            $backupData = $backup->script; // Already decoded as array due to casts

            if (!is_array($backupData)) {
                throw new \Exception('Invalid backup data format.');
            }

            // Get all tables to clear (except content_backups)
            $tables = DB::select('SHOW TABLES');
            $database = env('DB_DATABASE');
            $tableKey = 'Tables_in_' . $database;

            // Disable foreign key checks to avoid constraint issues
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Delete all records from tables except content_backups
            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                if ($tableName !== 'content_backups') {
                    DB::table($tableName)->delete(); // Use delete instead of truncate
                }
            }

            // Execute SQL insert statements
            foreach ($backupData as $tableName => $sqlStatements) {
                foreach ($sqlStatements as $sql) {
                    DB::statement($sql);
                }
            }

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Verify transaction is still active
            if (!DB::transactionLevel()) {
                throw new \Exception('Transaction was unexpectedly closed.');
            }

            DB::commit();
            return response()->json(['message' => 'Backup restored successfully'], 200);
        } catch (\Exception $e) {
            // Only attempt rollback if transaction is active
            if (DB::transactionLevel()) {
                DB::rollBack();
            }
            return response()->json(['message' => 'Failed to restore backup: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            // Validate input
            $request->validate([
                'password' => 'required|string',
            ]);

            // Verify the user's password
            $user = Auth::user();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return redirect()->back()->with('error', 'Invalid password. Please try again.');
            }

            $backup = ContentBackup::findOrFail($id);
            $backup->delete();
            return redirect()->back()->with('success', 'Backup deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete backup: ' . $e->getMessage());
        }
    }
}
