<?php

use App\Location;
use App\Repositories\Backend\Location\LocationContract;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class LocationTableSeeder extends Seeder
{
    
    public function __construct(LocationContract $location) 
    {
            $this->locations = $location;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

		
		DB::table('locations')->truncate();
                
		$location = [
			[
				'name' => 'Myanmar',
                                'pcode' => 'MMR',
				'type' => 'country',
				'lat' => 16.799999,
				'long' => 96.150002,
				'mya_name' => 'မြန်မာ',
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now()
			],
		];
                Location::buildTree($location);
                //$files = [
                //    'locations/Myanmar/Ayeyarwady.csv'
                //    ];
                /**
                $files = 'locations/Myanmar';
                $excel = Excel::batch($files, function($rows, $file) {
                        
                      $this->rows[$rows->title] =  $rows->each(function($row) {});
                        
                });
                $nested_set = $this->locations->merge_excel_import($this->rows);
                $parent = Location::where('pcode', '=', 'MMR')->first();
                //dd($parent->location);
                $imported = $parent->makeTree($nested_set); // => true
                 * 
                 */
                $files = 'locations/Myanmar';
                $excel = Excel::batch($files, function($rows, $file) {
                        $i=5000;
                        Excel::filter('chunk')->load($file)->chunk(5000, function($results) use ($file, &$i)
                            { 
                                    $row = $this->locations->arrayToNestedSet('MMR', $results); 
                                    
                                    echo "$i rows completed\n"; 
                                    return $i += 5000;
                            });
                            
                        echo $file. "completed\n";
                        //$parent = Location::where('pcode', '=', 'MMR')->first();
                        //$imported = $parent->makeTree($nested_set_children);
                });

		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
