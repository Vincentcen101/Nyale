<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('court_cases', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('case_number')->nullable();
            $table->string('court')->nullable();
            $table->string('lawyer')->nullable();
            $table->string('status')->default('ongoing'); // ongoing, concluded, on_appeal
            $table->text('summary')->nullable();
            $table->longText('body')->nullable();
            $table->string('image')->nullable();
            $table->date('case_date')->nullable();
            $table->unsignedInteger('likes_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('case_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('court_case_id')->constrained('court_cases')->onDelete('cascade');
            $table->string('name');
            $table->string('email')->nullable();
            $table->text('body');
            $table->timestamps();

            $table->index('court_case_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_comments');
        Schema::dropIfExists('court_cases');
    }
};
