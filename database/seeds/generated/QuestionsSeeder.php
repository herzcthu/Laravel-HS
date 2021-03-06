<?php

use Illuminate\Database\Seeder;
use ViKon\Utilities\SeederProgressBarTrait;
use App\Models\Question;

/**
 * Generated by vi-kon/db-exporter
 *
 * @author Kovács Vince<vincekovacs@hotmail.com>
 */
class QuestionsSeeder extends Seeder {
    use SeederProgressBarTrait;

    protected $output;

    protected $structure = array (
  0 => 'id',
  1 => 'section',
  2 => 'report',
  3 => 'qnum',
  4 => 'question',
  5 => 'display',
  6 => 'related_data',
  7 => 'answers',
  8 => 'sameanswer',
  9 => 'answer_view',
  10 => 'related_id',
  11 => 'project_id',
  12 => 'created_at',
  13 => 'updated_at',
);

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
	    DB::table('questions')->delete();

        $this->output = $this->command->getOutput();

        $progress = $this->createProgressBar();
        $this->command->line('<info>Inserting data:</info> questions');
        $progress->start(44);

        $data = include(__DIR__ . '/data/questions_table_data.php');
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
	    Question::create($data);
	}

}
