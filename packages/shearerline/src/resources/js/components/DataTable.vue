<template>
    <div class="data-table">
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
                    <th v-if="hasActions" class="data-table__actions-cell">操作</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(row, index) in data" :key="row.id ?? index">
                    <td v-for="column in columns" :key="column.prop">
                        {{ row[column.prop] }}
                    </td>
                    <td v-if="hasActions" class="data-table__actions-cell">
                        <slot name="actions" :row="row" :index="index" />
                    </td>
                </tr>
                <tr v-if="!data.length">
                    <td :colspan="colspan" class="data-table__empty">
                        暂无数据
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { computed, useSlots } from 'vue';

const props = defineProps({
    columns: {
        type: Array,
        required: true
    },
    data: {
        type: Array,
        required: true
    }
});

const slots = useSlots();

const hasActions = computed(() => !!slots.actions);

const colspan = computed(() => props.columns.length + (hasActions.value ? 1 : 0));
</script>

<style scoped>
.data-table {
    width: 100%;
    overflow-x: auto;
    background: #ffffff;
    border: 1px solid #eef0f4;
    border-radius: 12px;
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
    padding: 32px 16px;
}
</style>
