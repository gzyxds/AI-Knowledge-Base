<template>
    <view class="h-full pt-[20rpx]">
        <view class="h-full bg-white flex flex-col pt-[20rpx]">
            <view class="flex justify-between items-center mb-[20rpx] px-[20rpx]">
                <view class="bg-page flex p-[8rpx] rounded-[16rpx] flex-none">
                    <view
                        v-for="item in tabState.list"
                        :key="item.type"
                        class="px-[34rpx] py-[12rpx] rounded-[16rpx]"
                        :class="{
                            'tab-active': item.type == tabState.current
                        }"
                        @click="tabsChange(item.type)"
                    >
                        {{ item.name }}
                    </view>
                </view>

                <view class="w-[250rpx] app-select-style" v-show="tabState.current == 'record'">
                    <app-select
                        v-model="isFeedback"
                        popupTitle="筛选反馈"
                        :dataLists="feedbackList"
                        placeholder="全部"
                        :closeable="false"
                        name="alias"
                        value="id"
                    >
                    </app-select>
                </view>
            </view>
            <view class="flex-1 min-h-0">
                <Data v-if="tabState.current == 'data'"/>
                <Record v-if="tabState.current == 'record'" :isFeedback="isFeedback"/>
            </view>
        </view>
    </view>
</template>
<script lang="ts">
export default {
    options: {
        virtualHost: true,
        styleIsolation: 'shared'
    },
    externalClasses: ['class']
}
</script>
<script setup lang="ts">
import {onMounted, reactive, ref, shallowRef} from 'vue'
import {useRoute} from 'uniapp-router-next'
import Data from './data.vue'
import Record from './record.vue'

const route = useRoute()
const isFeedback = ref<string | number>(-1)
const feedbackList = shallowRef([
    {id: -1, alias: '全部'},
    {id: 0, alias: '未反馈'},
    {id: 1, alias: '已反馈'}
])
const tabState = reactive({
    list: [
        {
            name: '对话数据',
            type: 'data'
        },
        {
            name: '对话记录',
            type: 'record'
        }
    ],
    current: 'data'
})

const tabsChange = (type: string) => {
    tabState.current = type
}

onMounted(() => {
    const dialogue = route.query.dialogue as string
    if (dialogue) {
        tabState.current = dialogue
    }
})
</script>
<style lang="scss" scoped>
.tab-active {
    background: linear-gradient(
            90deg,
            var(--color-minor) 0%,
            var(--color-primary) 100%
    );
    @apply text-white;
}

.app-select-style {
    :deep() {
        .input {
            height: 66rpx !important;
            min-height: 66rpx !important;
            line-height: 60rpx !important;
        }
    }
}
</style>
