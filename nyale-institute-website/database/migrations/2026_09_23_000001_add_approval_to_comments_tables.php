<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = ['post_comments', 'case_comments'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->boolean('is_approved')->default(false)->after('body');
                $t->foreignId('approved_by')->nullable()->after('is_approved')->constrained('users')->nullOnDelete();
                $t->timestamp('approved_at')->nullable()->after('approved_by');
            });

            // Comments that were already public before moderation existed stay visible.
            DB::table($table)->update(['is_approved' => true, 'approved_at' => now()]);
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropConstrainedForeignId('approved_by');
                $t->dropColumn(['is_approved', 'approved_at']);
            });
        }
    }
};
