<?php

namespace Shearerline\Exceptions;

class UnauthorizedException extends ShearerlineException
{
    protected $message = '未授权操作';

    protected $code = 403;

    protected $errorCode = 'UNAUTHORIZED';

    protected $ability;

    public function __construct(string $ability = '', string $message = '')
    {
        $this->ability = $ability;

        $msg = $message ?: $this->message;
        if ($ability) {
            $msg = "没有权限执行 [{$ability}] 操作";
        }

        parent::__construct($msg, $this->code, [
            'ability' => $ability,
        ]);
    }

    public function getAbility(): string
    {
        return $this->ability;
    }
}
