<template>
    <el-scrollbar>
        <div class="p-main">
            <div class="grid lg:grid-cols-2 gap-4 grid-cols-1 h-full">
                <div>
                    <el-form label-width="90px">
                        <el-form-item label="测试文本">
                            <el-input
                                v-model="formData.question"
                                type="textarea"
                                rows="20"
                            />
                        </el-form-item>
                        <el-form-item>
                            <div>
                                <el-button
                                    :loading="isLock"
                                    :disabled="
                                        formData.question == '' ||
                                        formData.kb_id == ''
                                    "
                                    type="primary"
                                    @click="dataTestFn"
                                >
                                    测试
                                </el-button>
                            </div>
                        </el-form-item>
                    </el-form>
                </div>
                <div class="px-[10px] py-[5px] h-full lg:borderLeft">
                    <div
                        v-if="answerList.length == 0"
                        class="flex flex-col items-center justify-center h-full"
                    >
                        <el-image :src="empty" />
                        <div class="mt-[10px] text-[#5a646e]">
                            测试结果将在这里展示
                        </div>
                    </div>
                    <el-scrollbar>
                        <div v-if="answerList.length != 0">
                            <div
                                v-for="(item, index) in answerList"
                                :key="index"
                                class="p-[10px] border border-solid border-br-light mb-[10px] rounded"
                            >
                                <el-progress
                                    :percentage="Math.abs(item.score / 1) * 100"
                                    color="var(--el-text-color-disabled)"
                                >
                                    <span>{{
                                        Math.abs(item.score).toFixed(5)
                                    }}</span>
                                </el-progress>
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
                    </el-scrollbar>
                </div>
            </div>
        </div>
    </el-scrollbar>
</template>

<script setup lang="ts">
import empty from '@/assets/image/empty.png'

import { useLockFn } from '@/composables/useLockFn'
import { itemDatatest } from '@/api/my_database'

const props = defineProps({
    id: {
        type: Number,
        default: 0
    },
    type: {
        type: String,
        default: ''
    }
})

const answerList = ref<any[]>([])

const formData = ref({
    kb_id: props.id,
    question: ''
})

//数据测试
const dataTest = async () => {
    answerList.value = await itemDatatest({ ...formData.value })
}

const { lockFn: dataTestFn, isLock } = useLockFn(dataTest)
</script>

<style scoped lang="scss">
.borderLeft {
    border-left: 1px solid #e2e2e2;
}
</style>
