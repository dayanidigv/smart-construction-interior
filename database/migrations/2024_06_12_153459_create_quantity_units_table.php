<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quantity_units', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('e.g., SQ.FT, EACH');
            $table->text('description')->nullable();
            $table->softDeletes(); 
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->string('dimensions')->nullable();
            $table->enum('type', ['Interior', 'Exterior', 'Both'])->default('Interior');
            $table->unsignedBigInteger('unit_id')->index();
            $table->decimal('rate_per', 10, 2);
            $table->softDeletes(); 
            $table->timestamps();

            $table->foreign('unit_id')->references('id')->on('quantity_units')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('parent_id')->index()->nullable();
            $table->enum('type', ['Interior', 'Exterior', 'Both'])->default('Interior');
            $table->softDeletes(); 
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('categories')->cascadeOnDelete();
        });

        Schema::create('customer_category', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->softDeletes(); 
            $table->timestamps();
        });
        
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_category_id');
            $table->text('description')->nullable();
            $table->string('site_status')->nullable();
            $table->enum('type_of_work', ['Interior', 'Exterior', 'Both'])->default('Interior');
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('creator_id')->index()->nullable();
            $table->unsignedBigInteger('customer_id')->index();
            $table->enum('status', ['confirmed', 'cancelled', 'follow-up'])->default('confirmed');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('customer_id')->references('id')->on('customers');
        });
        
    
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->enum('type', ['Interior', 'Exterior', 'Both'])->default('Interior');
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('creator_id')->index()->nullable();
            $table->unsignedBigInteger('enquiry_id')->index()->nullable();
            $table->unsignedBigInteger('customer_id')->index();
            $table->enum('status', ['ongoing', 'follow-up', 'completed', 'cancelled'])->default('ongoing');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->boolean('is_set_approved')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->softDeletes(); 
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('customer_id')->references('id')->on('customers');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('category_id')->index();
            $table->unsignedBigInteger('design_id')->index()->nullable();
            $table->decimal('quantity', 10, 2);
            $table->decimal('rate_per', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->decimal('total', 10, 2);
            $table->decimal('sub_total', 10, 2);
            $table->string('dimension')->nullable();
            $table->string('length')->nullable();
            $table->string('breath')->nullable();
            $table->softDeletes(); 
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreign('category_id')->references('id')->on('categories');
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('invoice_number')->unique();
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->decimal('sub_total_amount', 15, 2)->default(0.00);
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->decimal('advance_pay_amount', 15, 2)->default(0.00);
            $table->decimal('balance_amount', 15, 2)->default(0.00);
            $table->enum('payment_status', ['not confirmed', 'pending', 'paid', 'partially_paid', 'late', 'overdue'])->default('not confirmed');
            $table->enum('payment_method', ['cash', 'credit_card', 'bank_transfer', 'paypal', 'UPI', 'other'])->nullable();
            $table->text('payment_history')->nullable();
            $table->text('terms_and_conditions')->nullable()->default('1. In case of changes in design rate will be changed\n2. Extra works cause extra charges.');
            $table->timestamp('created_date')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('products');
        Schema::dropIfExists('quantity_units');
    }
};
