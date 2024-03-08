<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AddUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add user to the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user     = User::make();
        $email    = $this->ask("email");
        $name     = $this->ask("name (first and last)");
        $password = $this->secret("password");

        $user->fill([
            'email'    => $email,
            'name'     => $name,
            'password' => bcrypt($password)
        ]);

        $user->save();
    }
}
