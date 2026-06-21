<template>
    <div class="data-table">
        <div class="data-table__toolbar">
            <div class="data-table__search">
                <input
                    v-model="searchQuery"
                    type="text"
                    class="data-table__search-input"
                    :placeholder="searchPlaceholder"
                    @input="handleSearch"
                />
                <button class="data-table__search-btn" @click="handleSearch">
                    {{ locale.search || 'Search' }}
                </button>
                <button class="data-table__reset-btn" @click="handleReset">
                    {{ locale.reset || 'Reset' }}
                </button>
            </div>
            <div class="data-table__page-size">
                <label>{{ showPageSizeLabel }}</label>
                <select v-model="pageSize" @change="handlePageSizeChange">
                    <option v-for="size in pageSizeOptions" :key="size" :value="size">
                        {{ size }}
                    </option>
                </select>
            </div>
        </div>

        <div class="data-table__wrapper">
            <table class="data-table__table">
                <thead>
                    <tr>
                        <th
                            v-for="column in columns"
                            :key="column.prop"
                            :style="column.width ? { width: column.width } : null"
                        >
                            {{ column.label }}
                        </th>
                        <th v-if="hasActions" class="data-table__actions-cell">
                            {{ locale.actions || 'Actions' }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, index) in paginatedData" :key="row.id ?? index">
                        <td v-for="column in columns" :key="column.prop">
                            <slot v-if="column.slotName" :name="column.slotName" :row="row" :index="index" />
                            <template v-else>{{ formatCell(row, column) }}</template>
                        </td>
                        <td v-if="hasActions" class="data-table__actions-cell">
                            <slot name="actions" :row="row" :index="index" />
                        </td>
                    </tr>
                    <tr v-if="!filteredData.length">
                        <td :colspan="colspan" class="data-table__empty">
                            {{ noDataText }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="data-table__pagination" v-if="showPagination">
            <div class="data-table__pagination-info">
                {{ paginationInfoText }}
            </div>
            <div class="data-table__pagination-controls">
                <button
                    class="data-table__page-btn"
                    :disabled="currentPage <= 1"
                    @click="goToPage(1)"
                >
                    «
                </button>
                <button
                    class="data-table__page-btn"
                    :disabled="currentPage <= 1"
                    @click="goToPage(currentPage - 1)"
                >
                    ‹
                </button>
                <button
                    v-for="page in visiblePages"
                    :key="page"
                    class="data-table__page-btn"
                    :class="{ 'is-active': page === currentPage }"
                    @click="goToPage(page)"
                >
                    {{ page }}
                </button>
                <button
                    class="data-table__page-btn"
                    :disabled="currentPage >= totalPages"
                    @click="goToPage(currentPage + 1)"
                >
                    ›
                </button>
                <button
                    class="data-table__page-btn"
                    :disabled="currentPage >= totalPages"
                    @click="goToPage(totalPages)"
                >
                    »
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, useSlots, watch } from 'vue';

const props = defineProps({
    columns: {
        type: Array,
        required: true
    },
    data: {
        type: Array,
        required: true
    },
    searchable: {
        type: Boolean,
        default: true
    },
    searchFields: {
        type: Array,
        default: () => []
    },
    searchPlaceholder: {
        type: String,
        default: 'Search...'
    },
    noDataText: {
        type: String,
        default: 'No data available'
    },
    pagination: {
        type: Boolean,
        default: true
    },
    pageSizeOptions: {
        type: Array,
        default: () => [10, 20, 50, 100]
    },
    defaultPageSize: {
        type: Number,
        default: 10
    },
    locale: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['search', 'reset', 'page-change', 'page-size-change']);

const slots = useSlots();

const searchQuery = ref('');
const currentPage = ref(1);
const pageSize = ref(props.defaultPageSize);

const hasActions = computed(() => !!slots.actions);

const colspan = computed(() => props.columns.length + (hasActions.value ? 1 : 0));

const activeSearchFields = computed(() => {
    if (props.searchFields.length > 0) {
        return props.searchFields;
    }
    return props.columns.filter(c => !c.slotName).map(c => c.prop);
});

const filteredData = computed(() => {
    if (!searchQuery.value.trim()) {
        return props.data;
    }
    const query = searchQuery.value.toLowerCase().trim();
    return props.data.filter(row => {
        return activeSearchFields.value.some(field => {
            const value = row[field];
            return value !== null && value !== undefined &&
                String(value).toLowerCase().includes(query);
        });
    });
});

const totalPages = computed(() => {
    return Math.max(1, Math.ceil(filteredData.value.length / pageSize.value));
});

const paginatedData = computed(() => {
    if (!props.pagination) {
        return filteredData.value;
    }
    const start = (currentPage.value - 1) * pageSize.value;
    const end = start + pageSize.value;
    return filteredData.value.slice(start, end);
});

const showPagination = computed(() => {
    return props.pagination && filteredData.value.length > 0;
});

const showPageSizeLabel = computed(() => {
    return props.locale.show || 'Show';
});

const visiblePages = computed(() => {
    const pages = [];
    const total = totalPages.value;
    const current = currentPage.value;
    let start = Math.max(1, current - 2);
    let end = Math.min(total, current + 2);
    if (end - start < 4) {
        if (start === 1) {
            end = Math.min(5, total);
        } else if (end === total) {
            start = Math.max(1, total - 4);
        }
    }
    for (let i = start; i <= end; i++) {
        pages.push(i);
    }
    return pages;
});

const paginationInfoText = computed(() => {
    const total = filteredData.value.length;
    if (total === 0) {
        return '0 items';
    }
    const start = (currentPage.value - 1) * pageSize.value + 1;
    const end = Math.min(currentPage.value * pageSize.value, total);
    return `Showing ${start} - ${end} of ${total} items`;
});

const formatCell = (row, column) => {
    const value = row[column.prop];
    if (column.formatter && typeof column.formatter === 'function') {
        return column.formatter(value, row);
    }
    if (value === null || value === undefined || value === '') {
        return '-';
    }
    return value;
};

const handleSearch = () => {
    currentPage.value = 1;
    emit('search', searchQuery.value);
};

const handleReset = () => {
    searchQuery.value = '';
    currentPage.value = 1;
    emit('reset');
};

const goToPage = (page) => {
    if (page < 1 || page > totalPages.value || page === currentPage.value) {
        return;
    }
    currentPage.value = page;
    emit('page-change', page);
};

const handlePageSizeChange = () => {
    currentPage.value = 1;
    emit('page-size-change', pageSize.value);
};

watch(() => props.data, () => {
    if (currentPage.value > totalPages.value) {
        currentPage.value = totalPages.value;
    }
});
</script>

<style scoped>
.data-table {
    background: #ffffff;
    border: 1px solid #eef0f4;
    border-radius: 12px;
    overflow: hidden;
}

.data-table__toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 16px;
    border-bottom: 1px solid #eef0f4;
}

.data-table__search {
    display: flex;
    align-items: center;
    gap: 8px;
}

.data-table__search-input {
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.9rem;
    outline: none;
    min-width: 220px;
    transition: border-color 0.2s;
}

.data-table__search-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.data-table__search-btn,
.data-table__reset-btn {
    padding: 8px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s;
    background: #ffffff;
}

.data-table__search-btn {
    background: #3b82f6;
    border-color: #3b82f6;
    color: #ffffff;
}

.data-table__search-btn:hover {
    background: #2563eb;
    border-color: #2563eb;
}

.data-table__reset-btn:hover {
    background: #f3f4f6;
}

.data-table__page-size {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    color: #475569;
}

.data-table__page-size select {
    padding: 6px 10px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.85rem;
    outline: none;
    cursor: pointer;
    background: #ffffff;
}

.data-table__wrapper {
    overflow-x: auto;
}

.data-table__table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}

.data-table__table thead th {
    padding: 12px 16px;
    text-align: left;
    font-weight: 600;
    color: #475569;
    background: #f8fafc;
    border-bottom: 1px solid #eef0f4;
    white-space: nowrap;
}

.data-table__table tbody td {
    padding: 12px 16px;
    color: #1f2937;
    border-bottom: 1px solid #f1f5f9;
}

.data-table__table tbody tr:last-child td {
    border-bottom: none;
}

.data-table__table tbody tr:hover {
    background: #f8fafc;
}

.data-table__actions-cell {
    text-align: right;
    white-space: nowrap;
}

.data-table__empty {
    text-align: center;
    color: #9aa3b2;
    padding: 48px 16px;
}

.data-table__pagination {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 16px;
    border-top: 1px solid #eef0f4;
}

.data-table__pagination-info {
    font-size: 0.85rem;
    color: #6b7280;
}

.data-table__pagination-controls {
    display: flex;
    align-items: center;
    gap: 4px;
}

.data-table__page-btn {
    min-width: 34px;
    height: 34px;
    padding: 0 10px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #ffffff;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s;
    color: #374151;
}

.data-table__page-btn:hover:not(:disabled):not(.is-active) {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.data-table__page-btn.is-active {
    background: #3b82f6;
    border-color: #3b82f6;
    color: #ffffff;
}

.data-table__page-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
