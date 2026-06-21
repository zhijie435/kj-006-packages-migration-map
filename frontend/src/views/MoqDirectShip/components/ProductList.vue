<template>
  <div class="product-list">
    <div class="toolbar">
      <el-input
        v-model="keyword"
        placeholder="搜索产品名称、SKU"
        clearable
        class="search-input"
        @keyup.enter="loadProducts"
        @clear="loadProducts"
      >
        <template #prefix>
          <el-icon><Search /></el-icon>
        </template>
      </el-input>

      <el-select
        v-model="supplierId"
        placeholder="选择供应商"
        clearable
        class="filter-select"
        @change="loadProducts"
        @clear="loadProducts"
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
        新建产品
      </el-button>
    </div>

    <el-table :data="products" v-loading="loading" border stripe>
      <el-table-column prop="sku" label="SKU编码" width="150" />
      <el-table-column prop="name" label="产品名称" min-width="200" />
      <el-table-column prop="supplier.name" label="供应商" width="150">
        <template #default="{ row }">
          {{ row.supplier?.name }}
        </template>
      </el-table-column>
      <el-table-column prop="price" label="单价" width="100" align="right">
        <template #default="{ row }">
          ¥{{ row.price?.toFixed(2) }}
        </template>
      </el-table-column>
      <el-table-column prop="moq" label="MOQ" width="80" align="center">
        <template #default="{ row }">
          {{ row.moq }}{{ row.unit }}
        </template>
      </el-table-column>
      <el-table-column prop="stock" label="库存" width="100" align="center">
        <template #default="{ row }">
          <el-tag :type="row.stock > 0 ? 'success' : 'danger'">
            {{ row.stock }}{{ row.unit }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="is_active" label="状态" width="100" align="center">
        <template #default="{ row }">
          <el-tag :type="row.is_active ? 'success' : 'danger'">
            {{ row.is_active ? '启用' : '禁用' }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="created_at" label="创建时间" width="160">
        <template #default="{ row }">
          {{ formatDate(row.created_at) }}
        </template>
      </el-table-column>
      <el-table-column label="操作" width="180" fixed="right">
        <template #default="{ row }">
          <el-button type="primary" link @click="handleEdit(row)">编辑</el-button>
          <el-button type="danger" link @click="handleDelete(row)">删除</el-button>
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
      v-model="dialogVisible"
      :title="isEdit ? '编辑产品' : '新建产品'"
      width="600px"
      destroy-on-close
    >
      <el-form :model="form" label-width="100px">
        <el-form-item label="所属供应商" required>
          <el-select
            v-model="form.supplier_id"
            placeholder="选择供应商"
            style="width: 100%"
          >
            <el-option
              v-for="supplier in suppliers"
              :key="supplier.id"
              :label="supplier.name"
              :value="supplier.id"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="SKU编码" required>
          <el-input v-model="form.sku" placeholder="请输入SKU编码" />
        </el-form-item>
        <el-form-item label="产品名称" required>
          <el-input v-model="form.name" placeholder="请输入产品名称" />
        </el-form-item>
        <el-form-item label="产品描述">
          <el-input
            v-model="form.description"
            type="textarea"
            :rows="2"
            placeholder="请输入产品描述"
          />
        </el-form-item>
        <el-row :gutter="20">
          <el-col :span="8">
            <el-form-item label="单价" required>
              <el-input-number
                v-model="form.price"
                :min="0"
                :precision="2"
                style="width: 100%"
              />
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item label="MOQ" required>
              <el-input-number
                v-model="form.moq"
                :min="1"
                style="width: 100%"
              />
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item label="库存" required>
              <el-input-number
                v-model="form.stock"
                :min="0"
                style="width: 100%"
              />
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item label="单位">
          <el-input v-model="form.unit" placeholder="件、个、箱等" />
        </el-form-item>
        <el-form-item label="图片地址">
          <el-input v-model="form.image_url" placeholder="请输入图片地址" />
        </el-form-item>
        <el-form-item label="状态">
          <el-switch v-model="form.is_active" active-text="启用" inactive-text="禁用" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button type="primary" @click="submitForm" :loading="submitting">
          {{ isEdit ? '保存' : '创建' }}
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Search, Plus } from '@element-plus/icons-vue'
import { productApi, supplierApi } from '@/api/moqDirectShip'

const loading = ref(false)
const submitting = ref(false)
const keyword = ref('')
const supplierId = ref('')
const products = ref([])
const suppliers = ref([])
const dialogVisible = ref(false)
const isEdit = ref(false)
const currentId = ref(null)

const pagination = reactive({
  page: 1,
  pageSize: 15,
  total: 0,
})

const form = reactive({
  supplier_id: '',
  sku: '',
  name: '',
  description: '',
  price: 0,
  moq: 1,
  stock: 0,
  unit: '件',
  image_url: '',
  specs: null,
  is_active: true,
})

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

const loadProducts = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.page,
      per_page: pagination.pageSize,
      keyword: keyword.value || undefined,
      supplier_id: supplierId.value || undefined,
    }
    const res = await productApi.list(params)
    if (res.code === 200) {
      products.value = res.data.data
      pagination.total = res.data.total
    }
  } catch (error) {
    console.error('加载产品列表失败:', error)
    ElMessage.error('加载产品列表失败')
  } finally {
    loading.value = false
  }
}

const handleCreate = () => {
  isEdit.value = false
  currentId.value = null
  Object.assign(form, {
    supplier_id: '',
    sku: '',
    name: '',
    description: '',
    price: 0,
    moq: 1,
    stock: 0,
    unit: '件',
    image_url: '',
    specs: null,
    is_active: true,
  })
  dialogVisible.value = true
}

const handleEdit = (row) => {
  isEdit.value = true
  currentId.value = row.id
  Object.assign(form, {
    supplier_id: row.supplier_id,
    sku: row.sku,
    name: row.name,
    description: row.description,
    price: row.price,
    moq: row.moq,
    stock: row.stock,
    unit: row.unit,
    image_url: row.image_url,
    specs: row.specs,
    is_active: row.is_active,
  })
  dialogVisible.value = true
}

const submitForm = async () => {
  if (!form.supplier_id || !form.sku || !form.name) {
    ElMessage.warning('请填写必填项')
    return
  }

  submitting.value = true
  try {
    const res = isEdit.value
      ? await productApi.update(currentId.value, form)
      : await productApi.create(form)
    if (res.code === 200) {
      ElMessage.success(isEdit.value ? '更新成功' : '创建成功')
      dialogVisible.value = false
      loadProducts()
    } else {
      ElMessage.error(res.message || '操作失败')
    }
  } catch (error) {
    ElMessage.error(error?.message || '操作失败')
  } finally {
    submitting.value = false
  }
}

const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm('确认删除该产品吗？', '删除确认', {
      type: 'warning',
    })
    const res = await productApi.delete(row.id)
    if (res.code === 200) {
      ElMessage.success('删除成功')
      loadProducts()
    } else {
      ElMessage.error(res.message || '删除失败')
    }
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error(error?.message || '删除失败')
    }
  }
}

const handleSizeChange = (size) => {
  pagination.pageSize = size
  pagination.page = 1
  loadProducts()
}

const handlePageChange = (page) => {
  pagination.page = page
  loadProducts()
}

onMounted(() => {
  loadSuppliers()
  loadProducts()
})
</script>

<style scoped>
.product-list {
  padding: 16px 0;
}

.toolbar {
  display: flex;
  gap: 12px;
  margin-bottom: 16px;
}

.search-input {
  flex: 1;
  max-width: 400px;
}

.filter-select {
  width: 200px;
}

.pagination {
  display: flex;
  justify-content: flex-end;
  margin-top: 20px;
}
</style>
