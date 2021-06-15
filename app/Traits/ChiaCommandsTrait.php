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

use App\Classes\Commands\ChiaCommands;
use Illuminate\Contracts\Container\BindingResolutionException;

trait ChiaCommandsTrait
{
    /** @var ChiaCommands Chia commands class instance cache */
    private ChiaCommands $chiaCommands;

    /**
     * Get chia commands class.
     *
     * @return  ChiaCommands
     * @throws  BindingResolutionException
     */
    protected function chiaCommands(): ChiaCommands
    {
        if (!isset($this->chiaCommands)) {
            $this->chiaCommands = app()->make(ChiaCommands::class);
        }

        return $this->chiaCommands;
    }
}
