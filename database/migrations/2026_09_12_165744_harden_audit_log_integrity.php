<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('audit_logs')) {
            return;
        }

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->foreignId('admin_id')
                ->nullable()
                ->after('user_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('actor_role', 50)
                ->nullable()
                ->after('admin_id');

            $table->string('actor_name', 150)
                ->nullable()
                ->after('actor_role');

            $table->string('actor_email', 255)
                ->nullable()
                ->after('actor_name');

            $table->string('scope', 20)
                ->default('system')
                ->after('actor_email');

            $table->index(
                ['admin_id', 'created_at'],
                'audit_logs_admin_created_at_index'
            );

            $table->index(
                ['scope', 'created_at'],
                'audit_logs_scope_created_at_index'
            );

            $table->index(
                ['user_id', 'created_at'],
                'audit_logs_user_created_at_index'
            );
        });

        /*
         * Backfill existing audit records.
         *
         * This intentionally uses database-independent query-builder
         * operations instead of MySQL-specific UPDATE JOIN syntax so
         * fresh MySQL and SQLite databases behave consistently.
         */
        DB::table('audit_logs')
            ->select('id', 'user_id')
            ->orderBy('id')
            ->chunkById(500, function ($auditLogs): void {
                $userIds = $auditLogs
                    ->pluck('user_id')
                    ->filter(fn ($userId) => $userId !== null)
                    ->unique()
                    ->values();

                $users = $userIds->isEmpty()
                    ? collect()
                    : DB::table('users')
                        ->whereIn('id', $userIds)
                        ->get([
                            'id',
                            'admin_id',
                            'role',
                            'name',
                            'email',
                        ])
                        ->keyBy('id');

                foreach ($auditLogs as $auditLog) {
                    $user = $auditLog->user_id !== null
                        ? $users->get($auditLog->user_id)
                        : null;

                    $adminId = null;
                    $actorRole = null;
                    $actorName = null;
                    $actorEmail = null;
                    $scope = 'system';

                    if ($user !== null) {
                        $actorRole = $user->role;
                        $actorName = $user->name;
                        $actorEmail = $user->email;

                        if ($user->role === 'admin') {
                            $adminId = $user->id;
                            $scope = 'tenant';
                        } elseif ($user->role === 'customer') {
                            $adminId = $user->admin_id;
                            $scope = 'tenant';
                        } elseif ($user->role === 'super_admin') {
                            $scope = 'platform';
                        }
                    }

                    DB::table('audit_logs')
                        ->where('id', $auditLog->id)
                        ->update([
                            'admin_id' => $adminId,
                            'actor_role' => $actorRole,
                            'actor_name' => $actorName,
                            'actor_email' => $actorEmail,
                            'scope' => $scope,
                        ]);
                }
            }, 'id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('audit_logs')) {
            return;
        }

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex('audit_logs_user_created_at_index');
            $table->dropIndex('audit_logs_scope_created_at_index');
            $table->dropIndex('audit_logs_admin_created_at_index');

            $table->dropForeign(['admin_id']);

            $table->dropColumn([
                'admin_id',
                'actor_role',
                'actor_name',
                'actor_email',
                'scope',
            ]);
        });
    }
};