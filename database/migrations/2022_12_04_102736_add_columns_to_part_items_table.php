<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('part_items', function (Blueprint $table) {
            $table->string('status')->nullable()->after('total_value');
            $table->string('type')->nullable()->after('remarks');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('part_items', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('type');
            
        });
    }
}
