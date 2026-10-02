<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        if (! Permission::where('name', 'warehouse.adjust_stock')->where('guard_name', 'web')->exists()) {
            Permission::create([
                'name' => 'warehouse.adjust_stock',
                'guard_name' => 'web',
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin_roles = Role::where('name', 'like', 'Admin#%')->get();
        foreach ($admin_roles as $role) {
            if (! $role->hasPermissionTo('warehouse.adjust_stock', 'web')) {
                $role->givePermissionTo('warehouse.adjust_stock');
            }
        }
    }

    public function down(): void
    {
        //
    }
};
