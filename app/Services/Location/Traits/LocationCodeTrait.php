<?php namespace App\Services\Location\Traits;

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

trait LocationCodeTrait {

    /**
	 * Many-to-Many relations with Participant.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function participants()
	{       // $args are ('model', 'pivot_table_name', 'foreign_key_in_this_model', 'foreign_key_in_other_model')
		return $this->belongsToMany('App\Participant', config('aio.participant.participant_in_location_table'), 'location_id' , 'participant_id')->withTimestamps();
	}
        
        public function location()
        {
                return $this->belongsTo('App\Location', 'location_id');
        }
}
