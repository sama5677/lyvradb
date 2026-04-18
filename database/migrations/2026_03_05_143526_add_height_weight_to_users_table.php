<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            
            // نضيف الحقول لو مش موجودة
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->nullable();
            }
            if (!Schema::hasColumn('users', 'age')) {
                $table->integer('age')->nullable();
            }
            if (!Schema::hasColumn('users', 'height')) {
                $table->integer('height')->nullable();
            }
            if (!Schema::hasColumn('users', 'weight')) {
                $table->integer('weight')->nullable();
            }
            if (!Schema::hasColumn('users', 'goal')) {
                $table->string('goal')->nullable();
            }
            if (!Schema::hasColumn('users', 'focus_area')) {
                $table->string('focus_area')->nullable();
            }
            if (!Schema::hasColumn('users', 'activity_level')) {
                $table->string('activity_level')->nullable();
            }
            if (!Schema::hasColumn('users', 'workout_frequency')) {
                $table->integer('workout_frequency')->nullable();
            }
            if (!Schema::hasColumn('users', 'injuries')) {
                $table->string('injuries')->nullable();
            }
            if (!Schema::hasColumn('users', 'meals_per_day')) {
                $table->integer('meals_per_day')->nullable();
            }
            if (!Schema::hasColumn('users', 'eating_pattern')) {
                $table->string('eating_pattern')->nullable();
            }
            if (!Schema::hasColumn('users', 'water_intake')) {
                $table->integer('water_intake')->nullable();
            }
            if (!Schema::hasColumn('users', 'snacks')) {
                $table->string('snacks')->nullable();
            }
            if (!Schema::hasColumn('users', 'onboarding_completed')) {
                $table->boolean('onboarding_completed')->default(false);
            }
            if (!Schema::hasColumn('users', 'profile_completed')) {
                $table->boolean('profile_completed')->default(false);
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'gender', 'age', 'height', 'weight', 'goal', 'focus_area',
                'activity_level', 'workout_frequency', 'injuries', 'meals_per_day',
                'eating_pattern', 'water_intake', 'snacks', 
                'onboarding_completed', 'profile_completed'
            ]);
        });
    }
};