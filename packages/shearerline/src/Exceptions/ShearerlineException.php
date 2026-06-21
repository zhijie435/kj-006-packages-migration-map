<?php

namespace Shearerline\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ShearerlineException extends Exception
{
    protected $message = 'Shearerline 扩展包发生异常';

    protected $code = 500;

    protected $errorCode = 'SHEARERLINE_ERROR';

    protected $data = [];

    public function __construct(string $message = '', int $code = 0, array $data = [])
    {
        parent::__construct($message ?: $this->message, $code ?: $this->code);
        $this->data = $data;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function render($request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'code' => $this->getCode(),
                'error_code' => $this->getErrorCode(),
                'message' => $this->getMessage(),
                'data' => (object) $this->getData(),
            ], $this->getCode());
        }

        return redirect()->back()->with('error', $this->getMessage())->withInput();
    }
}
