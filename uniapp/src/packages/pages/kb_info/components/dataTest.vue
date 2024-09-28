<template>
    <scroll-view scroll-y class="h-full bg-white">
        <view class="p-[20rpx]">
            <view
                class="p-[20rpx] border border-solid border-[#DCDFE6] rounded-lg bg-[#F7F8F9]"
            >
                <textarea
                    v-model="formData.question"
                    placeholder="请输入需要搜索的内容"
                />
            </view>
            <view class="mt-4">
                <button class="bg-primary text-white" @click="test">
                    测试
                </button>
            </view>
            <view
                class="flex flex-col items-center justify-center py-[40rpx]"
                v-if="answerList.length == 0"
            >
                <image class="h-[240rpx] w-[240rpx]" :src="testResult"></image>
                <view>测试结果将在这里展示</view>
            </view>
            <view class="mt-4">
                <div v-if="answerList.length != 0">
                    <div
                        v-for="(item, index) in answerList"
                        :key="index"
                        class="p-[10px] border border-solid border-[#e3e3e3] mb-[10px] rounded"
                    >
                        <u-line-progress
                            :percent="Math.abs(item.score / 1) * 100"
                            :show-percent="true"
                        >
                            <div>{{ item.score }}</div>
                        </u-line-progress>
                        <!-- <el-progress
                        :percentage="Math.abs(item.score / 1) * 100"
                        color="var(--el-text-color-disabled)"
                    >
                        <span>{{ Math.abs(item.score).toFixed(5) }}</span>
                    </el-progress> -->
                        <div
                            class="text-sm text-tx-secondary mt-[5px] whitespace-pre-line"
                        >
                            {{ item.question }}
                        </div>
                        <div
                            class="text-sm text-tx-secondary whitespace-pre-line"
                        >
                            {{ item.answer }}
                        </div>
                    </div>
                </div>
            </view>
        </view>
    </scroll-view>
</template>

<script lang="ts" setup>
import testResult from '@/packages/static/images/testResult.png'
import { dataTest } from '@/api/kb'
import { ref } from 'vue'
import { useKB } from '../useKb'

const { KBId } = useKB()

const answerList = ref<any[]>([])

const formData = ref({
    kb_id: KBId.value,
    question: ''
})

//数据测试
const test = async () => {
    answerList.value = await dataTest({ ...formData.value })
}
</script>

<style lang="scss" scoped></style>
