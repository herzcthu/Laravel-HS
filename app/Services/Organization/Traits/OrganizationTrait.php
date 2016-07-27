<?php namespace App\Services\Organization\Traits;

/**
 * Class UserHasOrganization
 * @package App\Services\Access\Traits
 */
trait OrganizationTrait {
	

	/**
	 * Checks if the user has a Organization by its name.
	 *
	 * @param string $name Organization name.
	 *
	 * @return bool
	 */
	public function hasOrganization($name)
	{
		foreach ($this->organizations as $organization) {
			if ($organization->name == $name) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks to see if user has array of organizations
	 * All must return true
	 * @param $organizations
	 * @return bool
	 */
	public function hasOrganizations($organizations, $needsAll) {
		//User has to possess all of the organizations specified
		if ($needsAll)
		{
			$hasOrganizations = 0;
			$numOrganizations = count($organizations);

			foreach ($organizations as $organization)
			{
				if ($this->hasOrganization($organization))
					$hasOrganizations++;
			}

			return $numOrganizations == $hasOrganizations;
		}

		//User has to possess one of the organizations specified
		$hasOrganizations = 0;
		foreach ($organizations as $organization) {
			if ($this->hasOrganization($organization))
				$hasOrganizations++;
		}

		return $hasOrganizations > 0;
	}

	
	/**
	 * Alias to eloquent many-to-many relation's attach() method.
	 *
	 * @param mixed $organization
	 *
	 * @return void
	 */
	public function attachOrganization($organization)
	{
		if( is_object($organization))
			$organization = $organization->getKey();

		if( is_array($organization))
			$organization = $organization['id'];

		$this->organizations()->attach($organization);
	}

	/**
	 * Alias to eloquent many-to-many relation's detach() method.
	 *
	 * @param mixed $organization
	 *
	 * @return void
	 */
	public function detachOrganization($organization)
	{
		if (is_object($organization)) {
			$organization = $organization->getKey();
		}

		if (is_array($organization)) {
			$organization = $organization['id'];
		}

		$this->organizations()->detach($organization);
	}

	/**
	 * Attach multiple organizations to a user
	 *
	 * @param mixed $organizations
	 *
	 * @return void
	 */
	public function attachOrganizations($organizations)
	{
		foreach ($organizations as $organization) {
			$this->attachOrganization($organization);
		}
	}

	/**
	 * Detach multiple organizations from a user
	 *
	 * @param mixed $organizations
	 *
	 * @return void
	 */
	public function detachOrganizations($organizations)
	{
		foreach ($organizations as $organization) {
			$this->detachOrganization($organization);
		}
	}

	
}