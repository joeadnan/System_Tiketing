<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }

            if (!Schema::hasColumn('users', 'department_id')) {
                $table->foreignId('department_id')->nullable()->after('password')->constrained('departments')->nullOnDelete();
            }

            if (!Schema::hasColumn('users', 'location_id')) {
                $table->foreignId('location_id')->nullable()->after('department_id')->constrained('locations')->nullOnDelete();
            }

            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user')->after('location_id')->index();
            }

            if (!Schema::hasColumn('users', 'level')) {
                $table->string('level')->nullable()->after('role')->index();
            }

            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('level')->index();
            }

            if (!Schema::hasColumn('users', 'last_assigned_at')) {
                $table->timestamp('last_assigned_at')->nullable()->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'department_id')) {
                $table->dropConstrainedForeignId('department_id');
            }

            if (Schema::hasColumn('users', 'location_id')) {
                $table->dropConstrainedForeignId('location_id');
            }

            foreach (['phone', 'role', 'level', 'is_active', 'last_assigned_at'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
