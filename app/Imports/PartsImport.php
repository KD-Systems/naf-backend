<?php

namespace App\Imports;

use Milon\Barcode\DNS1D;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class PartsImport implements ToCollection, WithChunkReading, ShouldQueue
{
    /**
     * Set the chunk size
     *
     * @return int
     */
    public function chunkSize(): int
    {
        return 500;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        $rows->shift();
        $rows->filter();

        try {
            DB::beginTransaction();
            foreach ($rows as $key => $row) {
                //Skip the blank rows
                if (!$row[2])
                    continue;

                /**
                 * Check the machine is exists or not . If it's not exist then insert into database
                 */
                $machine = DB::table('machines')->where('name', $row[2])->value('id');
                if (!$machine)
                    $machine = DB::table('machines')->insertGetId(['name' => $row[2]]);

                /**
                 * Check the Part heading is exists or not . If it's not exist then insert into database
                 */
                $part_heading = DB::table('part_headings')
                    ->where('machine_id', $machine)
                    ->where('name', $row[3])
                    ->value('id');
                if (!$part_heading)
                    $part_heading = DB::table('part_headings')->insertGetId(['name' => $row[3], 'machine_id' => $machine]);

                /**
                 * Check the Part is exists or not . If it's not exist then insert into database
                 */
                $alias = DB::table('part_aliases')
                    ->where('part_heading_id', $part_heading)
                    ->where('name', $row[0])
                    ->first();

                if ($alias) {
                    $part = DB::table('parts')
                        ->where('id', $alias->part_id)
                        ->first();

                    //Update the unique ID
                    DB::table('parts')
                        ->where('id', $part->id)
                        ->update([
                            'unique_id' => str_pad($part->id, 6, 0, STR_PAD_LEFT)
                        ]);
                } else {
                    $part = DB::table('parts')->insertGetId([
                        'unit' => $row[6],
                        'unit_value' => $row[7],
                        'description' => null
                    ]);

                    //Generate unique ID and barcode for the parts
                    $unique_id = str_pad($part, 6, 0, STR_PAD_LEFT);
                    $barcode = new DNS1D;
                    $barcode_data = $barcode->getBarcodePNG($unique_id, 'I25');

                    //Update the unique ID
                    DB::table('parts')
                        ->where('id', $part)
                        ->update([
                            'unique_id' => str_pad($part, 6, 0, STR_PAD_LEFT),
                            'barcode' => $barcode_data
                        ]);

                    $alias = DB::table('part_aliases')->insertGetId([
                        'name' => $row[0],
                        'part_number' => $row[1],
                        'machine_id' => $machine,
                        'part_heading_id' => $part_heading,
                        'part_id' => $part,
                    ]);
                }

                $ware_house = DB::table('warehouses')
                    ->where('name', $row[5])
                    ->value('id');

                // $unique_id = DB::table('parts')->where('unique_id', $row[11])->value('unique_id');
                // if (!$unique_id)
                //     $unique_id = DB::table('parts')->insertGetId(['unique_id' => $row[11]]);

                // // barcode create

                // $barcode = DB::table('parts')->where('unique_id', $unique_id)->value('barcode');


                /**
                 * Check the Part is exists or not . If it's not exist then insert into database
                 */
                // $part_stocks = DB::table('part_stocks')->where('part_id',$part->id)->value('id');

                // if ($part_stocks) {
                //     $ware_house = DB::table('warehouses')->where('name',$row[5])->value('id');
                // }

                if (!$ware_house)
                    $ware_house = DB::table('warehouses')->insertGetId(['name' => $row[5]]);


                // $stocks = DB::table('part_stocks')->where('warehouse_id',$ware_house->id)->value('id');
                DB::table('part_stocks')->insert([
                    'part_id' => $part,
                    'part_heading_id' => $part_heading,
                    'warehouse_id' => $ware_house,
                    'unit_value' => $row[7],
                    'yen_price' => $row[8],
                    'formula_price' => $row[9],
                    'selling_price' => $row[10],
                ]);
            }

            DB::commit();

            return "All good";
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            // something went wrong


        }
    }
}
