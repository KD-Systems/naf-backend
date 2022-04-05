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
            $table->string('invoice_number');
            $table->date('expected_delivery');
            $table->enum('payment_mode', [
                'cash',
                'bank',
                'check',
                'card'
            ])->default('cash')->nullable();
            $table->enum('payment_term', [
                'full',
                'half',
                'partial',
            ])->default('full')->nullable();
            $table->enum('payment_partial_mode', [
                'days',
                'weeks',
                'months',
                'years',
            ])->default('months')->nullable();

            $table->date('next_payment')->nullable();
            $table->date('last_payment')->nullable();
            $table->longText('remarks')->nullable();
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
