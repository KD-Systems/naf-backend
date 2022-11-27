<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToRequiredPartRequisitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('required_part_requisitions', function (Blueprint $table) {
            $table->longText('account_details')->nullable()->after('payment_mode');
            $table->string('created_by')->nullable()->after('remarks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('required_part_requisitions', function (Blueprint $table) {
            $table->dropColumn('account_details');
            $table->dropColumn('created_by');
        });
    }
}
