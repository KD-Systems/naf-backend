<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContractCollection;
use App\Http\Resources\ContractResource;
use App\Models\Contract;
use Illuminate\Http\Request;

class ClientContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // if (!user()->details)
        //     return message('You\'re not a company user', 400);

        $contracts = Contract::with('machineModels.model:id,machine_id,name')->where('company_id', user()->details->company_id)
            ->latest()
            ->has('company')
            ->has('machineModels');
        //Search the companies
        if ($request->q)
            $contracts = $contracts->where(function ($contracts) use ($request) {
                //Search name
                $contracts = $contracts->whereHas('company', fn ($q) => $q->where('name', 'LIKE', '%' . $request->q . '%'));
            });
        //Check if request wants all data of the requisitions
        if ($request->rows == 'all')
            return ContractCollection::collection($contracts->get());

        $contracts = $contracts->paginate($request->get('rows', 10));

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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Contract $clientContract)
    {

        $clientContract->load('machineModels.model');
        return ContractResource::make($clientContract);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
