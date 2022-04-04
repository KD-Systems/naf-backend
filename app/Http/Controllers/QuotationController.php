<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuotationCollection;
use App\Http\Resources\QuotationResource;
use App\Models\Quotation;
use Illuminate\Http\Request;

class QuotationController extends Controller
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
            'requisition.machines.machineModel:id,name',
        );
         //Check if request wants all data of the quotations

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
        // return $request->all();
        $request->validate([
            'part_items' => 'required|min:1',
            'company_id' => 'required|exists:companies,id',
        ]);

        try {
            $data = $request->except('part_items');
            $data['pq_number'] = uniqid();

            //Store the quotation data
            $quotation = Quotation::create($data);


            $items = collect($request->part_items);
            // return $items;
            $items = $items->map(function ($dt) {
                return [
                    'part_id' => $dt['part_id'],
                    'quantity' => $dt['quantity'],
                    'unit_value' => $dt['part']['selling_price'],
                    'total_value' => $dt['quantity'] * $dt['part']['selling_price']
                ];
            });

            $quotation->partItems()->createMany($items);

            return message('Quotation created successfully', 200, $quotation);
        } catch (\Throwable $th) {
            return message(
                $th->getMessage(),
                400
            );
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Quotation $quotation)
    {
        $quotation->load([
            'company',
            'requisition.machines:id,machine_model_id',
            'requisition.machines.machineModel:id,name',
            'partItems.part.aliases'
        ]);

        return QuotationResource::make($quotation);
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
