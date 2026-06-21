<template>
  <el-drawer
    :model-value="visible"
    title="订单详情"
    direction="rtl"
    size="800px"
    destroy-on-close
    @update:model-value="handleVisibleChange"
  >
    <div v-if="loading" class="loading-wrapper">
      <el-icon class="loading-icon" :size="48"><Loading /></el-icon>
      <p>加载中...</p>
    </div>

    <div v-else-if="orderDetail" class="order-detail">
      <div class="detail-section">
        <div class="section-header">
          <h3>基本信息</h3>
          <el-tag :type="getStatusType(orderDetail.status)" size="large">
            {{ getStatusText(orderDetail.status) }}
          </el-tag>
        </div>
        <el-descriptions :column="2" border>
          <el-descriptions-item label="订单号">
            {{ orderDetail.order_no }}
          </el-descriptions-item>
          <el-descriptions-item label="供应商">
            {{ orderDetail.supplier?.name }}
          </el-descriptions-item>
          <el-descriptions-item label="创建时间">
            {{ formatDate(orderDetail.created_at) }}
          </el-descriptions-item>
          <el-descriptions-item label="订单金额">
            <span class="price">¥{{ orderDetail.total_amount?.toFixed(2) }}</span>
          </el-descriptions-item>
          <el-descriptions-item label="总数量">
            {{ orderDetail.total_quantity }} 件
          </el-descriptions-item>
          <el-descriptions-item label="已发数量">
            {{ orderDetail.shipped_quantity }} 件
            <el-tag v-if="orderDetail.is_partially_shipped" size="small" type="warning">
              部分发货
            </el-tag>
            <el-tag v-else-if="orderDetail.is_fully_shipped" size="small" type="success">
              全部发货
            </el-tag>
          </el-descriptions-item>
          <el-descriptions-item label="支付状态" :span="2">
            <el-tag v-if="orderDetail.paid_amount > 0" type="success">
              已支付 ¥{{ orderDetail.paid_amount?.toFixed(2) }}
            </el-tag>
            <el-tag v-else type="warning">未支付</el-tag>
            <span v-if="orderDetail.paid_at" class="time-note">
              ({{ formatDate(orderDetail.paid_at) }})
            </span>
          </el-descriptions-item>
        </el-descriptions>
      </div>

      <div class="detail-section">
        <div class="section-header">
          <h3>客户信息</h3>
        </div>
        <el-descriptions :column="2" border>
          <el-descriptions-item label="客户姓名">
            {{ orderDetail.customer_name }}
          </el-descriptions-item>
          <el-descriptions-item label="联系电话">
            {{ orderDetail.customer_phone }}
          </el-descriptions-item>
          <el-descriptions-item label="收货地址" :span="2">
            {{ orderDetail.customer_address }}
          </el-descriptions-item>
          <el-descriptions-item label="客户备注" :span="2">
            {{ orderDetail.customer_remark || '-' }}
          </el-descriptions-item>
        </el-descriptions>
      </div>

      <div class="detail-section">
        <div class="section-header">
          <h3>产品明细</h3>
        </div>
        <el-table :data="orderDetail.items" border>
          <el-table-column prop="product_name" label="产品名称" min-width="200" />
          <el-table-column prop="product_sku" label="SKU" width="150" />
          <el-table-column prop="unit_price" label="单价" width="100" align="right">
            <template #default="{ row }">
              ¥{{ row.unit_price?.toFixed(2) }}
            </template>
          </el-table-column>
          <el-table-column prop="quantity" label="订购数量" width="100" align="center" />
          <el-table-column prop="shipped_quantity" label="已发数量" width="100" align="center" />
          <el-table-column label="待发数量" width="100" align="center">
            <template #default="{ row }">
              {{ row.quantity - row.shipped_quantity }}
            </template>
          </el-table-column>
          <el-table-column prop="subtotal" label="小计" width="120" align="right">
            <template #default="{ row }">
              ¥{{ row.subtotal?.toFixed(2) }}
            </template>
          </el-table-column>
        </el-table>
      </div>

      <div v-if="orderDetail.shipments?.length > 0" class="detail-section">
        <div class="section-header">
          <h3>物流信息</h3>
        </div>
        <div v-for="shipment in orderDetail.shipments" :key="shipment.id" class="shipment-card">
          <div class="shipment-header">
            <div class="shipment-info">
              <span class="shipment-no">{{ shipment.shipment_no }}</span>
              <el-tag :type="getShipmentStatusType(shipment.status)">
                {{ getShipmentStatusText(shipment.status) }}
              </el-tag>
            </div>
            <div class="shipment-actions">
              <el-button type="primary" link @click="handleUpdateTracking(shipment)">
                更新物流
              </el-button>
            </div>
          </div>
          <div class="shipment-content">
            <div class="info-row">
              <span class="label">物流公司：</span>
              <span>{{ shipment.logistics_company }}</span>
            </div>
            <div class="info-row">
              <span class="label">物流单号：</span>
              <span class="tracking-no">{{ shipment.tracking_no }}</span>
            </div>
            <div class="info-row">
              <span class="label">发货数量：</span>
              <span>{{ shipment.total_quantity }} 件</span>
            </div>
            <div class="info-row">
              <span class="label">发货时间：</span>
              <span>{{ formatDate(shipment.shipped_at) }}</span>
            </div>
            <div v-if="shipment.delivered_at" class="info-row">
              <span class="label">签收时间：</span>
              <span>{{ formatDate(shipment.delivered_at) }}</span>
            </div>
            <div class="shipment-items">
              <span class="label">发货明细：</span>
              <el-tag
                v-for="item in shipment.items"
                :key="item.order_item_id"
                size="small"
                style="margin-right: 8px"
              >
                {{ item.product_name }} × {{ item.quantity }}
              </el-tag>
            </div>
          </div>
        </div>
      </div>

      <div v-if="orderDetail.cancelled_reason || orderDetail.refunded_reason || orderDetail.internal_remark" class="detail-section">
        <div class="section-header">
          <h3>备注信息</h3>
        </div>
        <el-descriptions :column="1" border>
          <el-descriptions-item v-if="orderDetail.cancelled_reason" label="取消原因">
            {{ orderDetail.cancelled_reason }}
          </el-descriptions-item>
          <el-descriptions-item v-if="orderDetail.refunded_reason" label="退款原因">
            {{ orderDetail.refunded_reason }}
          </el-descriptions-item>
          <el-descriptions-item v-if="orderDetail.internal_remark" label="内部备注">
            {{ orderDetail.internal_remark }}
          </el-descriptions-item>
        </el-descriptions>
      </div>

      <div class="action-bar">
        <el-button @click="handleClose">关闭</el-button>
        <el-button
          v-if="orderDetail.status === 'pending'"
          type="success"
          @click="handleConfirm"
        >
          确认订单
        </el-button>
        <el-button
          v-if="orderDetail.status === 'confirmed'"
          type="primary"
          @click="handleProcess"
        >
          开始处理
        </el-button>
        <el-button
          v-if="orderDetail.status === 'confirmed' || orderDetail.status === 'processing'"
          type="warning"
          @click="handleShip"
        >
          发货
        </el-button>
        <el-button
          v-if="orderDetail.status === 'shipped'"
          type="success"
          @click="handleComplete"
        >
          标记完成
        </el-button>
        <el-button
          v-if="orderDetail.status === 'pending' || orderDetail.status === 'confirmed' || orderDetail.status === 'processing'"
          type="danger"
          @click="handleCancel"
        >
          取消订单
        </el-button>
        <el-button
          v-if="orderDetail.status === 'shipped' || orderDetail.status === 'completed'"
          type="danger"
          @click="handleRefund"
        >
          申请退款
        </el-button>
      </div>
    </div>

    <el-dialog
      v-model="shipDialogVisible"
      title="订单发货"
      width="600px"
      destroy-on-close
    >
      <el-form :model="shipForm" label-width="100px">
        <el-form-item label="物流公司" required>
          <el-input v-model="shipForm.logistics_company" placeholder="请输入物流公司" />
        </el-form-item>
        <el-form-item label="物流单号" required>
          <el-input v-model="shipForm.tracking_no" placeholder="请输入物流单号" />
        </el-form-item>
        <el-form-item label="发货明细" required>
          <div v-for="(item, index) in shipForm.items" :key="index" class="ship-item-row">
            <span class="product-name">{{ item.product_name }}</span>
            <span class="remaining">剩余: {{ item.remaining }}件</span>
            <el-input-number
              v-model="item.quantity"
              :min="1"
              :max="item.remaining"
              @change="validateShipItems"
            />
          </div>
        </el-form-item>
        <el-form-item label="备注">
          <el-input
            v-model="shipForm.remark"
            type="textarea"
            :rows="2"
            placeholder="请输入备注"
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="shipDialogVisible = false">取消</el-button>
        <el-button type="primary" @click="submitShip" :loading="submitting">
          确认发货
        </el-button>
      </template>
    </el-dialog>

    <el-dialog
      v-model="trackingDialogVisible"
      title="更新物流信息"
      width="500px"
      destroy-on-close
    >
      <el-form :model="trackingForm" label-width="100px">
        <el-form-item label="物流公司">
          <el-input v-model="trackingForm.logistics_company" placeholder="请输入物流公司" />
        </el-form-item>
        <el-form-item label="物流单号">
          <el-input v-model="trackingForm.tracking_no" placeholder="请输入物流单号" />
        </el-form-item>
        <el-form-item label="物流状态">
          <el-select v-model="trackingForm.status" style="width: 100%">
            <el-option label="已发货" value="shipped" />
            <el-option label="运输中" value="in_transit" />
            <el-option label="已签收" value="delivered" />
            <el-option label="已退回" value="returned" />
            <el-option label="失败" value="failed" />
          </el-select>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="trackingDialogVisible = false">取消</el-button>
        <el-button type="primary" @click="submitTracking" :loading="submitting">
          确认更新
        </el-button>
      </template>
    </el-dialog>
  </el-drawer>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Loading } from '@element-plus/icons-vue'
import {
  moqOrderApi,
  shipmentApi,
  statusMap,
  shipmentStatusMap,
} from '@/api/moqDirectShip'

const props = defineProps({
  visible: {
    type: Boolean,
    default: false,
  },
  orderId: {
    type: [Number, String],
    default: null,
  },
})

const emit = defineEmits(['update:visible', 'order-updated'])

const loading = ref(false)
const submitting = ref(false)
const orderDetail = ref(null)
const shipDialogVisible = ref(false)
const trackingDialogVisible = ref(false)
const currentShipment = ref(null)

const shipForm = reactive({
  logistics_company: '',
  tracking_no: '',
  remark: '',
  items: [],
})

const trackingForm = reactive({
  logistics_company: '',
  tracking_no: '',
  status: '',
})

const getStatusType = (status) => {
  return statusMap[status]?.type || 'info'
}

const getStatusText = (status) => {
  return statusMap[status]?.text || '未知'
}

const getShipmentStatusType = (status) => {
  return shipmentStatusMap[status]?.type || 'info'
}

const getShipmentStatusText = (status) => {
  return shipmentStatusMap[status]?.text || '未知'
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleString('zh-CN', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const fetchOrderDetail = async () => {
  if (!props.orderId) return

  loading.value = true
  try {
    const res = await moqOrderApi.detail(props.orderId)
    if (res.code === 200) {
      orderDetail.value = res.data
    }
  } catch (error) {
    console.error('加载订单详情失败:', error)
    ElMessage.error('加载订单详情失败')
  } finally {
    loading.value = false
  }
}

const handleVisibleChange = (val) => {
  emit('update:visible', val)
}

const handleClose = () => {
  emit('update:visible', false)
}

const handleConfirm = async () => {
  try {
    await ElMessageBox.confirm('确认该订单吗？', '确认订单', {
      type: 'warning',
    })
    const res = await moqOrderApi.confirm(props.orderId)
    if (res.code === 200) {
      ElMessage.success('订单已确认')
      orderDetail.value = res.data
      emit('order-updated')
    } else {
      ElMessage.error(res.message || '操作失败')
    }
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error(error?.message || '操作失败')
    }
  }
}

const handleProcess = async () => {
  try {
    await ElMessageBox.confirm('开始处理该订单吗？', '开始处理', {
      type: 'warning',
    })
    const res = await moqOrderApi.process(props.orderId)
    if (res.code === 200) {
      ElMessage.success('订单已开始处理')
      orderDetail.value = res.data
      emit('order-updated')
    } else {
      ElMessage.error(res.message || '操作失败')
    }
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error(error?.message || '操作失败')
    }
  }
}

const handleShip = () => {
  if (!orderDetail.value) return

  shipForm.items = orderDetail.value.items
    .filter((item) => item.quantity - item.shipped_quantity > 0)
    .map((item) => ({
      order_item_id: item.id,
      product_id: item.product_id,
      product_name: item.product_name,
      quantity: item.quantity - item.shipped_quantity,
      remaining: item.quantity - item.shipped_quantity,
    }))
  shipForm.logistics_company = ''
  shipForm.tracking_no = ''
  shipForm.remark = ''
  shipDialogVisible.value = true
}

const validateShipItems = () => {
  const total = shipForm.items.reduce((sum, item) => sum + (item.quantity || 0), 0)
  return total > 0
}

const submitShip = async () => {
  if (!shipForm.logistics_company || !shipForm.tracking_no) {
    ElMessage.warning('请填写完整的物流信息')
    return
  }
  if (!validateShipItems()) {
    ElMessage.warning('请填写发货数量')
    return
  }

  submitting.value = true
  try {
    const data = {
      logistics_company: shipForm.logistics_company,
      tracking_no: shipForm.tracking_no,
      remark: shipForm.remark,
      items: shipForm.items.map((item) => ({
        order_item_id: item.order_item_id,
        quantity: item.quantity,
      })),
    }
    const res = await moqOrderApi.ship(props.orderId, data)
    if (res.code === 200) {
      ElMessage.success('发货成功')
      shipDialogVisible.value = false
      if (res.data.order) {
        orderDetail.value = res.data.order
      } else {
        fetchOrderDetail()
      }
      emit('order-updated')
    } else {
      ElMessage.error(res.message || '操作失败')
    }
  } catch (error) {
    ElMessage.error(error?.message || '操作失败')
  } finally {
    submitting.value = false
  }
}

const handleComplete = async () => {
  try {
    await ElMessageBox.confirm('确认该订单已完成吗？', '完成订单', {
      type: 'warning',
    })
    const res = await moqOrderApi.complete(props.orderId)
    if (res.code === 200) {
      ElMessage.success('订单已完成')
      orderDetail.value = res.data
      emit('order-updated')
    } else {
      ElMessage.error(res.message || '操作失败')
    }
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error(error?.message || '操作失败')
    }
  }
}

const handleCancel = async () => {
  try {
    const { value: reason } = await ElMessageBox.prompt('请输入取消原因', '取消订单', {
      type: 'warning',
      inputPlaceholder: '请输入取消原因',
      inputValidator: (value) => {
        if (!value || value.trim().length === 0) {
          return '请输入取消原因'
        }
        return true
      },
    })
    const res = await moqOrderApi.cancel(props.orderId, { reason })
    if (res.code === 200) {
      ElMessage.success('订单已取消')
      orderDetail.value = res.data
      emit('order-updated')
    } else {
      ElMessage.error(res.message || '操作失败')
    }
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error(error?.message || '操作失败')
    }
  }
}

const handleRefund = async () => {
  try {
    const { value: reason } = await ElMessageBox.prompt('请输入退款原因', '申请退款', {
      type: 'warning',
      inputPlaceholder: '请输入退款原因',
      inputValidator: (value) => {
        if (!value || value.trim().length === 0) {
          return '请输入退款原因'
        }
        return true
      },
    })
    const res = await moqOrderApi.refund(props.orderId, { reason })
    if (res.code === 200) {
      ElMessage.success('退款申请已提交')
      orderDetail.value = res.data
      emit('order-updated')
    } else {
      ElMessage.error(res.message || '操作失败')
    }
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error(error?.message || '操作失败')
    }
  }
}

const handleUpdateTracking = (shipment) => {
  currentShipment.value = shipment
  trackingForm.logistics_company = shipment.logistics_company
  trackingForm.tracking_no = shipment.tracking_no
  trackingForm.status = shipment.status
  trackingDialogVisible.value = true
}

const submitTracking = async () => {
  if (!currentShipment.value) return

  submitting.value = true
  try {
    const res = await shipmentApi.updateTracking(currentShipment.value.id, trackingForm)
    if (res.code === 200) {
      ElMessage.success('物流信息更新成功')
      trackingDialogVisible.value = false
      fetchOrderDetail()
      emit('order-updated')
    } else {
      ElMessage.error(res.message || '操作失败')
    }
  } catch (error) {
    ElMessage.error(error?.message || '操作失败')
  } finally {
    submitting.value = false
  }
}

watch(
  () => props.visible,
  (val) => {
    if (val && props.orderId) {
      fetchOrderDetail()
    }
  }
)

watch(
  () => props.orderId,
  () => {
    if (props.visible && props.orderId) {
      fetchOrderDetail()
    }
  }
)
</script>

<style scoped>
.order-detail {
  padding: 0 8px;
}

.loading-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 0;
  color: #909399;
}

.loading-icon {
  animation: rotate 1.5s linear infinite;
  color: #409eff;
}

@keyframes rotate {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.detail-section {
  margin-bottom: 24px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.section-header h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: #303133;
}

.price {
  color: #f56c6c;
  font-weight: 600;
  font-size: 18px;
}

.time-note {
  color: #909399;
  font-size: 12px;
  margin-left: 8px;
}

.shipment-card {
  border: 1px solid #ebeef5;
  border-radius: 8px;
  margin-bottom: 12px;
  overflow: hidden;
}

.shipment-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  background: #f5f7fa;
}

.shipment-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.shipment-no {
  font-weight: 600;
  color: #303133;
}

.shipment-content {
  padding: 16px;
}

.info-row {
  display: flex;
  margin-bottom: 8px;
}

.info-row .label {
  color: #909399;
  width: 80px;
  flex-shrink: 0;
}

.tracking-no {
  font-family: 'Monaco', 'Consolas', monospace;
  color: #409eff;
}

.shipment-items {
  display: flex;
  align-items: flex-start;
  flex-wrap: wrap;
  margin-top: 8px;
}

.shipment-items .label {
  color: #909399;
  width: 80px;
  flex-shrink: 0;
}

.ship-item-row {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 8px 0;
  border-bottom: 1px solid #ebeef5;
}

.ship-item-row:last-child {
  border-bottom: none;
}

.product-name {
  flex: 1;
}

.remaining {
  color: #909399;
  font-size: 12px;
}

.action-bar {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 16px;
  border-top: 1px solid #ebeef5;
  position: sticky;
  bottom: 0;
  background: #fff;
}
</style>
