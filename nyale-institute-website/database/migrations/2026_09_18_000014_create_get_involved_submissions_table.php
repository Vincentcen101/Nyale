<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('get_involved_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('organisation')->nullable();
            $table->string('phone')->nullable();
            $table->string('type')->default('general'); // partner, research_collaboration, programme_collaboration, volunteer, support, general
            $table->text('message');
            $table->string('status')->default('new'); // new, in_progress, resolved
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('get_involved_submissions');
    }
};
