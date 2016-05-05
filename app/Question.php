<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

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
  public function getLogicAttribute() {
     return json_decode($this->attributes['logic'], true);
  }
  
  public function setLogicAttribute(Array $val) {
      $this->attributes['logic'] = json_encode($val);
  }
  
  public function getUrlhashAttribute() {
     return json_decode($this->attributes['urlhash'], true);
  }
  
  public function setUrlhashAttribute(Array $val) {
      $this->attributes['urlhash'] = json_encode($val);
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
      //$content = str_replace("'", "&apos;", $content); // this is quick fix for single quote. need to remove later
      // ajax route for creating new question
        $question_url = route('ajax.project.question.edit', [$this->project->id, $this->id]);
        // get ajax urlhash for project
        $hash = $this->urlhash;
        if(isset($hash['edit'])){
            $urlhash = $hash['edit'];
        }else{
            $urlhash = '';
        }
        // check if rehash need or not
        if (Hash::needsRehash($urlhash)) {
            // rehash if urlhash column in project table empty or invalid
            $hash['edit'] = Hash::make($question_url);
            // update project table in database with new or correct urlhash
            
            $this->update(['urlhash' => $hash]);
        }
        if(empty($this->slug)){
            $this->update(['slug' => str_slug($this->qnum)]);
        }
        $id = str_slug("$this->qnum -btn");
     return '<a id="'.$id.'" href="#" data-href="'.$question_url.'" class="btn btn-xs btn-primary '.$id.'" data-toggle="modal" data-target="#formTemplate" data-type="edit"><i class="fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit"  ></i></a>';
  }
  
  /**
  * @return string
  */
  public function getLogicButtonAttribute() {// ajax route for creating new question
        $logicurl = route('ajax.project.question.addlogic', [$this->project->id, $this->id]);
        // get ajax urlhash for project
        $hash = $this->urlhash;
        if(isset($hash['logic'])){
            $urlhash = $hash['logic'];
        }else{
            $urlhash = '';
        }
        // check if rehash need or not
        if (Hash::needsRehash($urlhash)) {
            // rehash if urlhash column in project table empty or invalid
            $hash['logic'] = Hash::make($logicurl);
            // update project table in database with new or correct urlhash
            
            $this->update(['urlhash' => $hash]);
        }
        $id = str_slug("$this->qnum -lgbtn");
        $dataid = str_slug("$this->qnum -btn");
     return '<a data-href="'.$logicurl.'" id=" '.$id.'" data-modal="'.$dataid.'" href="#" class="btn btn-xs btn-primary '.$dataid.'" data-toggle="modal" data-target="#logic" data-type="edit"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Add Logic"  ></i></a>';
  }
  
  /**
  * @return string
  */
  public function getDeleteButtonAttribute() {
    return '<a href="'.route('admin.project.questions.destroy', [$this->project->id, $this->id]).'" data-method="delete" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></a>';
  }
  
  public function getActionButtonsAttribute() {
      return $this->getEditButtonAttribute().' '.
              $this->getLogicButtonAttribute().' '.
      $this->getDeleteButtonAttribute();
  }
  
}
