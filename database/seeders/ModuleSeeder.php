<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modules = ['Dashboard','Employees','Roles','Companies','Contracts','WareHouses','Machines','Parts','Requisitions','Quatations','Invoices','Delivery Notes','Gate Pass','Sales Report','Stock Report','Settings','Company Parts','Claim Request','Claim Requisition','Transaction Summery','Required Requisition','Foc Summery','Return Parts'];


        foreach ($modules as $module) {
            $module = Module::create(['name' => $module]);
        }
    }
}
