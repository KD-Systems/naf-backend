<?php

namespace App\Http\Controllers;

use App\Models\DeliveryNote;
use App\Models\PartItem;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\DeliveryNotesResource;
use App\Http\Resources\DeliveryNotesCollection;
use App\Http\Resources\DeliveryNotesFocPartCollection;
use App\Models\Part;
use Carbon\Carbon;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DeliveryNotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Authorize the user
        abort_unless(access('deliverynotes_access'), 403);

        $delivery_notes = DeliveryNote::with(
            'invoice',
            'invoice.company',
            'invoice.quotation.requisition.machines:id,machine_model_id',
            'invoice.quotation.requisition.machines.model:id,name',
            'partItems',
            'partItems.Part.aliases',

        )->latest();
        //Search the Delivery notes
        if ($request->q)
            $delivery_notes = $delivery_notes->where(function ($delivery_notes) use ($request) {
                //Search the data by company name and id
                $delivery_notes = $delivery_notes->where('dn_number', 'LIKE', '%' . $request->q . '%');
            });
        if ($request->rows == 'all')
            return DeliveryNote::collection($delivery_notes->get());
        $delivery_notes = $delivery_notes->paginate($request->get('rows', 10));


        return DeliveryNotesCollection::collection($delivery_notes);
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
        // return $request;
        $request->validate(
            [
                'invoice' => 'required|array',
                'invoice.id' => 'required|exists:invoices,id',
                'part_items' => 'required|array',
                'part_items.*' => 'required|min:1'
            ],
            [],
            [
                'invoice.id' => 'invoice id'
            ]
        );

        //Authorize the user
        abort_unless(access('deliverynotes_create'), 403);

        DB::beginTransaction();
        try {
            //Store the data

            if (DeliveryNote::where('invoice_id', $request->invoice['id'])->doesntExist()) {
                $deliveryNote = DeliveryNote::create([
                    'created_by' => auth()->user()->id,
                    'invoice_id' =>  $request->invoice['id'],
                    'company_id' =>  $request->invoice['company']['id'],
                ]);

                $items = collect($request->part_items);
                $items = $items->map(function ($dt) {
                    return [
                        'part_id' => $dt['id'],
                        'quantity' => $dt['quantity'],
                        'remarks' => implode("", [
                            'invoice_exists' => $dt['invoice_exists'] ? "" : "not in invoice",
                            'quantity_match' => $dt['quantity_match'] ? "" : "quantity not matched",
                        ]),
                        'unit_value' => $dt['unit_value'],
                        'total_value' => $dt['unit_value'] * $dt['quantity'],
                        'status' => $dt['status'],
                        'type' => $dt['type'],

                    ];
                });

                $deliveryNote->partItems()->createMany($items);

                foreach ($deliveryNote->partItems as $item) {
                    $part =  Part::where('id', $item->part_id)->first();
                    $stocks = $part->stocks()->where('unit_value', '>', 0)->get(); //getting stock
                    $remain = $item->quantity; //taking quantity


                    foreach ($stocks as $partStock) { //looping stock and checking stock unit value
                        if ($partStock->unit_value >= $remain) { //when unit value is greater
                            $partStock->update(['unit_value' => $partStock->unit_value - $remain]);
                            break;
                        } else { //when remain is greater than unit value
                            $remain = $remain  - $partStock->unit_value;
                            $partStock->update(['unit_value' => 0]); //unit value will 0 and run the loop if $remain has value
                        }
                    }
                }

                DB::commit();

                return message('Delivery Note created successfully', 201, $deliveryNote);
            } else {
                return message('Delivery Note already exists', 422);
            }
        } catch (\Throwable $th) {
            DB::rollback();
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
    public function show(DeliveryNote $DeliveryNote)
    {
        //Authorize the user
        abort_unless(access('deliverynotes_show'), 403);

        $DeliveryNote->load(
            'invoice.quotation.requisition.machines:id,machine_model_id',
            'invoice.quotation.requisition.machines.model:id,name',
            'invoice.quotation.partItems.part.aliases',
            'partItems.part.aliases',
            'user'
        );
        return DeliveryNotesResource::make($DeliveryNote);
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
        $deliveryNote = DeliveryNote::find($id)->delete();
        PartItem::where('model_id',$id)->delete();
            return message('Quotation deleted successfully');
    }

    public function deliveredFocPart(Request $request)
    {
        $soldItems = PartItem::join('delivery_notes', function ($join) {
            $join->on('delivery_notes.id', '=', 'part_items.model_id')
                ->where('part_items.model_type', DeliveryNote::class);
        })
            ->join('invoices', 'invoices.id', '=', 'delivery_notes.invoice_id')
            ->join('companies', 'companies.id', '=', 'invoices.company_id')
            ->join('parts', 'parts.id', '=', 'part_items.part_id')
            ->join('part_aliases', 'part_aliases.part_id', '=', 'part_items.part_id')
            ->select('part_items.id','part_items.part_id', 'part_items.created_at', 'part_items.quantity','part_items.type','part_items.status','part_items.remarks', 'part_items.total_value', 'part_aliases.name as part_name', 'part_aliases.part_number', 'companies.name as company_name','companies.id as company_id','delivery_notes.dn_number as dn_number','delivery_notes.id as delivery_id')
            ->whereType('foc');

        // return $soldItems->get();

        if ($request->q)
            $soldItems = $soldItems->where(function ($p) use ($request) {
                //Search the data by aliases name and part number
                $p = $p->orWhere('part_aliases.name', 'LIKE', '%' . $request->q . '%');
                $p = $p->orWhere('part_aliases.part_number', 'LIKE', '%' . $request->q . '%');
                $p = $p->orWhere('companies.name', 'LIKE', '%' . $request->q . '%');
            });

        // Filtering with month
        // $soldItems = $soldItems->when($request->month, function ($q) use ($request) {
        //     $q->whereMonth('part_items.created_at', $request->month);
        // });
        // // Filtering with month
        // $soldItems = $soldItems->when($request->year, function ($q) use ($request) {
        //     $q->whereYear('part_items.created_at', $request->year);
        // });

        // // Filtering with date
        // $soldItems = $soldItems->when($request->start_date_format, function ($q) use ($request) {
        //     $q->whereBetween('part_items.created_at', [$request->start_date_format, Carbon::parse($request->end_date_format)->endOfDay()]);
        // });

        //Filter company
        $soldItems = $soldItems->when($request->company_id, function ($q) use ($request) {
            $q->where('companies.id', $request->company_id);
        });

        //Filter status
        $soldItems = $soldItems->when($request->status, function ($q) use ($request) {
            $q->where('part_items.status', $request->status);
        });


        if ($request->rows == 'all')
            return DeliveryNote::collection($soldItems->get());

        $soldItems = $soldItems->groupBy('part_items.id')
            ->paginate($request->get('rows', 10));

        return DeliveryNotesFocPartCollection::collection($soldItems);
    }

     /****DeliveryNote attachment file functanality**********/
    
     public function uploadFiles(Request $request, DeliveryNote $deliveryNote)
     {
         $request->validate([
             'files' => 'required|array',
             'files.*' => 'required|mimes:png,jpg,pdf,xlsx,xls,csv,doc,docx,txt,zip'
         ]);
         foreach ($request->file('files') as $file)
             $deliveryNote->addMedia($file)
                 ->preservingOriginal()
                 ->toMediaCollection('delivery-note-files');
 
         return message('Files uploaded successfully');
     }
 
     public function getFiles(DeliveryNote $deliveryNote)
     {
         $file = $deliveryNote->getMedia('delivery-note-files')->toArray();
 
         return ['data' => $file];
     }
 
     public function deleteFiles(Request $request, DeliveryNote $deliveryNote, Media $media)
     {
         $deliveryNote->deleteMedia($media);
         return message('Files deleted successfully');
     }
}
