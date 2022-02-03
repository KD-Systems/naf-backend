<?php

namespace App\Imports;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class PartsImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        $rows->shift();
        $request = request();


        try {
            DB::beginTransaction();
            foreach ($rows as $key => $row) {
                /**
                 * Check the machine is exists or not . If it's not exist then insert into database
                 */
                $machine = DB::table('machines')->where('name', $row[2])->value('id');
                if (!$machine)
                    $machine = DB::table('machines')->insertGetId(['name' => $row[2]]);


                /**
                 * Check the Part heading is exists or not . If it's not exist then insert into database
                 */
                $part_heading = DB::table('part_headings')->where('machine_id', $machine)->value('id');
                if (!$part_heading)
                    $part_heading = DB::table('part_headings')->insertGetId(['name' => $row[3], 'machine_id' => $machine]);

                /**
                 * Check the Part is exists or not . If it's not exist then insert into database
                 */
                $part = DB::table('part_aliases')->where('part_heading_id', $part_heading)->where('name', $row[0])->value('id');
                if (!$part) {
                    $parent = $part = DB::table('parts')->insertGetId(['description' => null]);

                    $part = DB::table('part_aliases')->insertGetId(
                        [
                            'name' => $row[0],
                            'part_number' => $row[1],
                            'machine_id' => $machine,
                            'part_heading_id' => $part_heading,
                            'part_id' => $parent,
                        ]
                    );
                }

                $ware_house = DB::table('warehouses')->where('name', $row[5])->value('id');

                /**
                 * Check the Part is exists or not . If it's not exist then insert into database
                 */
                // $part_stocks = DB::table('part_stocks')->where('part_id',$part->id)->value('id');

                // if ($part_stocks) {
                //     $ware_house = DB::table('warehouses')->where('name',$row[5])->value('id');
                // }

                if (!$ware_house) {
                    $ware_house = DB::table('warehouses')->insertGetId(['name' => $row[5]]);
                }


                // $stocks = DB::table('part_stocks')->where('warehouse_id',$ware_house->id)->value('id');

                DB::table('part_stocks')->insertGetId(
                    [
                        'warehouse_id' => $ware_house,
                        'part_id' => $part,
                        'unit' => $row[6],
                        'unit_value' => $row[7],
                        'yen_price' => $row[8],
                        'formula_price' => $row[9],
                        'selling_price' => $row[10],
                    ]

                );

            }
            DB::commit();
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong

            dd($e->getMessage());
        }
    }
}
