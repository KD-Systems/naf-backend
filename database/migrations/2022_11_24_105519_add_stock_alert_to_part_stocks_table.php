<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStockAlertToPartStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('part_stocks', function (Blueprint $table) {
            $table->integer('stock_alert')->nullable()->after('unit_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('part_stocks', function (Blueprint $table) {
            $table->dropColumn('stock_alert');
        });
    }
}
