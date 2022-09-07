<?php

namespace App\Http\Controllers;

use App\Http\Resources\PartAliasCollection;
use App\Http\Resources\PartAliasResource;
use App\Http\Resources\PartCollection;
use App\Models\Part;
use App\Models\PartAlias;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PartAliasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\Part $part
     * @return \Illuminate\Http\Response
     */
    public function index(Part $part)
    {
         $aliases = $part->aliases()
            ->with('machine:id,name', 'partHeading:id,name', 'machine','oldPartNumbers')
            ->get();

        return PartAliasCollection::collection($aliases);
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
     * @param \App\Models\Part $part
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Part $part)
    {
        $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'part_heading_id' => 'required|exists:part_headings,id',
            'name' => ['required', 'string', 'max:255', Rule::unique('part_aliases')->where(function ($query) {
                return $query->where('name', request('name'))
                    ->where('machine_id', request('machine_id'));
            })],
            'part_number' => 'required|string|max:255|unique:part_aliases',
            'description' => 'nullable|string',
        ]);

        try {
            $data = $request->only('machine_id', 'part_heading_id', 'name','old_part_number', 'part_number', 'description');
            $alias = $part->aliases()->create($data);
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }

        return message('Part alias created successfully', 200, $alias);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Part $part
     * @param  \App\Models\PartAlias  $partAlias
     * @return \Illuminate\Http\Response
     */
    public function show(Part $part, PartAlias $alias)
    {
        return PartAliasResource::make($alias);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PartAlias  $partAlias
     * @return \Illuminate\Http\Response
     */
    public function edit(PartAlias $partAlias)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\Models\Part $part
     * @param  \App\Models\PartAlias  $alias
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Part $part, PartAlias $alias)
    {
        // return $request;
        $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'part_heading_id' => 'required|exists:part_headings,id',
            'name' => 'required|string|max:255',
            'part_number' => 'required|string|max:255|unique:part_aliases,part_number,' . $alias->id,
            'description' => 'nullable|string',
        ]);

        try {
        //     $machine = $alias->machines()->find($request->machine_model_id);
        // if ($machine && $machine->mfg_number == $request->mfg_number)
        //     return message('Machine already exists with this MFG number', 400);


            $data = $request->only('machine_id', 'part_heading_id', 'name', 'part_number', 'description');
            $alias->update($data);


        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }

        return message('Part alias updated successfully', 200, $alias);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Part $part
     * @param  \App\Models\PartAlias  $partAlias
     * @return \Illuminate\Http\Response
     */
    public function destroy(Part $part, PartAlias $alias)
    {
        if ($part->aliases()->count() == 1)
            return message('You can\'t delete the last alias of a part', 400);

        if ($alias->delete())
            return message('Part alias deleted successfully');

        return message('Something went wrong', 400);
    }
}
