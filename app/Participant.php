<?php namespace App;

use Baum\Node;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\Participant\Traits\ParticipantTrait;
use Nicolaslopezj\Searchable\SearchableTrait;

/**
* Participant
*/
class Participant extends Node {
 use SoftDeletes, ParticipantTrait, SearchableTrait;
  /**
   * Table name.
   *
   * @var string
   */
  protected $table = 'participants';

  //////////////////////////////////////////////////////////////////////////////

  //
  // Below come the default values for Baum's own Nested Set implementation
  // column names.
  //
  // You may uncomment and modify the following fields at your own will, provided
  // they match *exactly* those provided in the migration.
  //
  // If you don't plan on modifying any of these you can safely remove them.
  //

  // /**
  //  * Column name which stores reference to parent's node.
  //  *
  //  * @var string
  //  */
   protected $parentColumn = 'parent_id';

  // /**
  //  * Column name for the left index.
  //  *
  //  * @var string
  //  */
   protected $leftColumn = 'lft';

  // /**
  //  * Column name for the right index.
  //  *
  //  * @var string
  //  */
   protected $rightColumn = 'rgt';

  // /**
  //  * Column name for the depth field.
  //  *
  //  * @var string
  //  */
   protected $depthColumn = 'depth';

  // /**
  //  * Column to perform the default sorting
  //  *
  //  * @var string
  //  */
   protected $orderColumn = 'name';

  // /**
  // * With Baum, all NestedSet-related fields are guarded from mass-assignment
  // * by default.
  // *
  // * @var array
  // */
   protected $guarded = array('id', 'parent_id', 'lft', 'rgt', 'depth');

   protected $scoped = array('org_id');
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
               'participant_id' => 2,
           ],
        ];
        
        public function pcode() {
            return $this->belongsTo('App\PLocation', 'pcode_id', 'primaryid');
        }
        
        public function role() {
            return $this->belongsTo('App\ParticipantRole', 'role_id');
        }
        
        public function organization() {
            return $this->belongsTo('App\Organization', 'org_id');
        }
        
        public function results() {
            //return $this->hasMany('App\Result', 'participant_id', 'id');
            return $this->morphMany('App\Result', 'resultable');
        }
        
        public function getPhonesAttribute() {
             return json_decode($this->attributes['phones']);
          }

          public function setPhonesAttribute(Array $val) {
              $this->attributes['phones'] = json_encode($val);
          }
          
          public function getLocationsAttribute() {
             return json_decode($this->attributes['phones']);
          }

          public function setLocationsAttribute(Array $val) {
              $this->attributes['phones'] = json_encode($val);
          }
          
    public function scopeNotWithResults($query){
        return $query->whereNotExists(function($query){
            $this_table = DB::getTablePrefix() . $this->table; 
            $query->select(DB::raw('resultable_id')) ->from('results') ->whereRaw('resultable_id = '.$this_table.'.id'); 
            
        });
    }
    
    public function scopeOrNotWithResults($query){
        return $query->orWhereNotExists(function($query){
            $this_table = DB::getTablePrefix() . $this->table; 
            $query->selectRaw(DB::raw('resultable_id')) ->from('results') ->whereRaw('resultable_id = '.$this_table.'.id'); 
            
        });
    }      
    
      /**
  * @return string
  */
  public function getAddButtonAttribute() {
     return '<a href="'.route('admin.participants.create').'" class="btn btn-xs btn-primary"><i class="fa fa-plus" data-toggle="tooltip" data-placement="top" title="Create"></i></a>';
  }  
    /**
  * @return string
  */
  public function getEditButtonAttribute() {
     return '<a href="'.route('admin.participants.edit', $this->id).'" class="btn btn-xs btn-primary"><i class="fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit"></i></a>';
  }

  public function getActionButtonsAttribute(){
      return  $this->getAddButtonAttribute().' '.
              $this->getEditButtonAttribute();
  }
}
