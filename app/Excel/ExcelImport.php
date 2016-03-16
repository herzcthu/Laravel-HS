<?php
namespace App\Excel;

use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Files\ExcelFile;

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

/**
 * Description of ExcelImport
 *
 * @author sithu
 */
class ExcelImport extends ExcelFile {
    
    public function getFile()
    {
        // Import a user provided file
        $file = Input::file('file');
        
        if ($file->isValid()) {
            /**
            $ori_ext = $file->getClientOriginalExtension();
            if(!$ori_ext){
                $file_ext = $file->guessExtension();
            }else{
                $file_ext = $ori_ext;
            }
             * 
             */
            $ori_name = $file->getClientOriginalName();
            
                $file->move('/tmp/', $ori_name);
                $file_path = '/tmp/'.$ori_name;
        }
        
                // Return it's location
        return realpath($file_path);
          
        
        
    }
}
