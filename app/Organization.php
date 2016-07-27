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
    /**
  * @return string
  */
  public function getAddButtonAttribute() {
     return '<a href="'.route('admin.access.organizations.create').'" class="btn btn-xs btn-primary"><i class="fa fa-plus" data-toggle="tooltip" data-placement="top" title="Create"></i></a>';
  }  
    /**
  * @return string
  */
  public function getEditButtonAttribute() {
     return '<a href="'.route('admin.access.organizations.edit', $this->id).'" class="btn btn-xs btn-primary"><i class="fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit"></i></a>';
  }
  
  public function getActionButtonsAttribute(){
      return  $this->getAddButtonAttribute().' '.
              $this->getEditButtonAttribute();
  }
}
