<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Services\Access\Traits\UserHasOrganization;
use App\Services\Access\Traits\UserHasRole;
use Nicolaslopezj\Searchable\SearchableTrait;

/**
 * Class User
 * @package App
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword, SoftDeletes, UserHasOrganization, UserHasRole, SearchableTrait;
        

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are not mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['id'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

	/**
	 * For soft deletes
	 *
	 * @var array
	 */
	protected $dates = ['deleted_at'];
        
        /**
        * Searchable rules.
        *
        * @var array
        */
        protected $searchable = [
           'columns' => [
               'name' => 2,
               'email' => 2,
           ],
        ];        

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function providers() {
		return $this->hasMany('App\UserProvider');
	}
        
        /**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function medias() {
		return $this->hasMany('App\Media', 'owner_id');
	}
        
        /**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function results() {
		return $this->hasMany('App\Result', 'user_id');
	}

	/**
	 * Hash the users password
	 *
	 * @param $value
	 */
	public function setPasswordAttribute($value)
	{
		if (\Hash::needsRehash($value))
			$this->attributes['password'] = bcrypt($value);
		else
			$this->attributes['password'] = $value;
	}

	/**
	 * @return mixed
	 */
	public function canChangeEmail() {
		return config('access.users.change_email');
	}

	/**
	 * @return string
	 */
	public function getConfirmedLabelAttribute() {
		if ($this->confirmed == 1)
			return "<label class='label label-success'>Yes</label>";
		return "<label class='label label-danger'>No</label>";
	}
}
