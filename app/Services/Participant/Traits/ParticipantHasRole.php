<?php namespace App\Services\Participant\Traits;

/**
 * Class UserHasRole
 * @package App\Services\Access\Traits
 */
trait ParticipantHasRole {

	/**
	 * Many-to-Many relations with Role.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function roles()
	{
		return $this->belongsToMany(config('aio.participant.rolemodel'), config('aio.participant.role_for_participant_table'), 'participant_id' , 'role_id');
	}

	

	/**
	 * Checks if the user has a Role by its name.
	 *
	 * @param string $name Role name.
	 *
	 * @return bool
	 */
	public function hasRole($name)
	{
		foreach ($this->roles as $role) {
			if ($role->name == $name) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks to see if user has array of roles
	 * All must return true
	 * @param $roles
	 * @return bool
	 */
	public function hasRoles($roles, $needsAll) {
		//User has to possess all of the roles specified
		if ($needsAll)
		{
			$hasRoles = 0;
			$numRoles = count($roles);

			foreach ($roles as $role)
			{
				if ($this->hasRole($role))
					$hasRoles++;
			}

			return $numRoles == $hasRoles;
		}

		//User has to possess one of the roles specified
		$hasRoles = 0;
		foreach ($roles as $role) {
			if ($this->hasRole($role))
				$hasRoles++;
		}

		return $hasRoles > 0;
	}
	

	/**
	 * Alias to eloquent many-to-many relation's attach() method.
	 *
	 * @param mixed $role
	 *
	 * @return void
	 */
	public function attachRole($role)
	{
		if( is_object($role))
			$role = $role->getKey();

		if( is_array($role))
			$role = $role['id'];

		$this->roles()->attach($role);
	}

	/**
	 * Alias to eloquent many-to-many relation's detach() method.
	 *
	 * @param mixed $role
	 *
	 * @return void
	 */
	public function detachRole($role)
	{
		if (is_object($role)) {
			$role = $role->getKey();
		}

		if (is_array($role)) {
			$role = $role['id'];
		}

		$this->roles()->detach($role);
	}

	/**
	 * Attach multiple roles to a user
	 *
	 * @param mixed $roles
	 *
	 * @return void
	 */
	public function attachRoles($roles)
	{
		foreach ($roles as $role) {
			$this->attachRole($role);
		}
	}

	/**
	 * Detach multiple roles from a user
	 *
	 * @param mixed $roles
	 *
	 * @return void
	 */
	public function detachRoles($roles)
	{
		foreach ($roles as $role) {
			$this->detachRole($role);
		}
	}
}