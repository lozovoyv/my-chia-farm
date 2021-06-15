<?php
/*
 * This file is part of the MyChiaFarm project.
 *
 *   (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Classes\MCFConfig;
use App\Classes\Commands\ChiaCommands;
use Illuminate\Contracts\Container\BindingResolutionException;

class ConfigController extends Controller
{
    /** @var MCFConfig MaxPlot config */
    protected MCFConfig $config;

    /**
     * ConfigController constructor.
     *
     * @param MCFConfig $config
     *
     * @return  void
     */
    public function __construct(MCFConfig $config)
    {
        $this->config = $config;
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

        return response()->json([$key => $this->config->get($key)]);
    }

    /**
     * Get keys from chia installation.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     *
     * @throws  BindingResolutionException
     */
    public function keys(Request $request): JsonResponse
    {
        $path = $request->input('path');

        /** @var ChiaCommands $keysGetter */
        $keysGetter = app()->make(ChiaCommands::class);

        $keys = $keysGetter->getKeys($path);

        return response()->json($keys ?? [], ($keys !== null) ? 200 : 500);
    }
}
