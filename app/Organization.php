<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
   
   /** Table name.
   *
   * @var string
   */
  protected $table = 'organizations';
  
  protected $guarded = ['id'];
  
  public function users() {
      return $this->hasMany('App\User', 'org_id');
  }
  
  public function projects() {
      return $this->hasMany('App\Project', 'org_id');
  }
  
  public function questions() {
      return $this->hasManyThrough('App\Question', 'App\Project', 'org_id');
  }
  
  public function pcode() {
      return $this->hasMany('App\PLocation', 'org_id');
  }
  
  public function results() {
      return $this->hasManyThrough('App\Result', 'App\PLocation', 'org_id', 'resultable_id');
  }
  
  public function scopeOfWithAndWhereHas($query, $relation, $constraint){
    return $query->whereHas($relation, $constraint)
                 ->with([$relation => $constraint]);
    }
}
