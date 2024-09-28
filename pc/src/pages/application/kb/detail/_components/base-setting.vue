<template>
    <div class="h-full">
        <ElScrollbar>
            <el-form label-position="top" class="setup-form">
                <el-form-item label="知识库名称">
                    <el-input
                        placeholder="知识库名称"
                        v-model="formData.name"
                    ></el-input>
                </el-form-item>
                <el-form-item label="简介">
                    <el-input
                        type="textarea"
                        placeholder="知识库名称"
                        :rows="5"
                        resize="none"
                        v-model="formData.intro"
                    ></el-input>
                </el-form-item>
                <el-form-item label="向量模型">
                    <template #label>
                        <el-popover
                            placement="right"
                            :width="300"
                            :show-arrow="false"
                            transition="custom-popover"
                            trigger="hover"
                            content="向量模型可以将自然语言转成向量(即数据训练), 用于进行语义检索, 注意: 不同向量模型无法一起使用, 选择完后将无法修改。"
                        >
                            <template #reference>
                                <div
                                    class="flex items-center cursor-pointer text-[#666]"
                                >
                                    <span class="mr-1">向量模型</span>
                                    <Icon
                                        name="el-icon-QuestionFilled"
                                        :size="14"
                                    />
                                </div>
                            </template>
                        </el-popover>
                    </template>
                    <ModelPicker
                        class="flex-1"
                        v-model:id="formData.embedding_model_id"
                        :set-default="false"
                        type="vectorModels"
                        :disabled="true"
                    />
                </el-form-item>
                <el-form-item label="文件处理模型">
                    <template #label>
                        <el-popover
                            placement="right"
                            :width="300"
                            :show-arrow="false"
                            transition="custom-popover"
                            trigger="hover"
                            content="文件模型用于QA拆分功能(导入数据->自动拆分问答对), 利用该AI模型对导入的文本进行处理，最终拆分成一问一答的数据形式。"
                        >
                            <template #reference>
                                <div
                                    class="flex items-center cursor-pointer text-[#666]"
                                >
                                    <span class="mr-1">文件处理模型</span>
                                    <Icon
                                        name="el-icon-QuestionFilled"
                                        :size="14"
                                    />
                                </div>
                            </template>
                        </el-popover>
                    </template>
                    <ModelPicker
                        class="flex-1"
                        v-model:id="formData.documents_model_id"
                        v-model:sub_id="formData.documents_model_sub_id"
                        :set-default="false"
                    />
                </el-form-item>
                <el-form-item label="设置封面">
                    <upload-img v-model="formData.image"></upload-img>
                </el-form-item>
                <el-form-item>
                    <el-button
                        type="primary"
                        :disabled="data.power >= 2"
                        @click="submit"
                    >
                        保存设置
                    </el-button>
                    <el-button
                        @click="delDatabase"
                        :disabled="data.power !== 1"
                    >
                        删除
                    </el-button>
                </el-form-item>
            </el-form>
        </ElScrollbar>
    </div>
</template>

<script setup lang="ts">
import { knowKnowledgeEdit, knowKnowledgeDel } from '@/api/my_database'
const props = withDefaults(
    defineProps<{
        data: Record<string, any>
    }>(),
    {
        data: () => ({})
    }
)
const emits = defineEmits(['update'])

const route = useRoute()
const router = useRouter()

//表单接口
interface IfromData {
    name: string
    image: string
    intro: string
    documents_model_id: string
    documents_model_sub_id: string
    embedding_model_id: string
    id?: number
    is_owner: number
}

//表单数据
const formData: IfromData = reactive({
    name: '', //库的名称
    image: '', //封面图标
    intro: '', //知识库简介
    documents_model_id: '', //文件处理模型
    documents_model_sub_id: '',
    embedding_model_id: '', //向量化的模型
    is_owner: 1
})

watch(
    () => props.data,
    (value) => {
        Object.assign(formData, value)
    }
)

const submit = async () => {
    await knowKnowledgeEdit({ ...formData, id: route.query.id })
    emits('update')
}

//删除数据库
const delDatabase = async () => {
    await feedback.confirm(`确认删除吗？`)
    await knowKnowledgeDel({ id: route.query.id })
    router.push('/application/layout/kb')
}
</script>

<style scoped lang="scss">
.setup-form {
    :deep(.el-form-item__label) {
        display: inline-block;
    }
}
</style>
