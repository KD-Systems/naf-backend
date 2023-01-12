<?php

namespace App\Http\Controllers;

use App\Models\PartItem;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\QuotationResource;
use App\Http\Resources\QuotationCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Authorize the user
        abort_unless(access('quotations_access'), 403);

        $quotations = Quotation::with(
            'invoice',
            'company:id,name',
            'requisition.machines:id,machine_model_id',
            'requisition.machines.model:id,name',
        )->latest();
        //Check if request wants all data of the quotations

        $quotations = $quotations->has('partItems');
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
            }); //end

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
        //Authorize the user
        abort_unless(access('quotations_create'), 403);

        $request->validate([
            'part_items' => 'required|min:1',
            'company_id' => 'required|exists:companies,id',
        ]);

        DB::beginTransaction();

        try {
            $data = $request->except('part_items');
            $data['created_by'] = auth()->user()->id;

            //Store the quotation data
            $quotation = Quotation::create($data);
            $items = collect($request->part_items); 
            // return $items;
            $items = $items->map(function ($dt) {
                return [
                    'part_id' => $dt['part_id'],
                    'quantity' => $dt['quantity'],
                    'unit_value' => $dt['unit_value'],
                    'total_value' => $dt['quantity'] * $dt['unit_value'],
                    'status' => $dt['status'],
                    'type' => $dt['type'],

                ];
            });

            $quotation->partItems()->createMany($items);

            DB::commit();
            return message('Quotation created successfully', 200, $quotation);
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
    public function show(Quotation $quotation)
    {
        //Authorize the user
        abort_unless(access('quotations_show'), 403);

        $quotation->load([
            'invoice',
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
        //Authorize the user
        abort_unless(access('quotations_partItems_update'), 403);

        $quatation = Quotation::findOrFail($id);
        $locked = $quatation->locked_at;
        if (!$locked) {
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
            foreach ($items as $item) {
                $pt = PartItem::findOrFail($item['id']);
                $pt->update([
                    'quantity'   => $item['quantity'],
                    'unit_value' => $item['unit_value'],
                    'total_value' => $item['total_value']
                ]);
            }
            $quatation->update([
                'status' => 'pending',
            ]);
            return message('Quotation updated successfully', 200, $quatation);
        } else {
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

    public function Locked(Request $request)
    {
        //Authorize the user
        abort_unless(access('quotations_lock'), 403);

        $quatation = Quotation::findOrFail($request->quotation_id);
        $lock = $quatation->locked_at;
        if (!$lock) {
            $quatation->update([
                'locked_at'   => date('Y-m-d H:i:s'),
            ]);
            return message('Quotation locked successfully', 200, $quatation);
        } else {
            return message('Quotation already locked', 422, $quatation);
        }
    }

    public function approve($id)
    {

        $data = Quotation::findOrFail($id);
        $data->update([
            'status'   => "approved",
        ]);
        return message('Quotation aprroved', 200);
    }

    public function reject($id)
    {

        $data = Quotation::findOrFail($id);
        $data->update([
            'status'   => "rejected",
        ]);
        return message('Quotation rejected', 200);
    }

    /****Quation attachment file functanality**********/
    public function uploadFiles(Request $request, Quotation $quotation)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|mimes:png,jpg,pdf,xlsx,xls,csv,doc,docx,txt,zip'
        ]);
        foreach ($request->file('files') as $file)
            $quotation->addMedia($file)
                ->preservingOriginal()
                ->toMediaCollection('quotation-files');

        return message('Files uploaded successfully');
    }

    public function getFiles(Quotation $quotation)
    {
        $file = $quotation->getMedia('quotation-files')->toArray();

        return ['data' => $file];
    }

    public function deleteFiles(Request $request, Quotation $quotation, Media $media)
    {
        $quotation->deleteMedia($media);
        return message('Files deleted successfully');
    }
}
