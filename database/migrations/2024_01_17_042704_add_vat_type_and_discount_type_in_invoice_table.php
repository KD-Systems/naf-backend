<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVatTypeAndDiscountTypeInInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('vat_type', ['percentage', 'fixed'])->default('percentage')->after('vat');
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage')->after('discount');
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
            $table->dropColumn('vat_type');
            $table->dropColumn('discount_type');
        });
    }
}
