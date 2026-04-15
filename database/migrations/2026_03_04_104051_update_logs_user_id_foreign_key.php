<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $this->dropLogsUserIdForeignKeys();

        Schema::table('logs', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        $this->dropLogsUserIdForeignKeys();

        Schema::table('logs', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
        });
    }

    private function dropLogsUserIdForeignKeys(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $rows = DB::select(
                'SELECT DISTINCT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = DATABASE()
                 AND TABLE_NAME = ?
                 AND COLUMN_NAME = ?
                 AND REFERENCED_TABLE_NAME IS NOT NULL',
                ['logs', 'user_id']
            );

            foreach ($rows as $row) {
                DB::statement('ALTER TABLE `logs` DROP FOREIGN KEY `'.$row->CONSTRAINT_NAME.'`');
            }

            return;
        }

        if ($driver === 'sqlite') {
            $list = DB::select('PRAGMA foreign_key_list(logs)');
            foreach ($list as $fk) {
                $from = $fk->from ?? null;
                if ($from === 'user_id') {
                    Schema::table('logs', function (Blueprint $table) {
                        $table->dropForeign(['user_id']);
                    });
                    break;
                }
            }
        }
    }
};
