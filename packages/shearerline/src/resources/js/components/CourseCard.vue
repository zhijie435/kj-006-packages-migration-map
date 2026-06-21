<template>
    <div class="course-card">
        <div class="course-card__cover">
            <img
                v-if="course.cover_image && !imageError"
                :src="course.cover_image"
                :alt="course.title"
                class="course-card__image"
                @error="imageError = true"
            />
            <div v-else class="course-card__placeholder">
                <span class="course-card__placeholder-text">No Cover</span>
            </div>
            <span class="course-card__status" :class="statusClass">{{ course.status }}</span>
        </div>
        <div class="course-card__body">
            <h3 class="course-card__title" :title="course.title">{{ course.title }}</h3>
            <div class="course-card__teacher">
                <span class="course-card__teacher-label">讲师</span>
                <span class="course-card__teacher-name">{{ course.teacher?.name || '未指定' }}</span>
            </div>
            <div class="course-card__tags">
                <span class="course-card__tag" v-if="course.difficulty">{{ course.difficulty }}</span>
                <span class="course-card__tag" v-if="course.category">{{ course.category }}</span>
            </div>
            <div class="course-card__footer">
                <span class="course-card__price">¥{{ formattedPrice }}</span>
                <span class="course-card__enrollment">{{ enrollmentCount }} 人已报名</span>
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
    }
});

const imageError = ref(false);

const formattedPrice = computed(() => {
    const price = Number(props.course.price);
    return isNaN(price) ? '0.00' : price.toFixed(2);
});

const statusClass = computed(() => {
    const map = {
        published: 'is-published',
        draft: 'is-draft',
        archived: 'is-archived'
    };
    return map[props.course.status] || 'is-default';
});

const enrollmentCount = computed(() => {
    return props.course.enrollment_count ?? 0;
});
</script>

<style scoped>
.course-card {
    display: flex;
    flex-direction: column;
    background: #ffffff;
    border: 1px solid #eef0f4;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.course-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.1);
}
.course-card__cover {
    position: relative;
    height: 168px;
    background: #f4f6fa;
}
.course-card__image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.course-card__placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.course-card__placeholder-text {
    color: #9aa3b2;
    font-size: 0.85rem;
}
.course-card__status {
    position: absolute;
    top: 12px;
    right: 12px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 600;
    color: #ffffff;
    text-transform: capitalize;
}
.course-card__status.is-published {
    background: #16a34a;
}
.course-card__status.is-draft {
    background: #d97706;
}
.course-card__status.is-archived {
    background: #6b7280;
}
.course-card__status.is-default {
    background: #475569;
}
.course-card__body {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 16px 18px 18px;
}
.course-card__title {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 600;
    color: #1f2937;
    line-height: 1.4;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.course-card__teacher {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.85rem;
}
.course-card__teacher-label {
    color: #9aa3b2;
}
.course-card__teacher-name {
    color: #475569;
    font-weight: 500;
}
.course-card__tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.course-card__tag {
    padding: 2px 9px;
    background: #f4f6fa;
    color: #475569;
    border-radius: 6px;
    font-size: 0.76rem;
}
.course-card__footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 4px;
}
.course-card__price {
    font-size: 1.2rem;
    font-weight: 700;
    color: #dc2626;
}
.course-card__enrollment {
    font-size: 0.8rem;
    color: #6b7280;
}
</style>
