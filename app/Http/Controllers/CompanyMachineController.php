<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyMachineCollection;
use App\Models\Company;
use App\Models\CompanyMachine;
use Illuminate\Http\Request;

class CompanyMachineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Company $company)
    {
        $machines = $company->machines()->with('machineModel.machine')->get();

        return CompanyMachineCollection::collection($machines);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Company $company)
    {
        $request->validate([
            'machine_model_id' => 'required|exists:machine_models,id'
        ]);

        //Check if the machine already attached with the company along with MFG
        $machine = $company->machines()->find($request->machine_model_id);
        if ($machine && $machine->mfg_number == $request->mfg_number)
            return message('Machine already exists with this MFG number', 400);

        try {
            $machine = $company->machines()->create([
                'machine_model_id' => $request->machine_model_id,
                'mfg_number' => $request->mfg_number,
                'notes' => $request->notes
            ]);

            return message('Machine added successfully', 200, $machine);
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CompanyMachine  $companyMachine
     * @return \Illuminate\Http\Response
     */
    public function show(CompanyMachine $companyMachine)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CompanyMachine  $companyMachine
     * @return \Illuminate\Http\Response
     */
    public function edit(CompanyMachine $companyMachine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CompanyMachine  $companyMachine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompanyMachine $companyMachine)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CompanyMachine  $companyMachine
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company, CompanyMachine $machine)
    {
        if ($machine->delete())
            return message('Machine removed successfully');

        return message('Something went wrong', 400);
    }
}
