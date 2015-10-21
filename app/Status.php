<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
     /** Table name.
   *
   * @var string
   */
  protected $table = 'status';
  
  /**
   * The attributes that are not mass assignable.
   *
   * @var array
   */
  protected $guarded = ['id'];
  
  public function pcode() {
      return $this->belongsTo('App\PLocation', 'station_id', 'primaryid');
  }
  
  public function participant() {
      return $this->belongsTo('App\Participant', 'participant_id');
  }
  
  public function project_id() {
      return $this->belongsTo('App\Project', 'project_id');
  }
  
  public function getStatusAttribute() {
     return json_decode($this->attributes['status']);
  }
  public function setStatusAttribute($val) {
      $this->attributes['status'] = json_encode($val);
  }
}
