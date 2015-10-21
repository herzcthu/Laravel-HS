<?php

/*
 * Copyright (C) 2015 sithu
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App\Services\Aio;


use App\Repositories\Backend\Location\LocationContract;
use Illuminate\Foundation\Application;

/**
 * Description of AioServices
 *
 * @author sithu
 */
class Aio {
    
    /**
	 * Laravel application
	 *
	 * @var Application
	 */
	public $app;

	/**
	 * Create a new confide instance.
	 *
	 * @param Application $app
	 */
	public function __construct($app)
	{
		$this->app = $app;
	}
        
        public function section($column) {
            
            
            if($column == 2){
                $section = 'col-md-6';
            }

            if($column == 3){
                $section = 'col-md-4';
            }

            if($column >= 4){
                $section = 'col-md-3';
            }

            if($column <= 1){
                $section = '';
            }
            
            return $section;
        }
        
        public function addNone(Array $array, $flip = false) {
            $array = array_merge(['None'=> 'none'], $array);
            if($flip){
                $array = array_flip($array);
            }
            return $array;
        }
        
        public function getColNum($count) {
            if(1 <= $count && $count < 5 ) {
                $divider = 12 / $count;
            } else if ( $count <= 6) {
                $divider = 2;
            } else {
                $divider = 1;
            }
            return $divider;
        }
        
        function sortNatural($collection, $sort_key){
            return $collection->sort(function($a, $b) use ($sort_key){
                $lengthA = strlen($a->{$sort_key});
                $lengthB = strlen($b->{$sort_key});
                $valueA = $a->{$sort_key};
                $valueB = $b->{$sort_key};

                if($lengthA == $lengthB){
                    if($valueA == $valueB) return 0;
                    return $valueA > $valueB ? 1 : -1;
                }
                return $lengthA > $lengthB ? 1 : -1;
            });
        }
        
        function createSelectBoxEntryFromArray($array, $value, $key = false){
            foreach($array as $k => $val){
                if(!$key){
                    $kk = $k;
                }else{
                $kk = $val->{$key};
                }
                $list[$kk] = $val->{$value};
            }
            
            return $list;
        }
        
        function createSelectBoxFromArray($array, Array $options = []){
            $attributes = '';
            foreach($options as $key => $option){
                $attributes .= "$key=$option ";
            }
            $selectbox = '';
            foreach($array as $k => $val){
                $selectbox .= "<option value='$k' $attributes>".  ucwords($val) ."</option>";
            }
            return $selectbox;
        }
        
}
