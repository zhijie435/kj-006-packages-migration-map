<?php

namespace Shearerline\Exceptions;

class InvalidStatusTransitionException extends ShearerlineException
{
    protected $message = '状态流转不合法';

    protected $code = 400;

    protected $errorCode = 'INVALID_STATUS_TRANSITION';

    protected $currentStatus;

    protected $targetStatus;

    public function __construct(
        string $currentStatus = '',
        string $targetStatus = '',
        string $message = ''
    ) {
        $this->currentStatus = $currentStatus;
        $this->targetStatus = $targetStatus;

        $msg = $message ?: $this->message;
        if ($currentStatus && $targetStatus) {
            $msg = "无法从状态 [{$currentStatus}] 转换到 [{$targetStatus}]";
        }

        parent::__construct($msg, $this->code, [
            'current_status' => $currentStatus,
            'target_status' => $targetStatus,
        ]);
    }

    public function getCurrentStatus(): string
    {
        return $this->currentStatus;
    }

    public function getTargetStatus(): string
    {
        return $this->targetStatus;
    }
}
