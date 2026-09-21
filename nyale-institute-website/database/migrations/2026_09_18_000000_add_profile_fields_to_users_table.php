<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('editor')->after('password'); // admin, editor
            $table->string('status')->default('active')->after('role'); // active, inactive, suspended
            $table->string('phone')->nullable()->after('status');
            $table->string('avatar')->nullable()->after('phone');
            $table->timestamp('last_login_at')->nullable()->after('avatar');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            $table->timestamp('suspended_at')->nullable()->after('last_login_ip');
            $table->string('suspension_reason')->nullable()->after('suspended_at');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'status', 'phone', 'avatar', 'last_login_at',
                'last_login_ip', 'suspended_at', 'suspension_reason', 'deleted_at',
            ]);
        });
    }
};
