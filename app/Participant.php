<?php namespace App;

use App\Services\Participant\Traits\ParticipantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Nicolaslopezj\Searchable\SearchableTrait;

/**
* Participant
*/
class Participant extends Model {
 use SoftDeletes, ParticipantTrait, SearchableTrait;
  /**
   * Table name.
   *
   * @var string
   */
  protected $table = 'participants';

   protected $guarded = array('id', 'parent_id');

   protected $scoped = array('org_id');
   
   //protected $with = ['results']; 
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
        
        public function supervisor() {
            return $this->belongsTo('App\Participant', 'parent_id');
        }
        
        public function children(){
            return $this->hasMany('App\Participant', 'parent_id');
        }
        
        public function pcode() {
            return $this->belongsToMany('App\PLocation', 'participant_pcode', 'participant_id', 'pcode_id');
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
