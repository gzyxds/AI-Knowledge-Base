<template>
    <u-popup
        v-model="showModel"
        mode="left"
        width="480rpx"
        safe-area-inset-bottom
    >
        <view class="flex flex-col h-full relative z-[9999]">
            <view class="px-[20rpx] py-[20rpx]">
                <u-button
                    type="primary"
                    :custom-style="{ width: '100%' }"
                    @click="emit('add')"
                >
                    + 新建会话
                </u-button>
            </view>
            <view class="flex-1 min-h-0">
                <scroll-view scroll-y class="h-full">
                    <view class="px-[20rpx]">
                        <view v-for="item in lists" :key="item.id">
                            <session-item
                                v-model="sessionId"
                                :item-id="item.id"
                                :name="item.name"
                                @edit="
                                    emit('edit', { id: item.id, name: $event })
                                "
                                @delete="emit('delete', item.id)"
                            />
                        </view>
                    </view>
                </scroll-view>
            </view>
            <view class="px-[20rpx] py-[20rpx]">
                <u-button
                    :custom-style="{ width: '100%' }"
                    @click="emit('clear')"
                >
                    删除所有会话
                </u-button>
            </view>
        </view>
    </u-popup>
</template>

<script setup lang="ts">
import { useVModels } from '@vueuse/core'
import SessionItem from './item.vue'
import { PropType } from 'vue'
const props = defineProps({
    lists: {
        type: Array as PropType<any[]>,
        default: () => []
    },
    show: {
        type: Boolean
    },
    modelValue: {
        type: [Number, String],
        required: true
    }
})
const emit = defineEmits<{
    (event: 'update:modelValue', value: boolean): void
    (event: 'add'): void
    (event: 'edit', data: any): void
    (event: 'delete', id: number): void
    (event: 'clear'): void
}>()
const { show: showModel, modelValue: sessionId } = useVModels(props, emit)
</script>
