<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QAnswers extends Model
{
    /** Table name.
   *
   * @var string
   */
  protected $table = 'qanswers';
  
  /**
   * The attributes that are not mass assignable.
   *
   * @var array
   */
  protected $guarded = ['id'];
  
  protected $orderColumn = 'value';
  
  public function question() {
      return $this->belongsTo('App\Question', 'qid');
  }
  
  public function answers(){
      return $this->hasMany('App\Answers', 'qid', 'qid');
  }
  
  public function getLogicAttribute() {
     return json_decode($this->attributes['logic'], true);
  }
  
  public function setLogicAttribute($val) {
      if(!empty($val) && is_array($val)) {
        $this->attributes['logic'] = json_encode($val);
      } else {
        $this->attributes['logic'] = '';
      }
  }
}
