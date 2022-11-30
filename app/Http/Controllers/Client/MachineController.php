<?php

namespace App\Http\Controllers\Client;

use App\Models\Company;

use App\Models\Machine;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\MachineResource;
use App\Http\Resources\CompanyMachineCollection;

class MachineController extends Controller
{
    public function show(Company $company)
    {
        $machines = $company->machines()->with('model.machine')->get();

        return CompanyMachineCollection::collection($machines);
    }

    public function getMachine(Machine $machine)
    {
        //Authorize the users
        return MachineResource::make($machine);
    }
}
