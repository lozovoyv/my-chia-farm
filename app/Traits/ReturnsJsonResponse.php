<?php
/*
 *  This file is part of the MyChiaFarm project.
 *
 *    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Traits;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;

trait ReturnsJsonResponse
{
    /**
     * Make JSON success response with message.
     *
     * @param string|null $message
     * @param mixed ...$arguments
     *
     * @return  JsonResponse
     */
    protected function success(?string $message = null, ...$arguments): JsonResponse
    {
        return response()->json(['message' => sprintf($message, ...$arguments)]);
    }

    /**
     * Make JSON success response with message and data.
     *
     * @param array|Arrayable|null $data
     * @param string|null $message
     * @param mixed ...$arguments
     *
     * @return  JsonResponse
     */
    protected function successWithData(Arrayable|array|null $data, ?string $message = null, ...$arguments): JsonResponse
    {
        return response()->json(['data' => $data, 'message' => sprintf($message, ...$arguments)]);
    }

    /**
     * Make JSON response with data.
     *
     * @param Arrayable|array|null $data
     *
     * @return  JsonResponse
     */
    protected function data(Arrayable|array|null $data): JsonResponse
    {
        return response()->json(['data' => $data]);
    }

    /**
     * Make JSON error response with message.
     *
     * @param string|null $message
     * @param mixed ...$arguments
     *
     * @return  JsonResponse
     */
    protected function error(?string $message = null, ...$arguments): JsonResponse
    {
        return response()->json(['message' => sprintf($message, ...$arguments)], 500);
    }
}
