<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\RequisitionCollection;
use Illuminate\Http\Request;
use App\Models\Requisition;

class ClientRequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $requisitions = Requisition::with(
            'company:id,name',
            'machines:id,machine_model_id',
            'machines.model:id,name'
        );

        //Search the quatation
        if ($request->q)
            $requisitions = $requisitions->where(function ($requisitions) use ($request) {
                //Search the data by company name and id
                $requisitions = $requisitions->where('rq_number', 'LIKE', '%' . $request->q . '%');
            });

        //Check if request wants all data of the requisitions
        if ($request->rows == 'all')
            return RequisitionCollection::collection($requisitions->get());

        $requisitions = $requisitions->paginate($request->get('rows', 10));

        return RequisitionCollection::collection($requisitions);
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
    public function show($id)
    {
        //
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
