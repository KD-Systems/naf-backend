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
        $contracts = Contract::with('company:id,name', 'machine:id,name', 'machineModels:id,name')->get();

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
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'machine_id' => 'required|exists:machines,id',
            'machine_model_id' => 'required|exists:machine_models,id',
            'mfg_number.*' => 'required|string|min:3',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'notes' => 'nullable'
        ], [
            'mfg_number.*.required' => 'Manufacturing number can not be empty'
        ]);

        try {
            $data = $request->all();
            $data['is_foc'] = $request->is_foc == true;
            $contract = Contract::create($data);

            //Attach the machine models
            $contract->machineModels()->sync($request->machine_model_id);

            foreach ($request->mfg_number as $modelId => $mfg) {
                $contract->machinesInfo()->where('machine_model_id', $modelId)->update([
                    'mfg_number' => $mfg
                ]);
            }

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
        $contract->load('machineModels', 'machine');

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
        $request->validate([
            // 'machine_id' => 'required|exists:machines,id',
            // 'machine_model_id' => 'required|exists:machine_models,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'notes' => 'nullable',
            'is_foc' => 'boolean'
        ]);

        try {
            $data = $request->only([
                // 'machine_id',
                // 'machine_model_id',
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
        if ($contract->delete())
            return message('Contract deleted successfully');

        return message('Something went wrong', 400);
    }
}
