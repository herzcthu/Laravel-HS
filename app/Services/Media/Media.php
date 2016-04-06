<?php
namespace App\Services\Media;

use App\Repositories\Backend\Media\MediaContract;
use Illuminate\Contracts\View\View;

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
 * Description of Media
 *
 * @author sithu
 */
class Media {
    //put your code here
    public function __construct(MediaContract $media) {
            $this->media = $media;
        }
        
    public function getAllMedia() {
        
        if(access()->user()->can('manage_media')){
                $withOwner = false;
        }else{
                $withOwner = true;
        }
            $media = $this->media->getMediasPaginated(config('access.users.default_per_page'), $withOwner);
            
            return $media;
	}
        
    public function compose(View $view) {
        $view->with('allmedia', $this->getAllMedia());
    }
}
