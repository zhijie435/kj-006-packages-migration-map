<?php

namespace Shearerline\Exceptions;

class ValidationException extends ShearerlineException
{
    protected $message = '数据验证失败';

    protected $code = 422;

    protected $errorCode = 'VALIDATION_ERROR';

    protected $errors = [];

    public function __construct(array $errors = [], string $message = '')
    {
        $this->errors = $errors;

        $msg = $message ?: $this->message;

        parent::__construct($msg, $this->code, [
            'errors' => $errors,
        ]);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
