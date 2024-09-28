<template>
    <view class="h-full">
        <view class="pt-[20rpx] bg-white h-full flex flex-col">
            <view class="flex mb-[20rpx] px-[20rpx]">
                <view class="p-[8rpx] bg-page flex rounded-[8rpx]">
                    <view
                        v-for="item in tabState.lists"
                        :key="item.type"
                        class="px-[30rpx] py-[9rpx] rounded-[8rpx]"
                        :class="{
                            'tab-active': item.type == tabState.current
                        }"
                        @click="tabState.current = item.type"
                    >
                        {{ item.name }}
                    </view>
                </view>
            </view>
            <view class="flex-1 min-h-0">
                <BaseSetting
                    class="h-full"
                    v-if="tabState.current === 'baseSetting'"
                />
            </view>
        </view>
    </view>
</template>

<script lang="ts" setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import BaseSetting from './base-setting.vue'
import { useKB } from '../useKb'

const { KBInfo, KBId } = useKB()

const formData: any = ref({
    name: '', //库的名称
    image: '', //封面图标
    intro: '', //知识库简介
    documents_model_id: '', //文件处理模型
    documents_model_sub_id: '', //文件处理模型
    embedding_model_id: '' //向量化的模型
})
const tabState = reactive({
    current: 'baseSetting',
    lists: [
        {
            type: 'baseSetting',
            name: '基础信息'
        }
    ]
})
onMounted(() => {
    formData.value = KBInfo.value
})
</script>

<style lang="scss" scoped>
.input {
    min-height: 70rpx;
    width: 100%;
    border-radius: 8rpx;
    line-height: 50rpx;
    border: 1px solid var(--color-light, #e5e5e5);
    &-placeholder {
        color: rgb(136, 136, 136);
    }
    padding: 10rpx 20rpx;
    box-sizing: border-box;
}

.tab-active {
    background: linear-gradient(
        90deg,
        var(--color-minor) 0%,
        var(--color-primary) 100%
    );
    @apply text-white;
}
</style>
