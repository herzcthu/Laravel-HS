<?php namespace App\Services\Access\Traits;


/**
 * Class UserHasOrganization
 * @package App\Services\Access\Traits
 */
trait UserHasOrganization {
    
	/**
	 * Many-to-Many relations with Organization.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function organization()
	{
		return $this->belongsTo('App\Organization', 'org_id');
	}
        
}