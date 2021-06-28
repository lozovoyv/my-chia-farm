<?php
/*
 *  This file is part of the MyChiaFarm project.
 *
 *    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\ReturnsJsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Classes\MCFConfig;

class ConfigController extends Controller
{
    use ReturnsJsonResponse;

    /** @var MCFConfig MaxPlot config */
    protected MCFConfig $config;

    /**
     * ConfigController constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->config = $app->make(MCFConfig::class);
    }

    /**
     * Update the specified config key in storage.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function set(Request $request): JsonResponse
    {
        $key = $request->input('key');
        $value = $request->input('value');

        $this->config->set([$key => $value]);
//        return $this->error('Error %s', 'testing');
        return $this->successWithData($this->config->get($key), 'Config updated');
    }
}
