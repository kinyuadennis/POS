<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('business_locations', function (Blueprint $table) {
            if (! Schema::hasColumn('business_locations', 'location_type')) {
                $table->string('location_type', 20)->default('shop')->after('name');
            }
        });

        $permissions = [
            'warehouse.access',
            'warehouse.view_stock',
            'warehouse.dispatch',
            'warehouse.receive_stock',
            'warehouse.manage_settings',
            'warehouse.adjust_stock',
        ];

        foreach ($permissions as $permission) {
            if (! Permission::where('name', $permission)->where('guard_name', 'web')->exists()) {
                Permission::create([
                    'name' => $permission,
                    'guard_name' => 'web',
                ]);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin_roles = Role::where('name', 'like', 'Admin#%')->get();
        foreach ($admin_roles as $role) {
            foreach ($permissions as $permission) {
                if (! $role->hasPermissionTo($permission, 'web')) {
                    $role->givePermissionTo($permission);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_locations', function (Blueprint $table) {
            if (Schema::hasColumn('business_locations', 'location_type')) {
                $table->dropColumn('location_type');
            }
        });
    }
};
