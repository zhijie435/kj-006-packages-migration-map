<?php

namespace Shearerline\Exceptions;

class MoqNotMetException extends ShearerlineException
{
    protected $message = '未达到最小起订量';

    protected $code = 400;

    protected $errorCode = 'MOQ_NOT_MET';

    protected $productId;

    protected $productName;

    protected $orderedQuantity;

    protected $moq;

    public function __construct(
        int $productId = 0,
        string $productName = '',
        int $orderedQuantity = 0,
        int $moq = 0,
        string $message = ''
    ) {
        $this->productId = $productId;
        $this->productName = $productName;
        $this->orderedQuantity = $orderedQuantity;
        $this->moq = $moq;

        $msg = $message ?: $this->message;
        if ($productName) {
            $msg = "产品 [{$productName}] 未达到最小起订量，订购 {$orderedQuantity}，MOQ {$moq}";
        }

        parent::__construct($msg, $this->code, [
            'product_id' => $productId,
            'product_name' => $productName,
            'ordered_quantity' => $orderedQuantity,
            'moq' => $moq,
        ]);
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getOrderedQuantity(): int
    {
        return $this->orderedQuantity;
    }

    public function getMoq(): int
    {
        return $this->moq;
    }
}
