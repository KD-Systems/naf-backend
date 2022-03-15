<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('machine_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number');
            $table->date('expected_delivery');
            $table->enum('payment_mode', [
                'cash',
                'bank',
                'check',
                'card'
            ])->default('cash');
            $table->enum('payment_term', [
                'full',
                'half',
                'partial',
            ])->default('full');
            $table->enum('payment_pertial_mode', [
                'days',
                'weeks',
                'months',
                'years',
            ])->default('months');

            $table->date('next_payment');
            $table->date('last_payment');
            $table->longText('remarks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
