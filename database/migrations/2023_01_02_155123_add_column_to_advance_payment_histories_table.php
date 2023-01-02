<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToAdvancePaymentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('advance_payment_histories', function (Blueprint $table) {
            $table->tinyInteger('is_returned')->default(0)->after('transaction_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('advance_payment_histories', function (Blueprint $table) {
            $table->tinyInteger('is_returned')->default(0)->after('transaction_type');
        });
    }
}
