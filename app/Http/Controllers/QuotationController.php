<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuotationCollection;
use App\Http\Resources\QuotationResource;
use App\Models\Quotation;
use App\Models\PartItem;
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
        // return $request->company_id;
        $request->validate([
            'part_items' => 'required|min:1',
            'company_id' => 'required|exists:companies,id',
        ]);

        try {
            $data = $request->except('part_items');

            //Store the quotation data
            $quotation = Quotation::create($data);

            // create unique id
            $id = \Illuminate\Support\Facades\DB::getPdo()->lastInsertId();
            $data = Quotation::findOrFail($id);
            // $str = str_pad($id, 4, '0', STR_PAD_LEFT);  //custom id generate
            $data->update([
                'pq_number'   => 'PQ'.date("Ym").$id,
            ]);

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
            'requisition.machines.model:id,name',
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
        
        $quatation = Quotation::findOrFail($id);
        $locked = $quatation->locked_at;
        if(!$locked){
            $items = collect($request->part_items);
           
            $items = $items->map(function ($dt) {
                return [
                    'id' => $dt['id'],
                    'model_type' => $dt['model_type'],

                    'part_id' => $dt['part_id'],
                    'quantity' => $dt['quantity'],
                    'unit_value' => $dt['unit_value'],
                    'total_value' => $dt['total_value']
                ];
            });
            // return $items;
            foreach($items as $item){
                $pt = PartItem::findOrFail($item['id']);
                $pt->update([
                    'quantity'   => $item['quantity'],
                    'unit_value' => $item['unit_value'],
                    'total_value' => $item['total_value']
                ]);    
            }
            return message('Quotation updated successfully', 200, $quatation);
        }
        else{
            return message('Quotation is already locked ', 422, $quatation);
        }
        
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

    public function Locked(Request $request){
        
        $quatation = Quotation::findOrFail($request->quotation_id);
        $lock = $quatation->locked_at;
        if(!$lock){
            $quatation->update([
                'locked_at'   => date('Y-m-d H:i:s'),
            ]);
            return message('Quotation locked successfully', 200, $quatation);
        }else{
            return message('Quotation already locked', 422, $quatation);
        }
            
           

    }
}
