<?php

namespace App\Console\Commands;

use App\Exceptions\GeneralException;
use App\Repositories\Backend\Participant\ParticipantContract;
use App\Repositories\Backend\PLocation\PLocationContract;
use Illuminate\Console\Command;

class EmsDbImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emsdb:import {--file=} {--org=} {--level=} {--filetype=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import location data and participant data.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PLocationContract $plocation, ParticipantContract $participant)
    {
        parent::__construct();
        $this->plocation = $plocation;
        $this->participant = $participant;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //$file = $this->argument('file');
        
        if(!is_null($this->option('file'))){
            $file = $this->option('file');
        }else{
            throw new GeneralException('Not enough options! Please specify file path.');
        }
        if(!is_null($this->option('org'))){
            $org = $this->option('org');
        }else{
            throw new GeneralException('Not enough options! Please specify org ID.');
        }
        if(!is_null($this->option('level'))){
            $level = $this->option('level');
        }else{
            throw new GeneralException('Not enough options! Please specify import type (participant or pcode)');
        }
        if(!is_null($this->option('filetype'))){
            $type = $this->option('filetype');
        }else{
            throw new GeneralException('Not enough options! Please specify import type (participant or pcode)');
        }
        if($type == 'participant'){
            $this->participant->cliImport($file, $org, $level);
        }
        if($type == 'pcode'){
            $this->plocation->cliImport($file, $org, $level);
        }
    }
}
