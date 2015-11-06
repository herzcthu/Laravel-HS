<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Result extends Model
{
    use \Mpociot\Versionable\VersionableTrait;
    
    /** Table name.
   *
   * @var string
   */
  protected $table = 'results';
  
  /**
   * The attributes that are not mass assignable.
   *
   * @var array
   */
  protected $guarded = ['id'];
  
  //public function pcode() {
  //    return $this->belongsTo('App\PLocation', 'station_id', 'primaryid');
  //}
  
  //public function participant(){
  //    return $this->belongsTo('App\Participant', 'participant_id', 'id');
  //}
  public function resultable() {
      return $this->morphTo();
  }
  public function project() {
      return $this->belongsTo('App\Project', 'project_id');
  } 
  
  public function user() {
      return $this->belongsTo('App\User', 'user_id');
  }
  
  public function answers(){
      return $this->hasMany('App\Answers', 'status_id');
  }
  
  public function getResultsAttribute() {
     return json_decode($this->attributes['results'], true);
  }
  public function setResultsAttribute($val) {
      $this->attributes['results'] = json_encode($val);
  }
  public function scopeOfWithAndWhereHas($query, $relation, $constraint){
    return $query->whereHas($relation, $constraint)
                 ->with([$relation => $constraint]);
  }
  
  public function scopeOfWithPcode($query, $column, $value){
        return $query->whereExists(function($query) use ($column, $value){
            $prefix = DB::getTablePrefix();
            $this_table = $prefix . $this->table; 
            $query->select(DB::raw('primaryid')) ->from($prefix.'pcode') ->whereRaw('primaryid = '.$this_table.'.resultable_id')
                ->where($column,'=',$value);
        });
  }
  
  public function scopeOfWithParticipant($query, $phone){
        
            return $query->whereExists(function($query) use ($phone){
            $prefix = DB::getTablePrefix();
            $this_table = $prefix . $this->table; 
            $query->select(DB::raw('*')) ->from($prefix.'participants') ->whereRaw('pcode_id = '.$this_table.'.resultable_id')
                ->where('phones','like','%'.$phone.'%');
        });
        
  }
    
  public function scopeCreatedAt($query){
      return $query->where(function($query) {
          $prefix = DB::getTablePrefix();
          $this_table = $prefix . $this->table; 
          $query->select(DB::raw('count(*) as resultcount'),DB::raw('ROUND(UNIX_TIMESTAMP(created_at)/(15 * 60)) AS timekey')) 
                  ->from($prefix.'results') 
                  ->groupBy('timekey');
          
      });
  }
}
