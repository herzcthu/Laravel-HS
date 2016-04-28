<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Stevebauman\EloquentTable\TableTrait;

class PLocation extends Model
{
    use TableTrait;
    
    public $timestamps = false;
    
    protected $table = 'pcode';
    
    protected $guarded = ['id'];
    
    //protected $primaryKey = 'primaryid';

    public static function boot()
    {
        parent::boot();
        
        // Attach event handler, on deleting of the plocation
        PLocation::deleting(function($plocation)
        {   
            // Delete all tricks that belong to this plocation
            foreach ($plocation->participants as $participant) {
                $participant->forceDelete();
            }
        });
    }
   

    /**
    public function location() {
        return $this->belongsTo('App\Location', 'location_id');
    }
    /**
    public function locate() {
        return $this->morphToMany('App\Location', 'locatable', 'locatable');
    }
     * 
     */
    
    public function participants() {
        return $this->belongsToMany('App\Participant', 'participant_pcode', 'pcode_id', 'participant_id');
    }
    
    public function organization() {
        return $this->belongsTo('App\Organization', 'org_id');
    }
    
    public function results() {
        //return $this->hasMany('App\Result', 'station_id', 'primaryid');
        return $this->morphMany('App\Result', 'resultable');
    }
    
    public function answers(){
        return $this->hasManyThrough('App\Answers', 'App\Result', 'resultable_id', 'status_id');
    }
    
    public function proles() {
        return $this->belongsTo('App\ParticipantRole', 'role_id');
    }
    
    public function status() {
        return $this->hasMany('App\Status', 'station_id', 'primary_id');
    }
    
    public function scopeOfState($query, $state)
    {
        return $query->where('state', $state);
    }
    public function scopeOfDistrict($query, $district)
    {
        return $query->where('district', $district);
    }
    public function scopeOfTownship($query, $tsp)
    {
        return $query->where('township', $tsp);
    }
    public function scopeOfVTract($query, $vtract)
    {
        return $query->where('village_tract', $vtract);
    }
    
    public function scopeOfWithAndWhereHas($query, $relation, $constraint){
    return $query->with([$relation => $constraint])
            ->whereHas($relation, $constraint);
    }
    
    public function scopeOfWithOrWhereHas($query, $relation, $constraint){
    return $query->with([$relation => $constraint])
            ->orWhereHas($relation, $constraint);
    }
    
    public function scopeNotWithResults($query){
        return $query->whereNotExists(function($query){
            $this_table = DB::getTablePrefix() . $this->table; 
            $query->select(DB::raw('resultable_id')) ->from('results') ->whereRaw('resultable_id = '.$this_table.'.primaryid'); 
            
        });
    }
    
    public function scopeOrNotWithResults($query){
        return $query->orWhereNotExists(function($query){
            $this_table = DB::getTablePrefix() . $this->table; 
            $query->selectRaw(DB::raw('resultable_id')) ->from('results') ->whereRaw('resultable_id = '.$this_table.'.primaryid'); 
            
        });
    }
    
    
    public function scopeOfOrNotWithResults($query, $project){
        return $query->orWhereNotExists(function($q) use ($project){
            $this_table = DB::getTablePrefix() . $this->table; 
            $q->selectRaw(DB::raw('resultable_id')) ->from('results') ->whereRaw('resultable_id = '.$this_table.'.primaryid')
                    ->where('project_id', $project->id); 
            
        });
    }
    public function scopeOfWhereDoesntHaveResults($query, $project){
        return $query->whereDoesntHave('results', function($q) use ($project){
            //$this_table = DB::getTablePrefix() . $this->table; 
            $q->where('project_id', $project->id); 
        });
    }
    
    public function scopeOfNotWithResults($query, $project){
        return $query->whereNotExists(function($q) use ($project){
            $this_table = DB::getTablePrefix() . $this->table; 
            $q->selectRaw(DB::raw('resultable_id')) ->from('results') ->whereRaw('resultable_id = '.$this_table.'.primaryid')
                    ->where('project_id', $project->id); 
            
        });
    }
    
     public function scopeOfOrWithResults($query,$relation, $project){
        return $query->with($relation)->whereExists(function($query) use ($project) {
            $this_table = DB::getTablePrefix() . $this->table; 
            $query->selectRaw(DB::raw('resultable_id')) ->from('results')
                    ->where('project_id', $project)
                    ->whereRaw('resultable_id = '.$this_table.'.primaryid'); 
            
        });
    }
}
