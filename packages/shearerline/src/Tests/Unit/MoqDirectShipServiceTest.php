<?php

namespace Shearerline\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Shearerline\Models\MoqOrder;
use Shearerline\Models\MoqOrderItem;
use Shearerline\Models\Product;
use Shearerline\Models\Shipment;
use Shearerline\Models\Supplier;
use Shearerline\Services\MoqDirectShipService;
use Shearerline\Tests\TestCase;

class MoqDirectShipServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $moqService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->moqService = $this->app->make(MoqDirectShipService::class);
    }

    protected function createTestSupplier(): Supplier
    {
        return Supplier::create([
            'name' => '测试供应商',
            'code' => 'SUP001',
            'contact_name' => '张三',
            'contact_phone' => '13800138000',
            'is_active' => true,
        ]);
    }

    protected function createTestProduct(Supplier $supplier, int $moq = 10, int $stock = 100): Product
    {
        return Product::create([
            'supplier_id' => $supplier->id,
            'name' => '测试产品',
            'sku' => 'PROD001',
            'price' => 100.00,
            'moq' => $moq,
            'stock' => $stock,
            'unit' => '件',
            'is_active' => true,
        ]);
    }

    protected function createTestOrder(Supplier $supplier, Product $product, int $quantity = 20): MoqOrder
    {
        return $this->moqService->createOrder([
            'supplier_id' => $supplier->id,
            'customer_name' => '测试客户',
            'customer_phone' => '13900139000',
            'customer_address' => '北京市朝阳区测试街道123号',
            'customer_remark' => '测试备注',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'remark' => '产品备注',
                ],
            ],
        ]);
    }

    public function test_partial_ship_order_keeps_processing_status()
    {
        $supplier = $this->createTestSupplier();
        $product = $this->createTestProduct($supplier, 10, 100);
        $order = $this->createTestOrder($supplier, $product, 20);

        $order = $this->moqService->confirmOrder($order->id);
        $order = $this->moqService->processOrder($order->id);

        $shipmentData = [
            'logistics_company' => '顺丰速运',
            'tracking_no' => 'SF1234567890',
            'items' => [
                [
                    'order_item_id' => $order->items[0]->id,
                    'quantity' => 10,
                ],
            ],
        ];

        $shipment = $this->moqService->shipOrder($order->id, $shipmentData);

        $order->refresh();
        $this->assertEquals(MoqOrder::STATUS_PROCESSING, $order->status);
        $this->assertEquals(10, $order->shipped_quantity);
        $this->assertFalse($order->is_fully_shipped);
        $this->assertTrue($order->is_partially_shipped);
    }

    public function test_full_ship_order_updates_to_shipped_status()
    {
        $supplier = $this->createTestSupplier();
        $product = $this->createTestProduct($supplier, 10, 100);
        $order = $this->createTestOrder($supplier, $product, 20);

        $order = $this->moqService->confirmOrder($order->id);
        $order = $this->moqService->processOrder($order->id);

        $shipmentData = [
            'logistics_company' => '顺丰速运',
            'tracking_no' => 'SF1234567890',
            'items' => [
                [
                    'order_item_id' => $order->items[0]->id,
                    'quantity' => 20,
                ],
            ],
        ];

        $shipment = $this->moqService->shipOrder($order->id, $shipmentData);

        $order->refresh();
        $this->assertEquals(MoqOrder::STATUS_SHIPPED, $order->status);
        $this->assertEquals(20, $order->shipped_quantity);
        $this->assertTrue($order->is_fully_shipped);
        $this->assertNotNull($order->shipped_at);
    }

    public function test_cancel_processing_order()
    {
        $supplier = $this->createTestSupplier();
        $product = $this->createTestProduct($supplier, 10, 100);
        $order = $this->createTestOrder($supplier, $product, 20);

        $order = $this->moqService->confirmOrder($order->id);
        $order = $this->moqService->processOrder($order->id);

        $this->assertEquals(MoqOrder::STATUS_PROCESSING, $order->status);

        $product->refresh();
        $initialStock = $product->stock;
        $orderItem = $order->items[0];

        $cancelledOrder = $this->moqService->cancelOrder($order->id, '客户取消');

        $this->assertEquals(MoqOrder::STATUS_CANCELLED, $cancelledOrder->status);
        $this->assertNotNull($cancelledOrder->cancelled_at);
        $this->assertEquals('客户取消', $cancelledOrder->cancelled_reason);

        $product->refresh();
        $this->assertEquals($initialStock + $orderItem->quantity, $product->stock);
    }

    public function test_update_tracking_auto_completes_order_when_all_delivered()
    {
        $supplier = $this->createTestSupplier();
        $product = $this->createTestProduct($supplier, 10, 100);
        $order = $this->createTestOrder($supplier, $product, 20);

        $order = $this->moqService->confirmOrder($order->id);
        $order = $this->moqService->processOrder($order->id);

        $shipmentData = [
            'logistics_company' => '顺丰速运',
            'tracking_no' => 'SF1234567890',
            'items' => [
                [
                    'order_item_id' => $order->items[0]->id,
                    'quantity' => 20,
                ],
            ],
        ];

        $shipment = $this->moqService->shipOrder($order->id, $shipmentData);

        $order->refresh();
        $this->assertEquals(MoqOrder::STATUS_SHIPPED, $order->status);

        $trackingData = [
            'status' => Shipment::STATUS_DELIVERED,
        ];

        $updatedShipment = $this->moqService->updateTracking($shipment->id, $trackingData);

        $order->refresh();
        $this->assertEquals(MoqOrder::STATUS_COMPLETED, $order->status);
        $this->assertNotNull($order->completed_at);

        $shipment->refresh();
        $this->assertEquals(Shipment::STATUS_DELIVERED, $shipment->status);
        $this->assertNotNull($shipment->delivered_at);
    }

    public function test_refund_shipped_order_sets_refunded_status()
    {
        $supplier = $this->createTestSupplier();
        $product = $this->createTestProduct($supplier, 10, 100);
        $order = $this->createTestOrder($supplier, $product, 20);

        $order = $this->moqService->confirmOrder($order->id);
        $order = $this->moqService->processOrder($order->id);

        $shipmentData = [
            'logistics_company' => '顺丰速运',
            'tracking_no' => 'SF1234567890',
            'items' => [
                [
                    'order_item_id' => $order->items[0]->id,
                    'quantity' => 20,
                ],
            ],
        ];

        $shipment = $this->moqService->shipOrder($order->id, $shipmentData);

        $order->refresh();
        $this->assertEquals(MoqOrder::STATUS_SHIPPED, $order->status);

        $refundedOrder = $this->moqService->refundOrder($order->id, '产品质量问题');

        $this->assertEquals(MoqOrder::STATUS_REFUNDED, $refundedOrder->status);
        $this->assertNotNull($refundedOrder->refunded_at);
        $this->assertEquals('产品质量问题', $refundedOrder->refunded_reason);
    }

    public function test_order_operations_return_loaded_relations()
    {
        $supplier = $this->createTestSupplier();
        $product = $this->createTestProduct($supplier, 10, 100);
        $order = $this->createTestOrder($supplier, $product, 20);

        $this->assertTrue($order->relationLoaded('items'));
        $this->assertTrue($order->relationLoaded('supplier'));
        $this->assertTrue($order->relationLoaded('shipments'));

        $confirmedOrder = $this->moqService->confirmOrder($order->id);
        $this->assertTrue($confirmedOrder->relationLoaded('items'));
        $this->assertTrue($confirmedOrder->relationLoaded('supplier'));
        $this->assertTrue($confirmedOrder->relationLoaded('shipments'));

        $processedOrder = $this->moqService->processOrder($confirmedOrder->id);
        $this->assertTrue($processedOrder->relationLoaded('items'));
        $this->assertTrue($processedOrder->relationLoaded('supplier'));
        $this->assertTrue($processedOrder->relationLoaded('shipments'));

        $cancelledOrder = $this->moqService->cancelOrder($processedOrder->id, '测试取消');
        $this->assertTrue($cancelledOrder->relationLoaded('items'));
        $this->assertTrue($cancelledOrder->relationLoaded('supplier'));
        $this->assertTrue($cancelledOrder->relationLoaded('shipments'));

        $order2 = $this->createTestOrder($supplier, $product, 20);
        $order2 = $this->moqService->confirmOrder($order2->id);
        $order2 = $this->moqService->processOrder($order2->id);

        $shipmentData = [
            'logistics_company' => '顺丰速运',
            'tracking_no' => 'SF1234567890',
            'items' => [
                [
                    'order_item_id' => $order2->items[0]->id,
                    'quantity' => 20,
                ],
            ],
        ];

        $shipment = $this->moqService->shipOrder($order2->id, $shipmentData);
        $order2->refresh();

        $completedOrder = $this->moqService->completeOrder($order2->id);
        $this->assertTrue($completedOrder->relationLoaded('items'));
        $this->assertTrue($completedOrder->relationLoaded('supplier'));
        $this->assertTrue($completedOrder->relationLoaded('shipments'));

        $refundedOrder = $this->moqService->refundOrder($completedOrder->id, '测试退款');
        $this->assertTrue($refundedOrder->relationLoaded('items'));
        $this->assertTrue($refundedOrder->relationLoaded('supplier'));
        $this->assertTrue($refundedOrder->relationLoaded('shipments'));
    }
}
