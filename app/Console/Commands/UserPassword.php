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

class UserPassword extends Command
{
    /** @var string The name and signature of the console command. */
    protected $signature = 'user:password {user? : The email of the user}';

    /** @var string The console command description. */
    protected $description = 'Change user password';

    /**
     * Execute the console command.
     *
     * @return  int
     */
    public function handle(): int
    {
        $userEmail = $this->hasArgument('user')
            ? $this->argument('user')
            : $this->ask('Enter email of user to change password');

        if (($user = User::query()->where('email', $userEmail)->first()) === null) {
            $this->error('User you want to change password is not exists');

            return 1;
        }

        $userPassword = $this->secret('Enter new password');

        $user->setAttribute('password', Hash::make($userPassword));
        $user->save();

        $this->info('Password changed.');

        return 0;
    }
}
