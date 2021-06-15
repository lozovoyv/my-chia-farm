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

class UserDelete extends Command
{
    /** @var string The name and signature of the console command. */
    protected $signature = 'user:delete {user? : The email of the user}';

    /** @var string The console command description. */
    protected $description = 'Delete user';

    /**
     * Execute the console command.
     *
     * @return  int
     */
    public function handle(): int
    {
        $userEmail = $this->hasArgument('user')
            ? $this->argument('user')
            : $this->ask('Enter email of user you want to delete');

        if (($user = User::query()->where('email', $userEmail)->first()) === null) {
            $this->error('User you want to delete is not exists');

            return 1;
        }

        $user->delete();

        $this->info('User deleted.');

        return 0;
    }
}
