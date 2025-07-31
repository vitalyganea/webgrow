# WebGrow

<p align="center"><img src="https://i.imgflip.com/ahgny.jpg?a485856" alt="Web Grow"></p>

A comprehensive Content Management System (CMS) built with Laravel framework, designed for creating and managing multilingual websites with advanced SEO capabilities, visual page editing, and powerful admin tools.

## 🌟 Key Features

### 📝 Advanced Page Editor
- **Visual Page Builder** - Intuitive drag-and-drop interface for creating pages
- **Content Management** - Rich text editor with media integration
- **Page Templates** - Pre-designed templates for quick page creation
- **Content Backup System** - Automatic backup and version control for all content
- **Real-time Preview** - See changes as you make them

### 🌍 Multi-Language Support
- **Language Management** - Add, edit, and manage multiple languages
- **Content Localization** - Translate pages and content for different languages
- **Language Switching** - Seamless language switching for visitors
- **Localized URLs** - SEO-friendly URLs for each language

### 🔍 Advanced SEO Tools
- **SEO Tag Management** - Custom meta titles, descriptions, and keywords
- **Page-specific SEO** - Individual SEO settings for each page
- **Meta Tag Generator** - Automatic generation of essential meta tags
- **SEO Analytics** - Built-in SEO performance tracking
- **Schema Markup** - Structured data for better search engine understanding
- **Robots.txt Generator** - Dynamic robots.txt file generation
- **Sitemap Generation** - Automatic XML sitemap creation

### 📋 Dynamic Form Builder
- **Form Designer** - Visual form builder with drag-and-drop interface
- **Custom Fields** - Various field types (text, email, select, checkbox, etc.)
- **Form Validation** - Server-side and client-side validation
- **Form Submissions** - Manage and export form submissions
- **Email Notifications** - Automatic email notifications for form submissions

### 📁 Media Management
- **File Manager** - Complete file and media management system
- **Image Upload** - Drag-and-drop image uploading with optimization
- **Media Gallery** - Organized media gallery with search and filtering
- **Image Optimization** - Automatic image compression and resizing
- **Cloud Storage** - Support for cloud storage integration

### 👥 User Management & Security
- **Role-Based Access Control** - Granular permissions system
- **User Dashboard** - Personalized user dashboards
- **Admin Authentication** - Secure admin login with Laravel Breeze
- **User Roles** - Multiple user roles (Admin, Editor, Author, etc.)
- **Activity Logging** - Track user activities and changes

### 🚀 Performance & Development
- **Script Management** - Custom JavaScript and CSS injection
- **Caching System** - Advanced caching for better performance
- **Database Optimization** - Optimized database queries and indexing
- **API Ready** - RESTful API endpoints for headless CMS usage

## 🛠 Technology Stack

### Backend
- **Laravel 12.17.0** - PHP web application framework
- **PHP 8.2** - Server-side scripting language
- **MySQL** - Relational database management system
- **Laravel Sanctum 4.1.1** - API authentication
- **Laravel Tinker 2.10.1** - Interactive shell

### Frontend
- **TailwindCSS 3.1.0** - Utility-first CSS framework
- **Alpine.js 3.13.1** - Lightweight JavaScript framework
- **Vite 4.0.0** - Fast build tool and development server
- **Axios 1.1.2** - HTTP client for API requests

### Development & Testing
- **PHPUnit** - PHP testing framework
- **Mockery 1.6.12** - Mocking framework
- **Laravel Pint 1.22.1** - Code style fixer
- **Prettier 3.0.3** - Code formatting for Blade templates
- **Faker 1.24.1** - Test data generation

### Additional Packages
- **Guzzle HTTP 7.9.3** - HTTP client library
- **Monolog 3.9.0** - Logging library
- **Carbon** - Date manipulation library

## 📋 System Requirements

- PHP >= 8.2
- Composer
- Node.js >= 16.x & NPM
- MySQL >= 8.0
- Git
- Web server (Apache/Nginx)

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/vitalyganea/webgrow.git
   cd webgrow
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database configuration**
    - Create a MySQL database
    - Update your `.env` file with database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=webgrow
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run database migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

7. **Set up file permissions**
   ```bash
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   ```

8. **Build frontend assets**
   ```bash
   npm run build
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

Your application will be available at `http://localhost:8000`

## 🔧 Development

### Running in development mode
