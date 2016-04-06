<?php namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 * @package App
 */
class ParticipantRole extends Model {
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table;

        protected $guarded = array('id');
	/**
	 *
	 */
	public function __construct()
	{
		$this->table = config('aio.participant.roles_table');
	}

	/**
	 * Many-to-Many relations with Users.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function participants()
	{
		return $this->hasMany(config('aio.participant.model'), 'role_id');
	}

        public function pcodes()
	{
		return $this->hasMany('App\PLocation', 'role_id');
	}
	
	/**
	 * @return string
	 */
	public function getEditButtonAttribute() {
		return '<a href="'.route('admin.participants.proles.edit', $this->id).'" class="btn btn-xs btn-primary"><i class="fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit"></i></a>';
	}

	/**
	 * @return string
	 */
	public function getDeleteButtonAttribute() {
		if ($this->id != 1) //Cant delete master admin role
			return '<a href="'.route('admin.participants.proles.destroy', $this->id).'" class="btn btn-xs btn-danger" data-method="delete"><i class="fa fa-times" data-toggle="tooltip" data-placement="top" title="Delete"></i></a>';
		return '';
	}

	/**
	 * @return string
	 */
	public function getActionButtonsAttribute() {
		return $this->getEditButtonAttribute().' '.$this->getDeleteButtonAttribute();
	}

	/**
	 * Before delete all constrained foreign relations
	 *
	 * @return bool
	 */
	public function beforeDelete()
	{
		DB::table(config('aio.participant.participant_role_table'))->where('role_id', $this->id)->delete();
	}
}