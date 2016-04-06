<?php namespace App\Services\Participant\Traits;

/**
 * Class UserHasLocation
 * @package App\Services\Access\Traits
 */
trait ParticipantInLocation {


	
	public function pcode()
	{       
            return $this->belongsTo('App\PLocation', 'pcode_id');
        }

	

	/**
	 * Checks if the user has a Location by its name.
	 *
	 * @param string $name Location name.
	 *
	 * @return bool
	 */
	public function hasPCode($pcode)
	{
		
            if ($this->pcode->pcode == $pcode) {
                    return true;
            }
            
            return false;
	}

}