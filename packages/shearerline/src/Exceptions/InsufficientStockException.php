<?php

namespace Shearerline\Exceptions;

class InsufficientStockException extends ShearerlineException
{
    protected $message = '库存不足';

    protected $code = 400;

    protected $errorCode = 'INSUFFICIENT_STOCK';

    protected $productId;

    protected $productName;

    protected $requiredQuantity;

    protected $availableStock;

    public function __construct(
        int $productId = 0,
        string $productName = '',
        int $requiredQuantity = 0,
        int $availableStock = 0,
        string $message = ''
    ) {
        $this->productId = $productId;
        $this->productName = $productName;
        $this->requiredQuantity = $requiredQuantity;
        $this->availableStock = $availableStock;

        $msg = $message ?: $this->message;
        if ($productName) {
            $msg = "产品 [{$productName}] 库存不足，需要 {$requiredQuantity}，当前库存 {$availableStock}";
        }

        parent::__construct($msg, $this->code, [
            'product_id' => $productId,
            'product_name' => $productName,
            'required_quantity' => $requiredQuantity,
            'available_stock' => $availableStock,
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

    public function getRequiredQuantity(): int
    {
        return $this->requiredQuantity;
    }

    public function getAvailableStock(): int
    {
        return $this->availableStock;
    }
}
