<?php

use Illuminate\Database\Seeder;
use ViKon\Utilities\SeederProgressBarTrait;
use App\Models\User;

/**
 * Generated by vi-kon/db-exporter
 *
 * @author Kovács Vince<vincekovacs@hotmail.com>
 */
class UsersSeeder extends Seeder {
    use SeederProgressBarTrait;

    protected $output;

    protected $structure = array (
  0 => 'id',
  1 => 'avatar',
  2 => 'name',
  3 => 'email',
  4 => 'password',
  5 => 'status',
  6 => 'confirmation_code',
  7 => 'confirmed',
  8 => 'org_id',
  9 => 'remember_token',
  10 => 'created_at',
  11 => 'updated_at',
  12 => 'deleted_at',
);

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
	    DB::table('users')->delete();

        $this->output = $this->command->getOutput();

        $progress = $this->createProgressBar();
        $this->command->line('<info>Inserting data:</info> users');
        $progress->start(5);

        $data = include(__DIR__ . '/data/users_table_data.php');
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
	    User::create($data);
	}

}
