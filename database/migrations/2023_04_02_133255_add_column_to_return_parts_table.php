<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToReturnPartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('return_parts', function (Blueprint $table) {
            $table->enum('type', [
                'advance',
                'refund',
            ])->default('advance')->after("grand_total");
            $table->string('remarks')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('return_parts', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('remarks');
        });
    }
}
