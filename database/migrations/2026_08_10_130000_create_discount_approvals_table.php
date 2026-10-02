<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('location_id');
            $table->unsignedInteger('created_by')->comment('Cashier user ID who requested discount');
            $table->unsignedInteger('approved_by')->nullable()->comment('Admin/Manager user ID who approved/modified/rejected');
            
            $table->enum('status', ['pending', 'approved', 'modified', 'rejected', 'used', 'cancelled', 'expired'])->default('pending')->index();
            
            $table->enum('discount_type', ['fixed', 'percentage'])->default('percentage');
            $table->enum('requested_discount_type', ['fixed', 'percentage'])->default('percentage');
            $table->decimal('requested_discount_amount', 22, 4)->default(0);
            
            $table->enum('approved_discount_type', ['fixed', 'percentage'])->nullable();
            $table->decimal('approved_discount_amount', 22, 4)->nullable();
            
            $table->decimal('original_total', 22, 4)->default(0);
            $table->decimal('resulting_total', 22, 4)->default(0);
            
            $table->longText('cart_snapshot')->nullable()->comment('JSON snapshot of cart items, customer, and pricing');
            $table->text('cashier_notes')->nullable();
            $table->text('admin_notes')->nullable();
            
            $table->string('approval_token', 64)->nullable()->unique()->comment('One-time verification token for POS submission');
            $table->unsignedInteger('transaction_id')->nullable()->comment('Linked transaction ID after sale completion');
            
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'created_by']);
            $table->index('approval_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discount_approvals');
    }
};
