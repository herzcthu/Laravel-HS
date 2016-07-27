<?php

use Illuminate\Database\Seeder;
use ViKon\Utilities\SeederProgressBarTrait;
use App\Models\Project;

/**
 * Generated by vi-kon/db-exporter
 *
 * @author Kovács Vince<vincekovacs@hotmail.com>
 */
class ProjectsSeeder extends Seeder {
    use SeederProgressBarTrait;

    protected $output;

    protected $structure = array (
  0 => 'id',
  1 => 'name',
  2 => 'project_id',
  3 => 'org_id',
  4 => 'form_code',
  5 => 'sections',
  6 => 'reporting',
  7 => 'created_at',
  8 => 'updated_at',
);

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
	    DB::table('projects')->delete();

        $this->output = $this->command->getOutput();

        $progress = $this->createProgressBar();
        $this->command->line('<info>Inserting data:</info> projects');
        $progress->start(2);

        $data = include(__DIR__ . '/data/projects_table_data.php');
	    foreach($data as $row) {
	        $this->create($row);
	        $progress->advance();
	    }

        $progress->finish();
        $this->command->getOutput()->writeln('');
	}

	protected function create($values) {
	    $data = [];
        foreach ($this->structure as $i => $key) {
            $data[$key] = $values[$i];
        }
	    Project::create($data);
	}

}