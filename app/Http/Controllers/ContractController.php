<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContractCollection;
use App\Http\Resources\ContractResource;
use App\Models\Contract;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Authorize the user
        abort_unless(access('contracts_access'), 403);

        $contracts = Contract::with(
            [
                'company:id,name,logo',
                'machineModels:id,mfg_number,machine_model_id',
                'machineModels.model:id,machine_id,name'
            ]
        )
            ->latest()
            ->has('company')
            ->has('machineModels')
            ->get();

        return ContractCollection::collection($contracts);
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
    public function store(Request $request)
    {
        //Authorize the user
        abort_unless(access('contracts_create'), 403);

        $request->validate([
            'company_id' => 'required|exists:companies,id',
            // 'company_machine_id' => 'required|exists:machine_models,id',
            // 'start_date' => 'required',
            // 'end_date' => 'required',
            'notes' => 'nullable'
        ]);

        try {
            $data = $request->all();
            $data['is_foc'] = $request->is_foc == true;
            $contract = Contract::create($data);

            //Attach the machine models
            $contract->machineModels()->sync($request->company_machine_id);

            return message('Contract created successfully', 200, $contract);
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function show(Contract $contract)
    {
        //Authorize the user
        abort_unless(access('contracts_show'), 403);

        $contract->load('machineModels.model');

        return ContractResource::make($contract);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function edit(Contract $contract)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contract $contract)
    {
        //Authorize the user
        abort_unless(access('contracts_edit'), 403);

        $request->validate([
            // 'start_date' => 'required',
            // 'end_date' => 'required',
            'notes' => 'nullable',
            'is_foc' => 'nullable|boolean'
        ]);

        try {
            $data = $request->only([
                'is_foc',
                'start_date',
                'end_date',
                'notes',
                'status'
            ]);
            $contract->update($data);

            return message('Contract updated successfully', 200, $contract);
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contract $contract)
    {
        //Authorize the user
        abort_unless(access('contracts_delete'), 403);


        if ($contract->delete())
            return message('Contract deleted successfully');

        return message('Something went wrong', 400);
    }
}
