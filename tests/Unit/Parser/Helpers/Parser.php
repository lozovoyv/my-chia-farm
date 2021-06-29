<?php
/*
 *  This file is part of the MyChiaFarm project.
 *
 *    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tests\Unit\Parser\Helpers;

use App\Classes\Plotters\BaseParser;
use Exception;

class Parser extends BaseParser
{
    public function _getPlotFileName(): ?string
    {
        try {
            return $this->getPlotFileName();
        }catch (Exception) {
            return null;
        }
    }

}
