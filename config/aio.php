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

return array(
    'country' => 'MMR', //config('location.country')
    'location' => [
        'model' => 'App\Location',
        'locations_table' => 'locations',
        'participant_in_location_table' => 'locations_participants',
        'default_per_page' => 30,
        ],
    'participant' => [
        'model' => 'App\Participant',
        'participants_table' => 'participants',
        'participant_in_location_table' => 'locations_participants',
        'roles_table' => 'participant_roles',
        'role_for_participant_table' => 'roles_participants',
        'rolemodel' => 'App\ParticipantRole',
        'default_role' => '',
        ],
    'media' => [
        'media_table' => 'media',
    ],
    
    
    
);