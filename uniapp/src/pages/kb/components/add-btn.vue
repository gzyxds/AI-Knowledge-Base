<template>
    <dragon-button :size="88" :xEdge="10" :yEdge="yEdge">
        <view class="add-btn" @click="emit('click')">
            <u-icon name="plus" :size="40" />
        </view>
    </dragon-button>
</template>

<script setup lang="ts">
const props = withDefaults(
    defineProps<{
        yEdge: number
    }>(),
    {
        yEdge: 200
    }
)

const emit = defineEmits<{
    (event: 'click'): void
}>()
</script>

<style lang="scss">
.add-btn {
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 50%;
    width: 88rpx;
    height: 88rpx;
    background: linear-gradient(
        90deg,
        var(--color-minor) 0%,
        var(--color-primary) 100%
    );
    filter: drop-shadow(0 4px 10px #00000029);
    @apply text-white;
}
</style>
