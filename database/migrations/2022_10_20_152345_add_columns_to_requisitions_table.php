<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToRequisitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requisitions', function (Blueprint $table) {
            $table->longText('account_details')->nullable()->after('payment_mode');
            $table->string('previous_due')->nullable()->after('reason_of_trouble');
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
        Schema::table('requisitions', function (Blueprint $table) {
            $table->dropColumn('account_details');
            $table->dropColumn('previous_due');
            $table->dropColumn('created_by');

        });
    }
}
