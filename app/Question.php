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
  
  protected $with = ['qanswers']; 
  
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

    public function scopeOfWithAndWhereHas($query, $relation, $constraint){
        return $query->whereHas($relation, $constraint)
                 ->with([$relation => $constraint]);
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
  
  /**
  * @return string
  */
  public function getEditButtonAttribute() {
      $content = json_encode($this, true);
     return '<a href="#" data-href="'.route('ajax.project.question.edit', [$this->project->id, $this->id]).'" class="btn btn-xs btn-primary" data-content=\''.$content.'\' data-toggle="modal" data-target="#formTemplate" data-type="edit"><i class="fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit"  ></i></a>';
  }
  
  /**
  * @return string
  */
  public function getDeleteButtonAttribute() {
    return '<a href="'.route('admin.project.questions.destroy', [$this->project->id, $this->id]).'" data-method="delete" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></a>';
  }
  
  public function getActionButtonsAttribute() {
      return $this->getEditButtonAttribute().
      $this->getDeleteButtonAttribute();
  }
  
}
