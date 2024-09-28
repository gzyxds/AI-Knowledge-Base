<template>
    <div class="chat-msg-item" :style="bg ? `--item-bg: ${bg}` : ''">
        <div :class="`chat-msg-item__${type}`">
            <img v-if="avatar" class="chat-msg-item__avatar" :src="avatar" />
            <div :class="[`chat-msg-item__${type}-wrap`, { 'has-time': time }]">
                <div
                    v-if="time"
                    class="h-[20px] mb-[10px] text-tx-secondary text-xs"
                >
                    {{ time }}
                    <el-tag
                        v-if="modelName && appStore.getChatConfig.is_show_model"
                        class="ml-2"
                        type="success"
                        style="--el-tag-border-color: transparent"
                    >
                        {{ modelName }}
                    </el-tag>
                </div>

                <div :class="`chat-msg-item__${type}-content`">
                    <slot />
                </div>
                <div>
                    <slot name="actions" />
                </div>
            </div>
        </div>
        <slot name="outer_actions"></slot>
    </div>
</template>
<script setup lang="ts">
import { useAppStore } from '@/stores/app'
const props = withDefaults(
    defineProps<{
        type: 'left' | 'right'
        avatar?: string
        bg?: string
        color?: string
        time?: string
        modelName?: string
    }>(),
    {
        type: 'left',
        avatar: '',
        bg: '',
        color: 'black',
        time: '',
        modelName: ''
    }
)
const appStore = useAppStore()
</script>
<style lang="scss" scoped>
.chat-msg-item {
    $-avatar-size: 40;
    $-triangle-size: 8;
    $-triangle-position: -#{calc($-triangle-size * 2 - 1)}px;
    $-item-wrap-margin: #{$-triangle-size * 1.5}px;
    $-item-left-bg: var(--item-bg, #fff);
    $-item-right-bg: var(--item-bg, var(--el-color-primary));
    color: v-bind(color);
    &__avatar {
        flex: none;
        width: #{$-avatar-size}px;
        height: #{$-avatar-size}px;
        overflow: hidden;
        border-radius: 50%;
    }
    &__left,
    &__right {
        display: flex;
        align-items: flex-start;

        &-wrap {
            display: inline-block;
            max-width: calc(100% - #{$-avatar-size + $-triangle-size * 1.5}px);
            position: relative;
            display: flex;
            flex-direction: column;
            //&::before {
                //content: '';
                //display: block;
                //width: 0;
                //height: 0;
                //position: absolute;
                //top: #{calc(($-avatar-size - $-triangle-size * 2) / 2)}px;
                //border: #{$-triangle-size}px solid transparent;
            //}
            &.has-time {
                &::before {
                    // top: #{calc(($-avatar-size - $-triangle-size * 2) / 2 + 30)}px;
                    display: none;
                }
            }
        }
    }
    &__right {
        flex-direction: row-reverse;
        @apply lg:ml-[40px] sm:ml-[20px];
    }
    &__left {
        @apply lg:mr-[40px] sm:mr-[20px];
    }
    &__left-wrap {
        margin-left: $-item-wrap-margin;

        &::before {
            left: $-triangle-position;
            border-right-color: $-item-left-bg;
        }
    }
    &__right-wrap {
        margin-right: $-item-wrap-margin;
        align-items: flex-end;
        &::before {
            right: $-triangle-position;
            border-left-color: $-item-right-bg;
        }
    }
    &__left-content,
    &__right-content {
        max-width: 900px;
        min-width: 40px;
        min-height: 40px;
        padding: 10px 20px;
    }
    &__left-content {
        border-radius: 0 12px 12px 12px;
        background-color: $-item-left-bg;
    }

    &__right-content {
        border-radius: 12px 0 12px 12px;
        background-color: $-item-right-bg;
    }
}
</style>
