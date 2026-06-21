<?php

namespace Shearerline\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Shearerline\Exceptions\ShearerlineException;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function success($data = null, string $message = 'success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error(string $message = 'error', int $code = 400, $data = null, string $errorCode = ''): JsonResponse
    {
        return response()->json([
            'success' => false,
            'code' => $code,
            'error_code' => $errorCode,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function paginated($paginator, string $message = 'success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'code' => $code,
            'message' => $message,
            'data' => $paginator->items(),
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ], $code);
    }

    protected function checkPermission(string $ability, $model = null): void
    {
        if ($model) {
            $this->authorize($ability, $model);
        } else {
            $this->authorize($ability);
        }
    }
}
