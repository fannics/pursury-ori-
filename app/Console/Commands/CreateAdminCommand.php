<?php namespace ProjectCarrasco\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use ProjectCarrasco\User;

class CreateAdminCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'create:admin';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Creats Admin Users';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
	    $email = $this->argument('email');
        $pw = $this->argument('password');

        if (User::where('email',$email)->first() == null)
        {
            User::create([
                'email' => $email,
                'password' => bcrypt($pw),
                'name' => 'Admin',
                'gender' => 'male',
                'role' => 'ROLE_ADMIN',
                'active' => '1',
            ]);

        }
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['email', InputArgument::REQUIRED, 'Email Field'],
            ['password',InputArgument::REQUIRED , 'Password Field']
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
		];
	}

}
