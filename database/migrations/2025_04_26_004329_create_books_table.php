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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('author_id')->default(value: 0);
            $table->string('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->integer('publisher_id')->default(value: 0);
            $table->date('publication_date')->nullable();
            $table->integer('category_id')->default(value: 0);
            $table->string('isbn')->nullable();
            $table->integer('page_count')->default(value: 0);
            $table->integer('user_id')->default(value: 0);
            $table->enum('status', ['available', 'borrowed'])->default('available');
            $table->date('borrow_start_date')->nullable();
            $table->date('borrow_due_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
