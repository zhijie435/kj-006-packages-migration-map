<template>
  <div class="app-container">
    <el-container class="layout">
      <el-aside width="240px" class="sidebar">
        <div class="logo">
          <h1>P1 金路径二</h1>
          <p>国内小批量 MOQ 直发</p>
        </div>
        <el-menu
          :default-active="activeMenu"
          class="sidebar-menu"
          @select="handleMenuSelect"
        >
          <el-menu-item index="/moq-direct-ship">
            <el-icon><Goods /></el-icon>
            <span>MOQ直发管理</span>
          </el-menu-item>
        </el-menu>
      </el-aside>
      <el-container class="main-container">
        <el-main class="main-content">
          <router-view />
        </el-main>
      </el-container>
    </el-container>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { Goods } from '@element-plus/icons-vue'

const route = useRoute()
const activeMenu = ref('/moq-direct-ship')

watch(
  () => route.path,
  (path) => {
    activeMenu.value = path
  },
  { immediate: true }
)

const handleMenuSelect = (index) => {
  activeMenu.value = index
}
</script>

<style scoped>
.app-container {
  width: 100%;
  height: 100vh;
  overflow: hidden;
}

.layout {
  height: 100%;
}

.sidebar {
  background: #304156;
  display: flex;
  flex-direction: column;
}

.logo {
  padding: 24px 16px;
  text-align: center;
  border-bottom: 1px solid #1f2d3d;
}

.logo h1 {
  margin: 0 0 8px 0;
  font-size: 20px;
  font-weight: 700;
  color: #fff;
}

.logo p {
  margin: 0;
  font-size: 12px;
  color: #909399;
}

.sidebar-menu {
  border-right: none;
  flex: 1;
}

:deep(.el-menu) {
  background-color: #304156;
  border-right: none;
}

:deep(.el-menu-item) {
  color: #bfcbd9;
  height: 56px;
  line-height: 56px;
}

:deep(.el-menu-item:hover) {
  background-color: #263445;
  color: #fff;
}

:deep(.el-menu-item.is-active) {
  background-color: #409eff;
  color: #fff;
}

.main-container {
  background: #f0f2f5;
  overflow: hidden;
}

.main-content {
  padding: 0;
  height: 100%;
  overflow-y: auto;
}
</style>
