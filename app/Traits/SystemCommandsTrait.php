<?php
/*
 * This file is part of the MyChiaFarm project.
 *
 *   (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Traits;

use App\Classes\Commands\SystemCommands;
use Illuminate\Contracts\Container\BindingResolutionException;

trait SystemCommandsTrait
{
    /** @var SystemCommands System commands class instance cache */
    private SystemCommands $systemCommands;

    /**
     * Get chia commands class.
     *
     * @return  SystemCommands
     * @throws  BindingResolutionException
     */
    protected function systemCommands(): SystemCommands
    {
        if (!isset($this->systemCommands)) {
            $this->systemCommands = app()->make(SystemCommands::class);
        }

        return $this->systemCommands;
    }
}
