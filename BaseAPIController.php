<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;
use Response;
use Symfony\Component\Mime\Exception\InvalidArgumentException;

abstract class BaseAPIController extends Controller
{
    /**
     * @param string $message
     * @param JsonResource|array $data
     * @return JsonResource|array
     */
    final public function makeResponse(string $message, $data)
    {
        $additionalData = [
            'success' => true,
            'message' => $message,
        ];

        if ($data instanceof JsonResource) {
            $data->with = $additionalData;
        }
        elseif (is_array($data))
        {
            $data = [
                'data' => $data
            ];

            $data = array_merge($data, $additionalData);
        }
        else
        {
            throw new InvalidArgumentException();
        }

        return $data;
    }

    /**
     * @param string $message
     * @param array  $data
     *
     * @return array
     */
    final public function makeError($message, array $data = [])
    {
        $res = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($data)) {
            $res['data'] = $data;
        }

        return $res;
    }

    /**
     * @param $result
     * @param $message
     * @return \Illuminate\Http\JsonResponse|JsonResource
     */
    final public function sendResponse($result, $message)
    {
        $response = $this->makeResponse($message, $result);
        return is_array($response) ? Response::json($response) : $this->makeResponse($message, $result);
    }

    /**
     * @param $error
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    final public function sendError($error, $code = \Illuminate\Http\Response::HTTP_NOT_FOUND)
    {
        return Response::json($this->makeError($error), $code);
    }

    /**
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    final public function sendSuccess($message)
    {
        return Response::json([
            'success' => true,
            'message' => $message
        ], \Illuminate\Http\Response::HTTP_OK);
    }
}
