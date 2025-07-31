<?php

return [
    [
        'label' => 'Pages',
        'route' => 'admin.get.pages',
        'icon' => 'fas fa-file-alt',
        'active_routes' => ['admin.get.pages', 'admin.create.page', 'admin.edit.page'],
    ],
    [
        'label' => 'Languages',
        'route' => 'admin.get.languages',
        'icon' => 'fas fa-language',
        'active_routes' => ['admin.get.languages', 'admin.create.language', 'admin.edit.language'],
    ],
    [
        'label' => 'Seo Tags',
        'route' => 'admin.get.seo-tags',
        'icon' => 'fas fa-tags',
        'active_routes' => ['admin.get.seo-tags', 'admin.create.seo-tag', 'admin.edit.seo-tag'],
    ],
    [
        'label' => 'Backup Content',
        'route' => 'admin.get.content-backup',
        'icon' => 'fa fa-database',
        'active_routes' => ['admin.get.content-backup', 'admin.create.content-backup', 'admin.restore.content-backup'],
    ],
    [
        'label' => 'Scripts',
        'route' => 'admin.get.scripts',
        'icon' => 'fa fa-code',
        'active_routes' => ['admin.get.scripts', 'admin.edit.script', 'admin.create.script'],
    ],
    [
        'label' => 'Form Requests',
        'route' => 'admin.get.form-requests',
        'icon' => 'fas fa-comments',
        'active_routes' => ['admin.get.form-requests'],
        'count' => 'form_requests',
    ],

    [
        'label' => 'Public Files',
        'route' => 'admin.get.file-manager',
        'icon' => 'fa fa-file',
        'active_routes' => ['admin.get.file-manager'],
    ],

    [
        'label' => 'Users',
        'route' => 'admin.users.index',
        'icon' => 'fa fa-user',
        'active_routes' => ['admin.users.*'],
    ],

    [
        'label' => 'Settings',
        'route' => 'admin.settings.account',
        'icon' => 'fas fa-cog',
        'active_routes' => ['admin.settings.*'],
    ],
];
