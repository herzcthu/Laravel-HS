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
  
  public function question() {
      return $this->belongsTo('App\Question', 'qid');
  }
  
  public function answers(){
      return $this->hasMany('App\Answers', 'qid', 'qid');
  }
}
