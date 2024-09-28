<template>
    <view
        class="dub-item"
        :class="{
            'dub-item--is-active': isActive,
            'dub-item--placeholder': !value
        }"
        @tap="emit('click')"
    >
        <view class="flex-1 min-w-0 line-clamp-1">
            {{ value ? value : placeholder }}
        </view>
        <view
            class="flex mx-[10rpx]"
            v-if="value && closeable"
            @click.stop="emit('close')"
        >
            <u-icon name="close-circle" :size="36" />
        </view>
        <template v-if="url">
            <view class="flex" v-if="isPlaying" @click.stop="pause">
                <u-icon name="pause-circle" :size="36" />
            </view>
            <view class="flex" v-else @click.stop="play(url)">
                <u-icon name="play-circle" :size="36" />
            </view>
        </template>
    </view>
</template>

<script setup lang="ts">
import { useAudio } from '@/hooks/useAudio'

const props = defineProps<{
    isActive?: boolean
    value: string
    url?: string
    placeholder?: string
    closeable?: boolean
}>()
const emit = defineEmits<{
    (event: 'close'): void,
    (event: 'click'): void
}>()
const { play, pause, isPlaying } = useAudio()
</script>

<style lang="scss" scoped>
.dub-item {
    padding: 5rpx 0.625rem;
    border-color: rgb(220, 223, 230);
    text-align: left;
    border-radius: 4px;
    border: 1px solid var(--color-light, #e5e5e5);
    position: relative;
    min-height: 70rpx;

    flex: 1;
    display: flex;
    flex-direction: row;
    &--placeholder {
        @apply text-muted;
    }
    &--is-active {
        @apply text-primary border-primary;
    }
}
</style>
