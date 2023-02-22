<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraColumnsToInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->integer('sub_total')->default(0)->after('remarks');
            $table->integer('vat')->default(0)->after('sub_total');
            $table->integer('discount')->default(0)->after('vat');
            $table->integer('others')->default(0)->after('discount');
            $table->integer('grand_total')->default(0)->after('others'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('sub_total');
            $table->dropColumn('vat');
            $table->dropColumn('discount');
            $table->dropColumn('others');
            $table->dropColumn('grand_total');
        });

    }
}
