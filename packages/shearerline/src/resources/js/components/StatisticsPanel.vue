<template>
    <div class="statistics-panel">
        <div
            v-for="item in statItems"
            :key="item.key"
            class="statistics-panel__card"
            :class="`statistics-panel__card--${item.variant}`"
        >
            <div class="statistics-panel__icon-wrap">
                <span class="statistics-panel__icon">{{ item.icon }}</span>
            </div>
            <div class="statistics-panel__info">
                <div class="statistics-panel__label">{{ item.label }}</div>
                <div class="statistics-panel__value">{{ formatValue(item.key) }}</div>
                <div v-if="item.trend !== undefined" class="statistics-panel__trend" :class="trendClass(item.trend)">
                    <span class="statistics-panel__trend-arrow">{{ item.trend >= 0 ? '↑' : '↓' }}</span>
                    <span class="statistics-panel__trend-value">{{ Math.abs(item.trend) }}%</span>
                    <span class="statistics-panel__trend-label">{{ item.trendLabel || 'vs last period' }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    stats: {
        type: Object,
        required: true
    },
    labels: {
        type: Object,
        default: () => ({})
    }
});

const defaultLabels = {
    courses: 'Courses',
    students: 'Students',
    teachers: 'Teachers',
    enrollments: 'Enrollments'
};

const defaultIcons = {
    courses: '📚',
    students: '🎓',
    teachers: '👨‍🏫',
    enrollments: '📝'
};

const variants = ['blue', 'green', 'purple', 'orange'];

const statItems = computed(() => {
    const keys = ['courses', 'students', 'teachers', 'enrollments'];
    return keys.map((key, index) => {
        const data = props.stats[key] || {};
        const value = typeof data === 'object' ? (data.value ?? data.count ?? 0) : (data ?? 0);
        const trend = typeof data === 'object' ? data.trend : undefined;
        const trendLabel = typeof data === 'object' ? data.trendLabel : undefined;
        return {
            key,
            label: props.labels[key] || defaultLabels[key],
            icon: defaultIcons[key],
            variant: variants[index],
            value,
            trend,
            trendLabel
        };
    });
});

const formatValue = (key) => {
    const item = statItems.value.find(i => i.key === key);
    const value = item ? item.value : 0;
    return Number(value).toLocaleString();
};

const trendClass = (trend) => {
    if (trend > 0) return 'is-up';
    if (trend < 0) return 'is-down';
    return 'is-flat';
};
</script>

<style scoped>
.statistics-panel {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 20px;
}

.statistics-panel__card {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 24px;
    background: #ffffff;
    border: 1px solid #eef0f4;
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    position: relative;
    overflow: hidden;
}

.statistics-panel__card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    opacity: 0.9;
}

.statistics-panel__card--blue::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
.statistics-panel__card--green::before { background: linear-gradient(90deg, #16a34a, #4ade80); }
.statistics-panel__card--purple::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
.statistics-panel__card--orange::before { background: linear-gradient(90deg, #f97316, #fb923c); }

.statistics-panel__card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
}

.statistics-panel__icon-wrap {
    flex-shrink: 0;
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
}

.statistics-panel__card--blue .statistics-panel__icon-wrap {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
}
.statistics-panel__card--green .statistics-panel__icon-wrap {
    background: linear-gradient(135deg, #dcfce7, #bbf7d0);
}
.statistics-panel__card--purple .statistics-panel__icon-wrap {
    background: linear-gradient(135deg, #ede9fe, #ddd6fe);
}
.statistics-panel__card--orange .statistics-panel__icon-wrap {
    background: linear-gradient(135deg, #ffedd5, #fed7aa);
}

.statistics-panel__icon {
    font-size: 1.6rem;
    line-height: 1;
}

.statistics-panel__info {
    flex: 1;
    min-width: 0;
}

.statistics-panel__label {
    font-size: 0.88rem;
    color: #6b7280;
    font-weight: 500;
    margin-bottom: 6px;
}

.statistics-panel__value {
    font-size: 1.9rem;
    font-weight: 700;
    color: #111827;
    line-height: 1.2;
    letter-spacing: -0.02em;
}

.statistics-panel__trend {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 8px;
    font-size: 0.8rem;
}

.statistics-panel__trend.is-up {
    color: #16a34a;
}

.statistics-panel__trend.is-down {
    color: #dc2626;
}

.statistics-panel__trend.is-flat {
    color: #6b7280;
}

.statistics-panel__trend-arrow {
    font-weight: 600;
}

.statistics-panel__trend-value {
    font-weight: 600;
}

.statistics-panel__trend-label {
    color: #9ca3af;
    margin-left: 4px;
}
</style>
