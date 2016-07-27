<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
        use \Stevebauman\EloquentTable\TableTrait;
        /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * The attributes that are not mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['id'];
        
        /**
	 * For soft deletes
	 *
	 * @var array
	 */
	protected $dates = ['deleted_at'];
        
        public function __construct() {
            $this->table = config('aio.media.media_table');
        }
        
        public function owner(){
            return $this->belongsTo(config('auth.model'), 'owner_id');
        }
}
