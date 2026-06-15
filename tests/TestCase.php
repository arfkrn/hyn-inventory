<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();



        $this->app->register(\App\Providers\Filament\AdminPanelProvider::class);

        // Buat permission yang dibutuhkan Filament
        Permission::firstOrCreate([
            'name' => 'view_any_bahan',
            'guard_name' => 'web',
        ]);

        $role = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $role->givePermissionTo('view_any_bahan');
    }
}
