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

class UserList extends Command
{
    /** @var string The name and signature of the console command. */
    protected $signature = 'user:list';

    /** @var string The console command description. */
    protected $description = 'List all users';

    /**
     * Execute the console command.
     *
     * @return  int
     */
    public function handle(): int
    {
        $users = User::query()->select(['id', 'email'])->get();

        if ($users->count() === 0) {
            $this->info('No users registered');

            return 0;
        }

        $this->table(
            ['id', 'email'],
            $users->toArray()
        );

        return 0;
    }
}
