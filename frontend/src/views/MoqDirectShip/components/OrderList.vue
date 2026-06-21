<template>
  <div class="order-list">
    <div class="filter-bar">
      <el-input
        v-model="filters.keyword"
        placeholder="搜索订单号、客户名称、手机号"
        clearable
        class="filter-input"
        @keyup.enter="loadOrders"
        @clear="loadOrders"
      >
        <template #prefix>
          <el-icon><Search /></el-icon>
        </template>
      </el-input>

      <el-select
        v-model="filters.supplier_id"
        placeholder="选择供应商"
        clearable
        class="filter-select"
        @change="loadOrders"
        @clear="loadOrders"
      >
        <el-option
          v-for="supplier in suppliers"
          :key="supplier.id"
          :label="supplier.name"
          :value="supplier.id"
        />
      </el-select>

      <el-button type="primary" @click="handleCreate">
        <el-icon><Plus /></el-icon>
        新建订单
      </el-button>
    </div>

    <div class="status-tabs">
      <el-tag
        v-for="(item, key) in statusTabs"
        :key="key"
        :type="statusFilter === key ? 'primary' : 'info'"
        :effect="statusFilter === key ? 'dark' : 'plain'"
        class="status-tag"
        @click="handleStatusTab(key)"
      >
        {{ item.text }}
        <span v-if="item.count !== undefined" class="count">({{ item.count }})</span>
      </el-tag>
    </div>

    <el-table :data="orders" v-loading="loading" border stripe>
      <el-table-column prop="order_no" label="订单号" width="180" />
      <el-table-column prop="supplier.name" label="供应商" width="150">
        <template #default="{ row }">
          {{ row.supplier?.name }}
        </template>
      </el-table-column>
      <el-table-column prop="customer_name" label="客户名称" width="120" />
      <el-table-column prop="customer_phone" label="客户电话" width="130" />
      <el-table-column prop="total_quantity" label="数量" width="80" align="center">
        <template #default="{ row }">
          {{ row.total_quantity }}件
          <el-tag v-if="row.shipped_quantity > 0" size="small" type="success">
            已发{{ row.shipped_quantity }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="total_amount" label="金额" width="120" align="right">
        <template #default="{ row }">
          ¥{{ row.total_amount?.toFixed(2) }}
        </template>
      </el-table-column>
      <el-table-column prop="status" label="状态" width="100">
        <template #default="{ row }">
          <el-tag :type="getStatusType(row.status)">
            {{ getStatusText(row.status) }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="created_at" label="创建时间" width="160">
        <template #default="{ row }">
          {{ formatDate(row.created_at) }}
        </template>
      </el-table-column>
      <el-table-column label="操作" width="360" fixed="right">
        <template #default="{ row }">
          <el-button type="primary" link @click="handleView(row)">查看</el-button>
          <el-button
            v-if="row.status === 'pending'"
            type="success"
            link
            @click="handleConfirm(row)"
          >
            确认
          </el-button>
          <el-button
            v-if="row.status === 'confirmed'"
            type="primary"
            link
            @click="handleProcess(row)"
          >
            开始处理
          </el-button>
          <el-button
            v-if="row.status === 'confirmed' || row.status === 'processing'"
            type="warning"
            link
            @click="handleShip(row)"
          >
            发货
          </el-button>
          <el-button
            v-if="row.status === 'shipped'"
            type="success"
            link
            @click="handleComplete(row)"
          >
            完成
          </el-button>
          <el-button
            v-if="row.status === 'pending' || row.status === 'confirmed' || row.status === 'processing'"
            type="danger"
            link
            @click="handleCancel(row)"
          >
            取消
          </el-button>
          <el-button
            v-if="row.status === 'shipped' || row.status === 'completed'"
            type="danger"
            link
            @click="handleRefund(row)"
          >
            退款
          </el-button>
        </template>
      </el-table-column>
    </el-table>

    <div class="pagination">
      <el-pagination
        v-model:current-page="pagination.page"
        v-model:page-size="pagination.pageSize"
        :page-sizes="[15, 20, 30, 50]"
        :total="pagination.total"
        layout="total, sizes, prev, pager, next, jumper"
        @size-change="handleSizeChange"
        @current-change="handlePageChange"
      />
    </div>

    <el-dialog
      v-model="createDialogVisible"
      title="新建订单"
      width="800px"
      destroy-on-close
    >
      <el-form :model="createForm" label-width="100px">
        <el-row :gutter="20">
          <el-col :span="12">
            <el-form-item label="供应商" required>
              <el-select
                v-model="createForm.supplier_id"
                placeholder="选择供应商"
                style="width: 100%"
                @change="handleSupplierChange"
              >
                <el-option
                  v-for="supplier in suppliers"
                  :key="supplier.id"
                  :label="supplier.name"
                  :value="supplier.id"
                />
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="客户名称" required>
              <el-input v-model="createForm.customer_name" placeholder="请输入客户名称" />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="客户电话" required>
              <el-input v-model="createForm.customer_phone" placeholder="请输入客户电话" />
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="客户地址" required>
              <el-input v-model="createForm.customer_address" placeholder="请输入客户地址" />
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="客户备注">
              <el-input
                v-model="createForm.customer_remark"
                type="textarea"
                :rows="2"
                placeholder="请输入客户备注"
              />
            </el-form-item>
          </el-col>
        </el-row>

        <div class="form-section-title">产品明细</div>
        <el-table :data="createForm.items" border>
          <el-table-column label="产品" min-width="200">
            <template #default="{ row, $index }">
              <el-select
                v-model="row.product_id"
                placeholder="选择产品"
                style="width: 100%"
                @change="handleProductChange($index)"
              >
                <el-option
                  v-for="product in availableProducts"
                  :key="product.id"
                  :label="`${product.name} (SKU: ${product.sku}, MOQ: ${product.moq})`"
                  :value="product.id"
                />
              </el-select>
            </template>
          </el-table-column>
          <el-table-column label="单价" width="120">
            <template #default="{ row }">
              <span v-if="row.unit_price">¥{{ row.unit_price?.toFixed(2) }}</span>
            </template>
          </el-table-column>
          <el-table-column label="MOQ" width="80" align="center">
            <template #default="{ row }">
              {{ row.moq || '-' }}
            </template>
          </el-table-column>
          <el-table-column label="库存" width="80" align="center">
            <template #default="{ row }">
              {{ row.stock || '-' }}
            </template>
          </el-table-column>
          <el-table-column label="数量" width="120">
            <template #default="{ row, $index }">
              <el-input-number
                v-model="row.quantity"
                :min="1"
                :max="row.stock || 9999"
                style="width: 100%"
                @change="calculateSubtotal($index)"
              />
            </template>
          </el-table-column>
          <el-table-column label="小计" width="120">
            <template #default="{ row }">
              <span v-if="row.subtotal !== undefined">¥{{ row.subtotal?.toFixed(2) }}</span>
            </template>
          </el-table-column>
          <el-table-column label="操作" width="80" align="center">
            <template #default="{ $index }">
              <el-button
                v-if="createForm.items.length > 1"
                type="danger"
                link
                @click="removeProduct($index)"
              >
                删除
              </el-button>
            </template>
          </el-table-column>
        </el-table>
        <el-button
          type="primary"
          link
          style="margin-top: 12px"
          @click="addProduct"
          :disabled="!createForm.supplier_id"
        >
          + 添加产品
        </el-button>

        <div class="order-total">
          订单总金额：<span class="total-amount">¥{{ totalAmount?.toFixed(2) }}</span>
        </div>
      </el-form>
      <template #footer>
        <el-button @click="createDialogVisible = false">取消</el-button>
        <el-button type="primary" @click="submitCreate" :loading="submitting">创建订单</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Search, Plus } from '@element-plus/icons-vue'
import { moqOrderApi, supplierApi, productApi, statusMap } from '@/api/moqDirectShip'

const props = defineProps({
  statusFilter: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['view-detail', 'order-updated'])

const loading = ref(false)
const submitting = ref(false)
const orders = ref([])
const suppliers = ref([])
const availableProducts = ref([])
const createDialogVisible = ref(false)

const statusTabs = reactive({
  '': { text: '全部' },
  pending: { text: '待确认' },
  confirmed: { text: '已确认' },
  processing: { text: '处理中' },
  shipped: { text: '已发货' },
  completed: { text: '已完成' },
  cancelled: { text: '已取消' },
  refunded: { text: '已退款' },
})

const filters = reactive({
  keyword: '',
  supplier_id: '',
})

const pagination = reactive({
  page: 1,
  pageSize: 15,
  total: 0,
})

const createForm = reactive({
  supplier_id: '',
  customer_name: '',
  customer_phone: '',
  customer_address: '',
  customer_remark: '',
  items: [{ product_id: '', quantity: 1 }],
})

const totalAmount = computed(() => {
  return createForm.items.reduce((sum, item) => sum + (item.subtotal || 0), 0)
})

const getStatusType = (status) => {
  return statusMap[status]?.type || 'info'
}

const getStatusText = (status) => {
  return statusMap[status]?.text || '未知'
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

const loadSuppliers = async () => {
  try {
    const res = await supplierApi.all()
    if (res.code === 200) {
      suppliers.value = res.data
    }
  } catch (error) {
    console.error('加载供应商失败:', error)
  }
}

const loadProducts = async (supplierId) => {
  try {
    const res = await productApi.bySupplier(supplierId)
    if (res.code === 200) {
      availableProducts.value = res.data
    }
  } catch (error) {
    console.error('加载产品失败:', error)
  }
}

const loadOrders = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.page,
      per_page: pagination.pageSize,
      status: props.statusFilter || undefined,
      keyword: filters.keyword || undefined,
      supplier_id: filters.supplier_id || undefined,
    }
    const res = await moqOrderApi.list(params)
    if (res.code === 200) {
      orders.value = res.data.data
      pagination.total = res.data.total
    }
  } catch (error) {
    console.error('加载订单列表失败:', error)
    ElMessage.error('加载订单列表失败')
  } finally {
    loading.value = false
  }
}

const handleStatusTab = (key) => {
  emit('order-updated')
}

const handleSupplierChange = (supplierId) => {
  loadProducts(supplierId)
  createForm.items = [{ product_id: '', quantity: 1 }]
}

const handleProductChange = (index) => {
  const item = createForm.items[index]
  const product = availableProducts.value.find((p) => p.id === item.product_id)
  if (product) {
    item.unit_price = product.price
    item.moq = product.moq
    item.stock = product.stock
    if (item.quantity < product.moq) {
      item.quantity = product.moq
    }
    calculateSubtotal(index)
  }
}

const calculateSubtotal = (index) => {
  const item = createForm.items[index]
  if (item.unit_price && item.quantity) {
    item.subtotal = item.unit_price * item.quantity
  }
}

const addProduct = () => {
  createForm.items.push({ product_id: '', quantity: 1 })
}

const removeProduct = (index) => {
  createForm.items.splice(index, 1)
}

const handleCreate = () => {
  createDialogVisible.value = true
}

const submitCreate = async () => {
  if (!createForm.supplier_id) {
    ElMessage.warning('请选择供应商')
    return
  }
  if (!createForm.customer_name || !createForm.customer_phone || !createForm.customer_address) {
    ElMessage.warning('请填写完整的客户信息')
    return
  }
  const invalidItem = createForm.items.find((item) => !item.product_id || item.quantity < 1)
  if (invalidItem) {
    ElMessage.warning('请完善产品信息')
    return
  }

  submitting.value = true
  try {
    const res = await moqOrderApi.create(createForm)
    if (res.code === 200) {
      ElMessage.success('订单创建成功')
      createDialogVisible.value = false
      loadOrders()
      emit('order-updated')
    } else {
      ElMessage.error(res.message || '创建失败')
    }
  } catch (error) {
    ElMessage.error(error?.message || '创建失败')
  } finally {
    submitting.value = false
  }
}

const handleView = (row) => {
  emit('view-detail', row.id)
}

const handleConfirm = async (row) => {
  try {
    await ElMessageBox.confirm('确认该订单吗？', '确认订单', {
      type: 'warning',
    })
    const res = await moqOrderApi.confirm(row.id)
    if (res.code === 200) {
      ElMessage.success('订单已确认')
      loadOrders()
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

const handleProcess = async (row) => {
  try {
    await ElMessageBox.confirm('开始处理该订单吗？', '开始处理', {
      type: 'warning',
    })
    const res = await moqOrderApi.process(row.id)
    if (res.code === 200) {
      ElMessage.success('订单已开始处理')
      loadOrders()
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

const handleShip = (row) => {
  emit('view-detail', row.id)
}

const handleComplete = async (row) => {
  try {
    await ElMessageBox.confirm('确认该订单已完成吗？', '完成订单', {
      type: 'warning',
    })
    const res = await moqOrderApi.complete(row.id)
    if (res.code === 200) {
      ElMessage.success('订单已完成')
      loadOrders()
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

const handleCancel = async (row) => {
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
    const res = await moqOrderApi.cancel(row.id, { reason })
    if (res.code === 200) {
      ElMessage.success('订单已取消')
      loadOrders()
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

const handleRefund = async (row) => {
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
    const res = await moqOrderApi.refund(row.id, { reason })
    if (res.code === 200) {
      ElMessage.success('退款申请已提交')
      loadOrders()
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

const handleSizeChange = (size) => {
  pagination.pageSize = size
  pagination.page = 1
  loadOrders()
}

const handlePageChange = (page) => {
  pagination.page = page
  loadOrders()
}

watch(
  () => props.statusFilter,
  () => {
    pagination.page = 1
    loadOrders()
  }
)

onMounted(() => {
  loadSuppliers()
  loadOrders()
})

defineExpose({
  loadOrders,
})
</script>

<style scoped>
.order-list {
  padding: 16px 0;
}

.filter-bar {
  display: flex;
  gap: 12px;
  margin-bottom: 16px;
}

.filter-input {
  flex: 1;
  max-width: 400px;
}

.filter-select {
  width: 200px;
}

.status-tabs {
  display: flex;
  gap: 8px;
  margin-bottom: 16px;
  flex-wrap: wrap;
}

.status-tag {
  cursor: pointer;
  padding: 8px 16px;
}

.count {
  margin-left: 4px;
}

.pagination {
  display: flex;
  justify-content: flex-end;
  margin-top: 20px;
}

.form-section-title {
  font-weight: 600;
  margin: 16px 0 12px 0;
  color: #303133;
}

.order-total {
  text-align: right;
  margin-top: 20px;
  font-size: 16px;
}

.total-amount {
  color: #f56c6c;
  font-size: 24px;
  font-weight: 700;
  margin-left: 8px;
}
</style>
