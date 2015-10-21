<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
   /** Table name.
   *
   * @var string
   */
  protected $table = 'projects';
  
  /**
   * The attributes that are not mass assignable.
   *
   * @var array
   */
  protected $guarded = ['id'];
  
  public function organization() {
      return $this->belongsTo('App\Organization', 'org_id');
  }
  
  public function questions() {
      return $this->hasMany('App\Question', 'project_id');
  }
  
  public function results() {
      return $this->hasMany('App\Result', 'project_id');
  }
  
  public function answersQ() {
      return $this->hasManyThrough('App\Answers', 'App\Question', 'project_id', 'qid');
  }

  public function answersR() {
      return $this->hasManyThrough('App\Answers', 'App\Result', 'project_id', 'qid');
  }

  public function parent() {
      return $this->belongsTo('App\Project', 'project_id');
  }
  
  public function children() {
      return $this->hasMany('App\Project', 'project_id');
  }
  
  public function scopeOfWithAndWhereHas($query, $relation, $constraint){
    return $query->whereHas($relation, $constraint)
                 ->with([$relation => $constraint]);
  }
  
  public function getSectionsAttribute() {
     return json_decode($this->attributes['sections']);
  }
  public function setSectionsAttribute($val) {
      $this->attributes['sections'] = json_encode($val);
  }
  
  public function getReportingAttribute() {
      $reporting =  $this->attributes['reporting'];
      $reporting = json_decode($reporting);      
     return $reporting;
  }
  public function setReportingAttribute($val) {
      $this->attributes['reporting'] = json_encode($val);
  }
  /**
  * @return string
  */
  public function getEditButtonAttribute() {
     return '<a href="'.route('admin.project.edit', $this->id).'" class="btn btn-xs btn-primary"><i class="fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit"></i></a>';
  }
  
  /**
  * @return string
  */
  public function getDeleteButtonAttribute() {
    return '<a href="'.route('admin.project.destroy', $this->id).'" data-method="delete" class="btn btn-xs btn-danger"><i class="fa fa-trash" data-toggle="tooltip" data-placement="top" title="Delete"></i></a>';
  }
  
  /**
  * @return string
  */
  public function getExportButtonAttribute() {
    return '<a href="'.route('admin.project.export', $this->id).'" class="btn btn-xs btn-primary"><i class="fa fa-download" data-toggle="tooltip" data-placement="top" title="Export"></i></a>';
  }
  
  /**
  * @return string
  */
  public function getAddQuestionButtonAttribute() {
     return '<a href="'.route('admin.project.questions.create', ['p' => $this->id]).'" class="btn btn-xs btn-primary"><i class="fa fa-question"></i><i class="fa fa-plus" data-toggle="tooltip" data-placement="top" title="Add Question"></i></a>';
  }
  
  /**
  * @return string
  */
  public function getShowQuestionsButtonAttribute() {
     return '<a href="'.route('admin.project.questions.index', ['p' => $this->id]).'" class="btn btn-xs btn-success"><i class="fa fa-database"></i><i class="fa fa-plus" data-toggle="tooltip" data-placement="top" title="Add Answers"></i></a>';
  }
  
  /**
  * @return string
  */
  public function getAddResultsButtonAttribute() {
     return '<a href="'.route('clerk.project.questions.index', ['p' => $this->id]).'" class="btn btn-xs btn-success"><i class="fa fa-database"></i><i class="fa fa-plus" data-toggle="tooltip" data-placement="top" title="Add Answers"></i></a>';
  }
  
  /**
  * @return string
  */
  public function getAddResultsFrontendButtonAttribute() {
     return '<a href="'.route('data.project.results.create', ['p' => $this->id]).'" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Add Results"><i class="fa fa-database"></i><i class="fa fa-plus"></i></a>';
  }
  /**
  * @return string
  */
  public function getViewResultsFrontendButtonAttribute() {
     return '<a href="'.route('data.project.results.index', ['p' => $this->id]).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" data-html="true" title="<h5>Results List</h5><p>click here to add data</p>"><i class="fa fa-database"></i><i class="fa fa-eye"></i></a>';
  }
  
  /**
  * @return string
  */
  public function getEditQuestionsButtonAttribute() {
     return '<a href="'.route('admin.project.questions.editall', ['p' => $this->id]).'" class="btn btn-xs btn-primary"><i class="fa fa-question"></i><i class="fa fa-wrench" data-toggle="tooltip" data-placement="top" title="Edit Questions"></i></a>';
  }
  
  public function getActionButtonsAttribute() {
      return $this->getEditButtonAttribute().' '.
              $this->getDeleteButtonAttribute().' '.
              $this->getShowQuestionsButtonAttribute().' '.
              $this->getAddQuestionButtonAttribute().' '.
              $this->getEditQuestionsButtonAttribute().' '.
              $this->getExportButtonAttribute();
  }
  
  public function getFrontendActionButtonsAttribute(){
      return $this->getViewResultsFrontendButtonAttribute();
  }
  
}
