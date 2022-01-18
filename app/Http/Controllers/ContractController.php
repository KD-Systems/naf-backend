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
        $contracts = Contract::with('company')->get();

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
            'machine_id' => 'nullable',
            'machine_model_id' => 'nullable',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'notes' => 'nullable'
        ]);

        try {
            $data = $request->all();
            $contract = Contract::create($data);

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
            'machine_id' => 'nullable',
            'machine_model_id' => 'nullable',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'notes' => 'nullable',
        ]);

        try {
            $data = $request->only([
                'machine_id',
                'machine_model_id',
                'start_date',
                'end_date',
                'notes'
            ]);
            $data['status'] = $request->status;
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
