<?php

namespace Shearerline\Exceptions;

use Exception;

class ShearerlineException extends Exception
{
    protected $message = 'Shearerline 扩展包发生异常';

    protected $code = 500;

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $this->getMessage(),
            ], $this->getCode());
        }

        return redirect()->back()->with('error', $this->getMessage());
    }
}
