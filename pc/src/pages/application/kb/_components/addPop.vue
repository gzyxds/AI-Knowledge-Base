<template>
    <Popup
        ref="popRef"
        :title="`${id != -1 ? '编辑' : '新增'}知识库`"
        width="500px"
        async
        @confirm="submitData"
    >
        <el-form ref="formRef" label-width="130px" :model="formData" :rules="rules">
            <!-- <el-form-item label="知识库类型" prop="type">
              <el-radio-group v-model="formData.type" :disabled="id != -1">
                <el-radio :label="1"> 文档型 </el-radio>
                <el-radio :label="2"> 问答型 </el-radio>
              </el-radio-group>
            </el-form-item> -->
            <el-form-item label="知识库名称" prop="name">
                <el-input
                    v-model="formData.name"
                    placeholder="请输入知识库名称"
                    class="w-[240px]"
                />
            </el-form-item>
            <el-form-item label="知识库简介">
                <el-input
                    type="textarea"
                    v-model="formData.intro"
                    placeholder="请用一句话描述知识库"
                    class="w-[240px]"
                    :rows="3"
                />
            </el-form-item>
            <el-form-item label="向量模型" prop="embedding_model_id">
                <template #label>
                    <el-popover
                        placement="right"
                        :width="300"
                        :show-arrow="false"
                        transition="custom-popover"
                        trigger="hover"
                        content="向量模型可以将自然语言转成向量(即数据训练), 用于进行语义检索, 注意: 不同向量模型无法一起使用, 选择完后将无法修改。">
                        <template #reference>
                            <div
                                class="flex items-center cursor-pointer text-[#666]">
                                <span class="mr-1">向量模型</span>
                                <Icon name="el-icon-QuestionFilled" :size="14" />
                            </div>
                        </template>
                    </el-popover>
                </template>
                <ModelPicker
                    class="flex-1"
                    v-model:id="formData.embedding_model_id"
                    :set-default="false"
                    type="vectorModels"
                    :disabled="id != -1"
                />
            </el-form-item>
            <el-form-item label="文件处理模型" prop="documents_model_sub_id">
                <template #label>
                    <el-popover
                        placement="right"
                        :width="300"
                        :show-arrow="false"
                        transition="custom-popover"
                        trigger="hover"
                        content="文件模型用于QA拆分功能(导入数据->自动拆分问答对), 利用该AI模型对导入的文本进行处理，最终拆分成一问一答的数据形式。">
                        <template #reference>
                            <div
                                class="flex items-center cursor-pointer text-[#666]">
                                <span class="mr-1">文件处理模型</span>
                                <Icon name="el-icon-QuestionFilled" :size="14" />
                            </div>
                        </template>
                    </el-popover>
                </template>
                <ModelPicker
                    class="flex-1"
                    v-model:id="formData.documents_model_id"
                    v-model:sub_id="formData.documents_model_sub_id"
                    :set-default="false"
                    disabled
                />
            </el-form-item>
            <el-form-item label="封面" prop="image">
                <!-- <MaterialPicker v-model="formData.image" /> -->
                <div>
                    <UploadImg v-model="formData.image"/>
                    <div class="form-tips">建议尺寸：200*160px</div>
                </div>
            </el-form-item>
            <!-- <el-form-item
              label="排序"
              prop="sort"
            >
              <el-input
                v-model="formData.sort"
                class="w-[240px]"
              />
            </el-form-item>
            <el-form-item
              label="状态"
              prop="is_enable"
            >
              <el-switch
                v-model="formData.is_enable"
                :active-value="1"
                :inactive-value="0"
              />
            </el-form-item> -->
        </el-form>
    </Popup>
</template>

<script setup lang="ts">
import type {FormInstance, FormRules} from 'element-plus'
import {
    knowKnowledgeAdd,
    knowKnowledgeDetail,
    knowKnowledgeEdit,
} from '@/api/my_database'
import {useAppStore} from "~/stores/app";

const appStore = useAppStore()

// //表单接口
interface IfromData {
    name: string
    image: string
    intro: string
    documents_model_id: string
    documents_model_sub_id: string
    embedding_model_id: string
    //   type: number
    //   sort: number
    //   is_enable: number
    id?: number
}

const emits = defineEmits(['close', 'success'])

//弹框ref
const popRef = shallowRef()
//表单ref
const formRef = shallowRef<FormInstance>()

//ID
const id = ref<number>(-1)

// //表单数据
const formData: IfromData = reactive({
    name: '', //库的名称
    image: '', //封面图标
    intro: '', //知识库简介
    documents_model_id: '', //文件处理模型
    documents_model_sub_id: '', //文件处理模型
    embedding_model_id: '' //向量化的模型
    //   type: 1, //库的类型 1-问答型 2-检索型
    //   sort: 0, //排序编号
    //   is_enable: 1 //是否启用 0-否 1-是
})

const rules = reactive<FormRules>({
    name: [
        {
            required: true,
            message: '请输入库的名称',
            trigger: 'change'
        }
    ],
    image: [
        {
            required: true,
            message: '请选择封面图标',
            trigger: 'change'
        }
    ],
    type: [
        {
            required: true,
            message: 'Please select Activity zone',
            trigger: 'change'
        }
    ],
    embedding_model_id: [
        {
            required: true,
            message: '请选择向量模型'
        }
    ],
    documents_model_id: [
        {
            required: true,
            message: '请选择文件处理通道'
        }
    ],
    documents_model_sub_id: [
        {
            required: true,
            message: '请选择文件处理模型'
        }
    ],
    sort: [
        {
            required: true,
            message: '请输入排序',
            trigger: 'change'
        }
    ],
    is_enable: [
        {
            required: true,
            message: '请选择库的状态',
            trigger: 'change'
        }
    ]
})

const router = useRouter()
//提交数据
const submitData = async () => {
    await formRef.value?.validate()

    if (id.value != -1) {
        await knowKnowledgeEdit({id: id.value, ...formData})
    } else {
        const {id} = await knowKnowledgeAdd({...formData})

        router.push({
            path: '/application/kb/detail',
            query: {
                id
            }
        })
    }

    emits('success')
    popRef.value.close()

    // emits('close')
}
//获取数据
const getData = async (id: number) => {
    const res = await knowKnowledgeDetail({id})
    Object.keys(res).map((item) => {
        //@ts-ignore
        formData[item] = res[item]
    })
}

//打开弹框
const open = async (option: any) => {
    formRef.value?.resetFields()
    id.value = -1
    popRef.value.open()
    if (option?.id) {
        id.value = option.id
        await getData(id.value)
    }
}

defineExpose({open})
</script>

<style scoped lang="scss"></style>
