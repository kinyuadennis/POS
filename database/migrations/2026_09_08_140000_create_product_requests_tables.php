<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('product_requests')) {
            Schema::create('product_requests', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('business_id');
                $table->string('request_no', 100);
                $table->unsignedInteger('requesting_location_id');
                $table->unsignedInteger('source_location_id');
                $table->unsignedInteger('created_by');
                $table->string('status', 50)->default('draft');
                $table->string('priority', 30)->default('medium');
                $table->date('required_date')->nullable();
                $table->text('notes')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->unsignedInteger('approved_by')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->unsignedInteger('stock_transfer_id')->nullable();
                $table->unsignedInteger('dispatched_by')->nullable();
                $table->dateTime('dispatched_at')->nullable();
                $table->unsignedInteger('received_by')->nullable();
                $table->dateTime('received_at')->nullable();
                $table->timestamps();

                $table->index(['business_id', 'requesting_location_id']);
                $table->index(['source_location_id', 'status']);
                $table->index('stock_transfer_id');
                $table->index('created_by');
            });
        }

        if (! Schema::hasTable('product_request_lines')) {
            Schema::create('product_request_lines', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('product_request_id');
                $table->unsignedInteger('product_id');
                $table->unsignedInteger('variation_id');
                $table->decimal('requested_quantity', 22, 4);
                $table->decimal('approved_quantity', 22, 4)->nullable();
                $table->decimal('dispatched_quantity', 22, 4)->nullable();
                $table->decimal('received_quantity', 22, 4)->nullable();
                $table->decimal('discrepancy_quantity', 22, 4)->default(0);
                $table->text('discrepancy_reason')->nullable();
                $table->string('notes', 255)->nullable();
                $table->timestamps();

                $table->index('product_request_id');
                $table->index(['product_id', 'variation_id']);

                $table->foreign('product_request_id')
                    ->references('id')
                    ->on('product_requests')
                    ->onDelete('cascade');
            });
        }

        if (! Schema::hasTable('product_request_activities')) {
            Schema::create('product_request_activities', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('product_request_id');
                $table->unsignedInteger('user_id');
                $table->string('action', 100);
                $table->string('from_status', 50)->nullable();
                $table->string('to_status', 50)->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index('product_request_id');
                $table->index('user_id');

                $table->foreign('product_request_id')
                    ->references('id')
                    ->on('product_requests')
                    ->onDelete('cascade');
            });
        }

        // Seed default permissions
        $permissions = [
            'product_request.view',
            'product_request.view_all',
            'product_request.create',
            'product_request.edit',
            'product_request.submit',
            'product_request.approve',
            'product_request.reject',
            'product_request.fulfill',
            'product_request.receive',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Clear Spatie Permission cache to avoid 'Permission does not exist' errors
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Assign all permissions to Admin roles
        $adminRoles = Role::where('name', 'like', 'Admin%')->get();
        foreach ($adminRoles as $role) {
            $role->givePermissionTo($permissions);
        }

        // Assign basic permissions to Cashier roles
        $cashierPermissions = [
            'product_request.view',
            'product_request.create',
            'product_request.edit',
            'product_request.submit',
            'product_request.receive',
        ];
        $cashierRoles = Role::where('name', 'like', 'Cashier%')->get();
        foreach ($cashierRoles as $role) {
            $role->givePermissionTo($cashierPermissions);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_request_activities');
        Schema::dropIfExists('product_request_lines');
        Schema::dropIfExists('product_requests');

        $permissions = [
            'product_request.view',
            'product_request.view_all',
            'product_request.create',
            'product_request.edit',
            'product_request.submit',
            'product_request.approve',
            'product_request.reject',
            'product_request.fulfill',
            'product_request.receive',
        ];

        Permission::whereIn('name', $permissions)->delete();
    }
};