<template>
    <div class="statistics-panel">
        <div
            v-for="item in statItems"
            :key="item.key"
            class="statistics-panel__card"
        >
            <div class="statistics-panel__icon">{{ item.icon }}</div>
            <div class="statistics-panel__info">
                <div class="statistics-panel__value">{{ formatValue(item.key) }}</div>
                <div class="statistics-panel__label">{{ item.label }}</div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    stats: {
        type: Object,
        required: true
    }
});

const statItems = [
    { key: 'total_courses', label: '课程总数', icon: '📚' },
    { key: 'published_courses', label: '已发布课程', icon: '✅' },
    { key: 'total_students', label: '学生总数', icon: '🎓' },
    { key: 'active_students', label: '活跃学生', icon: '🟢' },
    { key: 'total_teachers', label: '讲师总数', icon: '👨‍🏫' },
    { key: 'total_enrollments', label: '报名总数', icon: '📝' },
    { key: 'completed_enrollments', label: '已完成报名', icon: '🏆' },
    { key: 'total_revenue', label: '总收入', icon: '💰' }
];

const formatValue = (key) => {
    const value = props.stats[key] ?? 0;
    if (key === 'total_revenue') {
        return '¥' + Number(value).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
    return Number(value).toLocaleString();
};
</script>

<style scoped>
.statistics-panel {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 16px;
}
.statistics-panel__card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 18px 20px;
    background: #ffffff;
    border: 1px solid #eef0f4;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.statistics-panel__card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(15, 23, 42, 0.1);
}
.statistics-panel__icon {
    flex-shrink: 0;
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    background: #f4f6fa;
    border-radius: 10px;
}
.statistics-panel__info {
    min-width: 0;
}
.statistics-panel__value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1.2;
}
.statistics-panel__label {
    margin-top: 2px;
    font-size: 0.85rem;
    color: #6b7280;
}
</style>
