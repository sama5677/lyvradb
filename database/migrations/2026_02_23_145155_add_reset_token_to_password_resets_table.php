<?php
// database/migrations/2024_01_01_000004_add_reset_token_to_password_resets_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('password_resets', function (Blueprint $table) {
            $table->string('reset_token')->nullable()->after('otp');
        });
    }

    public function down()
    {
        Schema::table('password_resets', function (Blueprint $table) {
            $table->dropColumn('reset_token');
        });
    }
};
