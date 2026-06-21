<template>
    <div class="course-card" @click="$emit('click', course)">
        <div class="course-card__cover">
            <img
                v-if="course.cover_image && !imageError"
                :src="course.cover_image"
                :alt="course.title"
                class="course-card__image"
                @error="imageError = true"
                loading="lazy"
            />
            <div v-else class="course-card__placeholder">
                <span class="course-card__placeholder-icon">📖</span>
                <span class="course-card__placeholder-text">No Cover</span>
            </div>
            <span v-if="course.status" class="course-card__status" :class="statusClass">
                {{ statusText }}
            </span>
            <div v-if="course.duration" class="course-card__duration">
                <span>⏱</span>
                <span>{{ course.duration }}</span>
            </div>
        </div>

        <div class="course-card__body">
            <h3 class="course-card__title" :title="course.title">
                {{ course.title }}
            </h3>

            <p v-if="course.description" class="course-card__description">
                {{ truncatedDescription }}
            </p>

            <div class="course-card__meta">
                <div v-if="course.teacher" class="course-card__teacher">
                    <div v-if="course.teacher.avatar" class="course-card__teacher-avatar">
                        <img :src="course.teacher.avatar" :alt="course.teacher.name" />
                    </div>
                    <span class="course-card__teacher-name">{{ course.teacher.name }}</span>
                </div>
            </div>

            <div v-if="hasTags" class="course-card__tags">
                <span v-if="course.difficulty" class="course-card__tag" :class="`is-${course.difficulty}`">
                    {{ course.difficulty }}
                </span>
                <span v-if="course.category" class="course-card__tag is-category">
                    {{ course.category }}
                </span>
                <span v-if="course.lesson_count" class="course-card__tag is-lessons">
                    {{ course.lesson_count }} 课时
                </span>
            </div>

            <div class="course-card__footer">
                <div class="course-card__price-wrap">
                    <span v-if="isFree" class="course-card__price is-free">免费</span>
                    <span v-else class="course-card__price">
                        <span class="course-card__price-symbol">¥</span>
                        <span class="course-card__price-value">{{ formattedPrice }}</span>
                    </span>
                </div>
                <div class="course-card__stats">
                    <span v-if="course.enrollment_count !== undefined" class="course-card__stat">
                        <span>👥</span>
                        <span>{{ course.enrollment_count }}</span>
                    </span>
                    <span v-if="course.rating !== undefined" class="course-card__stat">
                        <span>⭐</span>
                        <span>{{ course.rating }}</span>
                    </span>
                </div>
            </div>

            <div v-if="$slots.actions" class="course-card__actions">
                <slot name="actions" :course="course" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    course: {
        type: Object,
        required: true
    },
    statusLabels: {
        type: Object,
        default: () => ({})
    }
});

defineEmits(['click']);

const imageError = ref(false);

const defaultStatusLabels = {
    published: 'Published',
    draft: 'Draft',
    archived: 'Archived',
    pending: 'Pending'
};

const isFree = computed(() => {
    const price = Number(props.course.price);
    return price === 0 || props.course.is_free === true;
});

const formattedPrice = computed(() => {
    const price = Number(props.course.price);
    return isNaN(price) ? '0.00' : price.toFixed(2);
});

const statusClass = computed(() => {
    const status = props.course.status;
    const map = {
        published: 'is-published',
        draft: 'is-draft',
        archived: 'is-archived',
        pending: 'is-pending'
    };
    return map[status] || 'is-default';
});

const statusText = computed(() => {
    const status = props.course.status;
    return props.statusLabels[status] || defaultStatusLabels[status] || status;
});

const truncatedDescription = computed(() => {
    if (!props.course.description) return '';
    const desc = String(props.course.description);
    return desc.length > 80 ? desc.slice(0, 80) + '...' : desc;
});

const hasTags = computed(() => {
    return props.course.difficulty || props.course.category || props.course.lesson_count;
});
</script>

<style scoped>
.course-card {
    display: flex;
    flex-direction: column;
    background: #ffffff;
    border: 1px solid #eef0f4;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    cursor: pointer;
}

.course-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.12);
}

.course-card__cover {
    position: relative;
    height: 180px;
    background: linear-gradient(135deg, #f4f6fa, #e8ecf3);
    overflow: hidden;
}

.course-card__image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.3s ease;
}

.course-card:hover .course-card__image {
    transform: scale(1.05);
}

.course-card__placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: #9aa3b2;
}

.course-card__placeholder-icon {
    font-size: 2.4rem;
    opacity: 0.6;
}

.course-card__placeholder-text {
    font-size: 0.85rem;
}

.course-card__status {
    position: absolute;
    top: 12px;
    left: 12px;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #ffffff;
    text-transform: capitalize;
    backdrop-filter: blur(4px);
}

.course-card__status.is-published {
    background: rgba(22, 163, 74, 0.9);
}
.course-card__status.is-draft {
    background: rgba(217, 119, 6, 0.9);
}
.course-card__status.is-archived {
    background: rgba(107, 114, 128, 0.9);
}
.course-card__status.is-pending {
    background: rgba(59, 130, 246, 0.9);
}
.course-card__status.is-default {
    background: rgba(71, 85, 105, 0.9);
}

.course-card__duration {
    position: absolute;
    bottom: 12px;
    right: 12px;
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    background: rgba(15, 23, 42, 0.75);
    color: #ffffff;
    border-radius: 8px;
    font-size: 0.75rem;
    backdrop-filter: blur(4px);
}

.course-card__body {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 18px 20px 20px;
    flex: 1;
}

.course-card__title {
    margin: 0;
    font-size: 1.08rem;
    font-weight: 600;
    color: #1f2937;
    line-height: 1.4;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.course-card__description {
    margin: 0;
    font-size: 0.85rem;
    color: #6b7280;
    line-height: 1.55;
    min-height: 2.5em;
}

.course-card__meta {
    display: flex;
    align-items: center;
    gap: 12px;
}

.course-card__teacher {
    display: flex;
    align-items: center;
    gap: 8px;
}

.course-card__teacher-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.course-card__teacher-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.course-card__teacher-name {
    font-size: 0.85rem;
    color: #475569;
    font-weight: 500;
}

.course-card__tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.course-card__tag {
    padding: 3px 10px;
    border-radius: 8px;
    font-size: 0.76rem;
    font-weight: 500;
}

.course-card__tag.is-beginner {
    background: #dcfce7;
    color: #166534;
}
.course-card__tag.is-intermediate {
    background: #fef3c7;
    color: #92400e;
}
.course-card__tag.is-advanced {
    background: #fee2e2;
    color: #991b1b;
}
.course-card__tag.is-category {
    background: #eff6ff;
    color: #1e40af;
}
.course-card__tag.is-lessons {
    background: #f4f6fa;
    color: #475569;
}

.course-card__footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: auto;
    padding-top: 4px;
}

.course-card__price-wrap {
    display: flex;
    align-items: baseline;
}

.course-card__price {
    font-weight: 700;
    color: #dc2626;
    display: flex;
    align-items: baseline;
}

.course-card__price.is-free {
    color: #16a34a;
    font-size: 1.05rem;
}

.course-card__price-symbol {
    font-size: 0.95rem;
    margin-right: 1px;
}

.course-card__price-value {
    font-size: 1.4rem;
    letter-spacing: -0.01em;
}

.course-card__stats {
    display: flex;
    align-items: center;
    gap: 10px;
}

.course-card__stat {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 0.82rem;
    color: #6b7280;
}

.course-card__actions {
    display: flex;
    gap: 8px;
    padding-top: 12px;
    border-top: 1px solid #f1f5f9;
}
</style>
