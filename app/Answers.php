<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answers extends Model
{
    /** Table name.
   *
   * @var string
   */
  protected $table = 'answers';
  
  /**
   * The attributes that are not mass assignable.
   *
   * @var array
   */
  protected $guarded = ['id'];
  
  public function question() {
      return $this->belongsTo('App\Question', 'qid');
  }
  
  public function results() {
      return $this->belongsTo('App\Result', 'status_id');
  }
}
