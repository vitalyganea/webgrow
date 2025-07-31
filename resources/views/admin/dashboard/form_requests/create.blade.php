<x-admin.layouts.auth title="Create SEO Tag">
    <x-slot:header>
        Create a New SEO Tag
    </x-slot:header>

    <x-admin.card>
        <x-slot:header>
            <x-slot:title>SEO Tag Information</x-slot:title>
            <x-slot:description>Define your SEO tag (e.g. keywords, meta tag).</x-slot:description>
        </x-slot:header>

        <x-slot:content>
            <form method="POST" action="{{ route('admin.store.seo-tag') }}" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <x-admin.label for="seo_tag" value="SEO Tag" class="text-foreground" />
                    <x-admin.input
                        id="seo_tag"
                        name="seo_tag"
                        type="text"
                        class="mt-1 block w-full bg-input border-border text-foreground placeholder:text-muted-foreground focus:border-ring focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:ring-offset-background"
                        value="{{ old('seo_tag') }}"
                        required
                    />
                    <x-admin.input-error class="mt-2 text-destructive" :messages="$errors->get('seo_tag')" />
                </div>

                <div class="space-y-2">
                    <x-admin.label for="type" value="SEO Tag Type" class="text-foreground" />
                    <select
                        id="type"
                        name="type"
                        class="mt-1 block w-full bg-input border-border text-foreground focus:border-ring focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:ring-offset-background rounded-md shadow-sm"
                        required
                    >
                        <option value="" class="text-muted-foreground">Select Type</option>
                        <option value="text" {{ old('type') == 'text' ? 'selected' : '' }} class="text-foreground">Text</option>
                        <option value="image" {{ old('type') == 'image' ? 'selected' : '' }} class="text-foreground">Image</option>
                    </select>
                    <x-admin.input-error class="mt-2 text-destructive" :messages="$errors->get('type')" />
                </div>

                <div class="space-y-2">
                    <x-admin.label for="tag_format" value="SEO Tag format" class="text-foreground" />
                    <x-admin.input
                        id="tag_format"
                        name="tag_format"
                        type="text"
                        class="mt-1 block w-full bg-input border-border text-foreground placeholder:text-muted-foreground focus:border-ring focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:ring-offset-background"
                        value="{{ old('tag_format') }}"
                        required
                    />
                    <x-admin.input-error class="mt-2 text-destructive" :messages="$errors->get('tag_format')" />
                </div>

                <div class="flex items-center gap-4 pt-6 border-t border-border">
                    <x-admin.button class="bg-primary text-primary-foreground hover:bg-primary/90 focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:ring-offset-background disabled:opacity-50">
                        Create SEO Tag
                    </x-admin.button>
                    <x-admin.action-message status="seo-tag-created" class="text-muted-foreground" />
                </div>
            </form>
        </x-slot:content>
    </x-admin.card>
</x-admin.layouts.auth>
