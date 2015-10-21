<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
   /** Table name.
   *
   * @var string
   */
  protected $table = 'questions';
  
  /**
   * The attributes that are not mass assignable.
   *
   * @var array
   */
  protected $guarded = ['id'];
  
  //protected $fillable = array('project_id','answers','qnum','question','related_data','answer_view', 'section', 'sameanswer', 'related_id');
  
  public function project() {
      return $this->belongsTo('App\Project', 'project_id');
  }
  
  public function parent() {
      return $this->belongsTo('App\Question', 'related_id');
  }
  
  public function children() {
      return $this->hasMany('App\Question', 'related_id');
  }
  
  public function qanswers() {
    return $this->hasMany('App\QAnswers', 'qid');
    }
    
    public function ans() {
    return $this->hasMany('App\Answers', 'qid');
    }
  
  public function getAnswersAttribute() {
     return json_decode($this->attributes['answers']);
  }
  
  public function setAnswersAttribute(Array $val) {
      $this->attributes['answers'] = json_encode($val);
  }
  
  public function getDisplayAttribute() {
     return json_decode($this->attributes['display']);
  }
  
  public function setDisplayAttribute(Array $val) {
      $this->attributes['display'] = json_encode($val);
  }
  
  public function getRelatedDataAttribute() {
     return json_decode($this->attributes['related_data']);
  }
  
  public function setRelatedDataAttribute(Array $val) {
      $this->attributes['related_data'] = json_encode($val);
  }  
}
