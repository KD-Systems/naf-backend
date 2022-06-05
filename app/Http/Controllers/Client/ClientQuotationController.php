<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuotationCollection;
use App\Models\Quotation;
use Illuminate\Http\Request;

class ClientQuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $quotations = Quotation::with(
            'company:id,name',
            'requisition.machines:id,machine_model_id',
            'requisition.machines.model:id,name',
        );
         //Check if request wants all data of the quotations

         //Search the quatation
        if ($request->q)
        $quotations = $quotations->where(function ($quotations) use ($request) {
            //Search the data by company name and id
            $quotations = $quotations->whereHas('company', fn ($q) => $q->where('name', 'LIKE', '%' . $request->q . '%'))->orWhere('pq_number', 'LIKE', '%' . $request->q . '%');
        });

        // //Ordering the collection
        $order = json_decode($request->get('order'));
        if (isset($order->column))
            $quotations = $quotations->where(function ($quotations) use ($order) {

                // Order by name field
                if ($order->column == 'name')
                    $quotations = $quotations->whereHas('user', fn ($q) => $q->orderBy('name', $order->direction));

                // Order by name field
                if (isset($order->column) && $order->column == 'role')
                    $quotations = $quotations->whereHas('user.roles', fn ($q) => $q->orderBy('name', $order->direction));
            });//end

        if ($request->rows == 'all')
            return Quotation::collection($quotations->get());

        $quotations = $quotations->paginate($request->get('rows', 10));

        return QuotationCollection::collection($quotations);
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
