<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToStaffNegotations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_negotations', function (Blueprint $table) {
            //
            $table->longText('voice_report')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff_negotations', function (Blueprint $table) {
            //
            $table->dropColumn('voice_report');
        });
    }
}
