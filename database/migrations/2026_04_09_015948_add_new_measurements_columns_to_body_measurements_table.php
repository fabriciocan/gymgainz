<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('body_measurements', function (Blueprint $table) {
            $table->renameColumn('bicep_cm', 'bicep_left_cm');
            $table->renameColumn('thigh_cm', 'thigh_left_cm');
        });

        Schema::table('body_measurements', function (Blueprint $table) {
            $table->decimal('bicep_right_cm', 5, 2)->nullable()->after('bicep_left_cm');
            $table->decimal('thigh_right_cm', 5, 2)->nullable()->after('thigh_left_cm');
            $table->decimal('calf_left_cm', 5, 2)->nullable()->after('thigh_right_cm');
            $table->decimal('calf_right_cm', 5, 2)->nullable()->after('calf_left_cm');
            $table->decimal('abdomen_cm', 5, 2)->nullable()->after('waist_cm');
        });
    }

    public function down(): void
    {
        Schema::table('body_measurements', function (Blueprint $table) {
            $table->dropColumn(['bicep_right_cm', 'thigh_right_cm', 'calf_left_cm', 'calf_right_cm', 'abdomen_cm']);
        });

        Schema::table('body_measurements', function (Blueprint $table) {
            $table->renameColumn('bicep_left_cm', 'bicep_cm');
            $table->renameColumn('thigh_left_cm', 'thigh_cm');
        });
    }
};
