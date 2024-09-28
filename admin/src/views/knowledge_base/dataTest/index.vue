<template>
    <div style="height: calc(100vh - 130px)">
        <el-card class="!border-none h-full" shadow="never" :body-style="{ height: '100%' }">
            <div class="grid grid-cols-2 gap-4 h-full">
                <div>
                    <el-form label-width="90px">
                        <el-form-item label="选择知识库">
                            <el-select v-model="formData.kb_id" class="w-full" filterable>
                                <el-option
                                    v-for="(item, index) in drawDownList"
                                    :key="index"
                                    :label="item.name"
                                    :value="item.id"
                                />
                            </el-select>
                        </el-form-item>
                        <el-form-item label="测试文本">
                            <el-input v-model="formData.text" type="textarea" rows="20" />
                        </el-form-item>
                        <el-form-item>
                            <div>
                                <el-button
                                    :loading="isLock"
                                    @click="dataTestFn"
                                    :disabled="formData.text == '' || formData.kb_id == ''"
                                    type="primary"
                                >
                                    测试
                                </el-button>
                            </div>
                        </el-form-item>
                    </el-form>
                </div>
                <div
                    class="px-[10px] py-[5px] h-full min-h-0"
                    style="border-left: 1px solid #e2e2e2"
                >
                    <div
                        v-if="answerList.length == 0"
                        class="flex flex-col items-center justify-center h-full"
                    >
                        <el-image :src="empty" />
                        <div class="mt-[10px] text-[#5a646e]">测试结果将在这里展示</div>
                    </div>
                    <el-scrollbar>
                        <div v-if="answerList.length != 0">
                            <div
                                class="p-[10px] border border-solid border-br-light mb-[10px] rounded"
                                v-for="(item, index) in answerList"
                                :key="index"
                            >
                                <el-progress
                                    :percentage="Math.abs(item.distance / 1) * 100"
                                    color="var(--el-text-color-disabled)"
                                >
                                    <span>{{ Math.abs(item.distance).toFixed(5) }}</span>
                                </el-progress>
                                <div class="text-sm text-tx-secondary mt-[5px]">
                                    {{ item.question }}
                                </div>
                                <div class="text-sm text-tx-secondary">{{ item.answer }}</div>
                            </div>
                        </div>
                    </el-scrollbar>
                </div>
            </div>
        </el-card>
    </div>
</template>

<script setup lang="ts">
import empty from '@/assets/images/empty.png'
// import { knowKnowledgeAll } from '@/api/knowledge_training/manage'
import { konwledgeTest } from '@/api/knowledge_training/test'
import { useLockFn } from '@/hooks/useLockFn'

const formData = ref({
    kb_id: '',
    text: ''
})

const drawDownList: any = ref([])

const answerList = ref<any[]>([])

const getKnowledgeList = async () => {
    // drawDownList.value = await knowKnowledgeAll()
}

//数据测试
const dataTest = async () => {
    answerList.value = await konwledgeTest({ ...formData.value })
}

const { lockFn: dataTestFn, isLock } = useLockFn(dataTest)

onMounted(() => {
    getKnowledgeList()
})
</script>

<style scoped lang="scss"></style>
