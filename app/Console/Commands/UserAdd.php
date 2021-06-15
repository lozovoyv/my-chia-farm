<?php
/*
 * This file is part of the MyChiaFarm project.
 *
 *   (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class UserAdd extends Command
{
    /** @var string The name and signature of the console command. */
    protected $signature = 'user:add {user? : The email of the user}';

    /** @var string The console command description. */
    protected $description = 'Add user';

    /**
     * Execute the console command.
     *
     * @return  int
     */
    public function handle(): int
    {
        $this->info('Creating new user...');

        $userEmail = $this->hasArgument('user')
            ? $this->argument('user')
            : $this->ask('Enter email of user to change password');

        if (User::query()->where('email', $userEmail)->count() > 0) {
            $this->error('User with this email already exists');

            return 1;
        }

        $userPassword = $this->secret('Enter password');

        $user = new User;
        $user->setAttribute('email', $userEmail);
        $user->setAttribute('name', $userEmail);
        $user->setAttribute('password', Hash::make($userPassword));
        $user->save();

        $this->info('User created.');

        return 0;
    }
}
