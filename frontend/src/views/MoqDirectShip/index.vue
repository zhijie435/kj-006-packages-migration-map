<template>
  <div class="moq-direct-ship">
    <div class="page-header">
      <h2>P1 金路径二：国内小批量 MOQ 直发</h2>
      <p class="subtitle">订单全生命周期管理 · 状态实时同步</p>
    </div>

    <div class="stats-row">
      <div class="stat-card pending" @click="handleStatusFilter('pending')">
        <div class="stat-icon">⏳</div>
        <div class="stat-content">
          <div class="stat-value">{{ statistics.pending_orders || 0 }}</div>
          <div class="stat-label">待确认</div>
        </div>
      </div>
      <div class="stat-card processing" @click="handleStatusFilter('processing')">
        <div class="stat-icon">⚙️</div>
        <div class="stat-content">
          <div class="stat-value">{{ statistics.processing_orders || 0 }}</div>
          <div class="stat-label">处理中</div>
        </div>
      </div>
      <div class="stat-card shipped" @click="handleStatusFilter('shipped')">
        <div class="stat-icon">🚚</div>
        <div class="stat-content">
          <div class="stat-value">{{ statistics.shipped_orders || 0 }}</div>
          <div class="stat-label">已发货</div>
        </div>
      </div>
      <div class="stat-card completed" @click="handleStatusFilter('completed')">
        <div class="stat-icon">✅</div>
        <div class="stat-content">
          <div class="stat-value">{{ statistics.completed_orders || 0 }}</div>
          <div class="stat-label">已完成</div>
        </div>
      </div>
    </div>

    <el-tabs v-model="activeTab" class="main-tabs">
      <el-tab-pane label="订单管理" name="orders">
        <OrderList
          ref="orderListRef"
          :status-filter="statusFilter"
          @view-detail="handleViewDetail"
          @order-updated="loadStatistics"
        />
      </el-tab-pane>
      <el-tab-pane label="供应商管理" name="suppliers">
        <SupplierList />
      </el-tab-pane>
      <el-tab-pane label="产品管理" name="products">
        <ProductList />
      </el-tab-pane>
    </el-tabs>

    <OrderDetail
      v-model:visible="detailVisible"
      :order-id="currentOrderId"
      @order-updated="handleOrderUpdated"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { moqOrderApi } from '@/api/moqDirectShip'
import OrderList from './components/OrderList.vue'
import OrderDetail from './components/OrderDetail.vue'
import SupplierList from './components/SupplierList.vue'
import ProductList from './components/ProductList.vue'

const activeTab = ref('orders')
const statusFilter = ref('')
const detailVisible = ref(false)
const currentOrderId = ref(null)
const statistics = ref({})
const orderListRef = ref(null)

const loadStatistics = async () => {
  try {
    const res = await moqOrderApi.statistics()
    if (res.code === 200) {
      statistics.value = res.data
    }
  } catch (error) {
    console.error('加载统计数据失败:', error)
  }
}

const handleStatusFilter = (status) => {
  activeTab.value = 'orders'
  statusFilter.value = statusFilter.value === status ? '' : status
  if (orderListRef.value) {
    orderListRef.value.loadOrders()
  }
}

const handleViewDetail = (orderId) => {
  currentOrderId.value = orderId
  detailVisible.value = true
}

const handleOrderUpdated = () => {
  loadStatistics()
  if (orderListRef.value) {
    orderListRef.value.loadOrders()
  }
}

onMounted(() => {
  loadStatistics()
})
</script>

<style scoped>
.moq-direct-ship {
  padding: 20px;
  background: #f5f7fa;
  min-height: 100vh;
}

.page-header {
  margin-bottom: 24px;
}

.page-header h2 {
  margin: 0 0 8px 0;
  font-size: 28px;
  font-weight: 600;
  color: #303133;
}

.subtitle {
  margin: 0;
  color: #909399;
  font-size: 14px;
}

.stats-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 24px;
}

.stat-card {
  display: flex;
  align-items: center;
  padding: 24px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.08);
  cursor: pointer;
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 16px 0 rgba(0, 0, 0, 0.12);
}

.stat-card.pending {
  border-left: 4px solid #e6a23c;
}

.stat-card.processing {
  border-left: 4px solid #909399;
}

.stat-card.shipped {
  border-left: 4px solid #67c23a;
}

.stat-card.completed {
  border-left: 4px solid #409eff;
}

.stat-icon {
  font-size: 36px;
  margin-right: 16px;
}

.stat-value {
  font-size: 32px;
  font-weight: 700;
  color: #303133;
  line-height: 1.2;
}

.stat-label {
  font-size: 14px;
  color: #909399;
  margin-top: 4px;
}

.main-tabs {
  background: #fff;
  border-radius: 12px;
  padding: 0 24px;
  box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.08);
}

:deep(.el-tabs__header) {
  margin: 0;
}
</style>
