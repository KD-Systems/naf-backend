<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requisitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('machine_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('engineer_id');
            $table->enum('priority', [
                'low',
                'medium',
                'high'
            ])->default('low');
            $table->enum('type', [
                'claim_report',
                'purchase_request',
            ])->default('purchase_request');
            $table->enum('payment_mode', [
                'cash',
                'bank',
                'cheque',
                'card'
            ])->default('cash');
            $table->date('expected_delivery');
            $table->enum('payment_term', [
                'full',
                'half',
                'partial'
            ])->deafult('full');
            $table->enum('payment_partial_mode', [
                'days',
                'weeks',
                'months',
                'years',
            ])->default('months');

            $table->integer('partial_time');
            $table->date('next_payment');
            $table->string('ref_number');
            $table->longText('machine_problems');
            $table->longText('solutions');
            $table->longText('reason_of_trouble');
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
        Schema::dropIfExists('requisitions');
    }
}
