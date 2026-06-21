<template>
  <div class="supplier-list">
    <div class="toolbar">
      <el-input
        v-model="keyword"
        placeholder="搜索供应商名称、编码、联系人、电话"
        clearable
        class="search-input"
        @keyup.enter="loadSuppliers"
        @clear="loadSuppliers"
      >
        <template #prefix>
          <el-icon><Search /></el-icon>
        </template>
      </el-input>
      <el-button type="primary" @click="handleCreate">
        <el-icon><Plus /></el-icon>
        新建供应商
      </el-button>
    </div>

    <el-table :data="suppliers" v-loading="loading" border stripe>
      <el-table-column prop="code" label="供应商编码" width="150" />
      <el-table-column prop="name" label="供应商名称" min-width="200" />
      <el-table-column prop="contact_name" label="联系人" width="120" />
      <el-table-column prop="contact_phone" label="联系电话" width="150" />
      <el-table-column prop="address" label="地址" min-width="200" />
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
      :title="isEdit ? '编辑供应商' : '新建供应商'"
      width="600px"
      destroy-on-close
    >
      <el-form :model="form" label-width="100px">
        <el-form-item label="供应商编码" required>
          <el-input v-model="form.code" placeholder="请输入供应商编码" />
        </el-form-item>
        <el-form-item label="供应商名称" required>
          <el-input v-model="form.name" placeholder="请输入供应商名称" />
        </el-form-item>
        <el-form-item label="联系人">
          <el-input v-model="form.contact_name" placeholder="请输入联系人" />
        </el-form-item>
        <el-form-item label="联系电话">
          <el-input v-model="form.contact_phone" placeholder="请输入联系电话" />
        </el-form-item>
        <el-form-item label="地址">
          <el-input v-model="form.address" type="textarea" :rows="2" placeholder="请输入地址" />
        </el-form-item>
        <el-form-item label="备注">
          <el-input v-model="form.remark" type="textarea" :rows="2" placeholder="请输入备注" />
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
import { supplierApi } from '@/api/moqDirectShip'

const loading = ref(false)
const submitting = ref(false)
const keyword = ref('')
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
  code: '',
  name: '',
  contact_name: '',
  contact_phone: '',
  address: '',
  remark: '',
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
  loading.value = true
  try {
    const params = {
      page: pagination.page,
      per_page: pagination.pageSize,
      keyword: keyword.value || undefined,
    }
    const res = await supplierApi.list(params)
    if (res.code === 200) {
      suppliers.value = res.data.data
      pagination.total = res.data.total
    }
  } catch (error) {
    console.error('加载供应商列表失败:', error)
    ElMessage.error('加载供应商列表失败')
  } finally {
    loading.value = false
  }
}

const handleCreate = () => {
  isEdit.value = false
  currentId.value = null
  Object.assign(form, {
    code: '',
    name: '',
    contact_name: '',
    contact_phone: '',
    address: '',
    remark: '',
    is_active: true,
  })
  dialogVisible.value = true
}

const handleEdit = (row) => {
  isEdit.value = true
  currentId.value = row.id
  Object.assign(form, {
    code: row.code,
    name: row.name,
    contact_name: row.contact_name,
    contact_phone: row.contact_phone,
    address: row.address,
    remark: row.remark,
    is_active: row.is_active,
  })
  dialogVisible.value = true
}

const submitForm = async () => {
  if (!form.code || !form.name) {
    ElMessage.warning('请填写必填项')
    return
  }

  submitting.value = true
  try {
    const res = isEdit.value
      ? await supplierApi.update(currentId.value, form)
      : await supplierApi.create(form)
    if (res.code === 200) {
      ElMessage.success(isEdit.value ? '更新成功' : '创建成功')
      dialogVisible.value = false
      loadSuppliers()
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
    await ElMessageBox.confirm('确认删除该供应商吗？', '删除确认', {
      type: 'warning',
    })
    const res = await supplierApi.delete(row.id)
    if (res.code === 200) {
      ElMessage.success('删除成功')
      loadSuppliers()
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
  loadSuppliers()
}

const handlePageChange = (page) => {
  pagination.page = page
  loadSuppliers()
}

onMounted(() => {
  loadSuppliers()
})
</script>

<style scoped>
.supplier-list {
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

.pagination {
  display: flex;
  justify-content: flex-end;
  margin-top: 20px;
}
</style>
