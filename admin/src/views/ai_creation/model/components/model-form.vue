<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-page-header :content="headerTitle" @back="$router.back()" />
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <el-form
                class="ls-form"
                ref="formRef"
                :rules="rules"
                :model="formData"
                label-width="120px"
            >
                <el-form-item label="图标" prop="image">
                    <material-picker v-model="formData.image" :limit="1" />
                </el-form-item>
                <el-form-item label="模型名称" prop="name">
                    <div class="w-[380px]">
                        <el-input placeholder="请输入模型名称" v-model="formData.name" />
                    </div>
                </el-form-item>
                <el-form-item label="模型描述" prop="tips">
                    <div class="w-[380px]">
                        <el-input
                            placeholder="请输入模型描述"
                            type="textarea"
                            :rows="2"
                            v-model="formData.tips"
                        />
                    </div>
                </el-form-item>
                <el-form-item label="所属类目" prop="category_id">
                    <div class="w-[380px]">
                        <el-select
                            v-model="formData.category_id"
                            placeholder="请选择所属类目"
                            class="w-full"
                        >
                            <el-option
                                v-for="(item, index) in optionsData.categoryList"
                                :key="index"
                                :label="item.name"
                                :value="item.id"
                            />
                        </el-select>
                    </div>
                </el-form-item>
                <!-- 全局指令 -->
                <el-form-item label="全局指令" prop="system">
                    <div class="w-[380px]">
                        <el-input
                            placeholder="如果填写了该全局指令则会替换对话设置的全局指令"
                            :rows="3"
                            type="textarea"
                            v-model="formData.system"
                        />
                    </div>
                </el-form-item>
                <el-form-item label="调教文案" prop="content">
                    <div>
                        <div class="w-[380px]">
                            <el-input
                                ref="elInputRef"
                                placeholder="请输入调教文案"
                                type="textarea"
                                :rows="4"
                                v-model="formData.content"
                            />
                        </div>
                        <div class="flex flex-wrap mx-[-5px]">
                            <div
                                class="mx-[5px] mt-[10px]"
                                v-for="(item, index) in formData.form"
                                :key="index"
                            >
                                <ElButton @click="insertAFormField(item.props.field)">
                                    <span class="max-w-[100px]">
                                        <OverflowTooltip :content="item.props.title" />
                                    </span>
                                </ElButton>
                            </div>
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="模型参数">
                    <div class="flex flex-wrap max-w-[700px]">
                        <div class="w-[200px] mr-[20px] mb-[20px]">
                            <div class="flex items-center text-tx-regular text-xs">
                                <span class="mr-[4px] mt-[2px]">回复条数</span>
                                <el-tooltip
                                    class="box-item"
                                    effect="dark"
                                    content="为每个输入消息生成多个回复，取值范围为1~5之间的整数。"
                                    placement="top"
                                >
                                    <el-icon size="16px"><QuestionFilled /></el-icon>
                                </el-tooltip>
                            </div>

                            <el-slider v-model="formData.n" :min="1" :max="5" />
                        </div>
                        <div class="w-[200px] mr-[20px] mb-[20px]">
                            <div class="flex items-center text-tx-regular text-xs">
                                <span class="mr-[4px] mt-[2px]">词汇属性</span>
                                <el-tooltip
                                    class="box-item"
                                    effect="dark"
                                    content="用于控制生成文本的随机性，取值范围为0~1之间的浮点数，建议取值0.7左右。"
                                    placement="top"
                                >
                                    <el-icon size="16px"><QuestionFilled /></el-icon>
                                </el-tooltip>
                            </div>

                            <el-slider
                                v-model="formData.temperature"
                                :min="0"
                                :max="1"
                                :step="0.1"
                            />
                        </div>

                        <div class="w-[200px] mr-[20px] mb-[20px]">
                            <div class="flex items-center text-tx-regular text-xs">
                                <span class="mr-[4px] mt-[2px]">随机属性</span>
                                <el-tooltip
                                    class="box-item"
                                    effect="dark"
                                    content="用于控制生成文本的多样性，取值范围为0~1之间的浮点数，建议取值0.9左右。"
                                    placement="top"
                                >
                                    <el-icon size="16px"><QuestionFilled /></el-icon>
                                </el-tooltip>
                            </div>
                            <el-slider v-model="formData.top_p" :min="0" :max="1" :step="0.1" />
                        </div>
                        <div class="w-[200px] mr-[20px] mb-[20px]">
                            <div class="flex items-center text-tx-regular text-xs">
                                <span class="mr-[4px] mt-[2px]">话题属性</span>
                                <el-tooltip
                                    class="box-item"
                                    effect="dark"
                                    content="用于控制生成文本中是否出现给定的关键词，取值范围为0~1之间的浮点数，建议取值0.5左右。"
                                    placement="top"
                                >
                                    <el-icon size="16px"><QuestionFilled /></el-icon>
                                </el-tooltip>
                            </div>

                            <el-slider
                                v-model="formData.presence_penalty"
                                :step="0.1"
                                :min="0"
                                :max="1"
                            />
                        </div>
                        <!-- <div class="w-[200px] mr-[20px] mb-[20px]">
                            <div class="flex items-center text-tx-regular text-xs">
                                <span class="mr-[4px] mt-[2px]">重复属性</span>
                                <el-tooltip
                                    class="box-item"
                                    effect="dark"
                                    content="用于控制生成文本中重复的程度，取值范围为0~1之间的浮点数，建议取值0.5左右"
                                    placement="top"
                                >
                                    <el-icon size="16px"><QuestionFilled /></el-icon>
                                </el-tooltip>
                            </div>
                            <el-slider
                                v-model="formData.frequency_penalty"
                                :step="0.1"
                                :min="0"
                                :max="1"
                            />
                        </div> -->
                    </div>
                </el-form-item>
                <el-form-item label="表单内容" prop="form">
                    <div class="flex-1 min-w-0 max-w-[1000px]">
                        <div class="flex">
                            <el-button type="primary" @click="handleAdd">添加</el-button>
                            <el-popover placement="right" :width="300" trigger="hover">
                                <template #reference>
                                    <el-button type="primary" link>查看示例</el-button>
                                </template>
                                <img src="../images/example.png" />
                            </el-popover>
                        </div>
                        <div class="mt-4">
                            <el-table
                                ref="tableRef"
                                size="large"
                                class="mt-4"
                                row-key="id"
                                :data="formData.form"
                            >
                                <el-table-column width="50">
                                    <template #default>
                                        <div class="move-icon cursor-move">
                                            <Icon name="el-icon-Rank" />
                                        </div>
                                    </template>
                                </el-table-column>
                                <el-table-column label="字段值" min-width="120">
                                    <template #default="{ row }">
                                        <span>{{ row.props.field }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column
                                    label="字段标题"
                                    prop="props[title]"
                                    min-width="200"
                                />
                                <el-table-column label="类型" prop="title" min-width="100" />
                                <el-table-column
                                    label="是否必填"
                                    prop="props[isRequired]"
                                    min-width="100"
                                >
                                    <template #default="{ row }">
                                        <el-switch v-model="row.props.isRequired" />
                                    </template>
                                </el-table-column>

                                <el-table-column label="操作" width="120" fixed="right">
                                    <template #default="{ row, $index }">
                                        <el-button
                                            type="primary"
                                            link
                                            @click="handleEdit(row, $index)"
                                        >
                                            编辑
                                        </el-button>
                                        <el-button
                                            type="primary"
                                            link
                                            @click="handleDelete($index)"
                                        >
                                            删除
                                        </el-button>
                                    </template>
                                </el-table-column>
                            </el-table>
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="虚拟使用人数" prop="virtual_use_num">
                    <el-input-number v-model="formData.virtual_use_num" />
                </el-form-item>
                <el-form-item label="排序" prop="sort">
                    <div>
                        <el-input-number v-model="formData.sort" />
                        <div class="form-tips">默认为0，数值越大越排前面</div>
                    </div>
                </el-form-item>
                <el-form-item label="状态" prop="sort">
                    <el-switch v-model="formData.status" :active-value="1" :inactive-value="0" />
                </el-form-item>
            </el-form>
        </el-card>
        <form-designer-popup ref="formDesignerRef" @add="handleFormAdd" @edit="handleFormEdit" />
        <footer-btns>
            <el-button type="primary" @click="handleSave">保存</el-button>
        </footer-btns>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance, FormRules, InputInstance } from 'element-plus'
import { getCreationCategoryList } from '@/api/ai_creation'
import { useThrottleFn } from '@vueuse/core'
import feedback from '@/utils/feedback'
import Sortable from 'sortablejs'
import { setRangeText } from '@/utils/dom'
import { useDictOptions } from '@/hooks/useDictOptions'
const formRef = shallowRef<FormInstance>()

const props = defineProps<{
    headerTitle: string
    modelValue: Record<string, any>
}>()
const emit = defineEmits<{
    (event: 'update:modelValue', value: Record<string, any>): void
    (event: 'submit'): void
}>()

const formData = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
})
const tableRef = shallowRef()
const initSortable = () => {
    const el = tableRef.value.$el.querySelector('.el-table__body tbody')
    Sortable.create(el, {
        animation: 150,
        handle: '.move-icon',
        onEnd: ({ newIndex, oldIndex }: any) => {
            const arr = formData.value.form
            const currRow = arr.splice(oldIndex, 1)[0]
            arr.splice(newIndex, 0, currRow)
            formData.value.form = []
            nextTick(() => {
                formData.value.form = arr
            })
        }
    })
}
const elInputRef = shallowRef<InputInstance>()
const insertAFormField = (field: string) => {
    formData.value.content = setRangeText(elInputRef.value?.textarea!, `\${${field}}`)
}
//表单校验规则
const rules: FormRules = {
    name: [
        {
            required: true,
            message: '请输入名称'
        }
    ],
    tips: [
        {
            required: true,
            message: '请输入模型描述'
        }
    ],
    content: [
        {
            required: true,
            message: '请输入调教文案'
        }
    ],
    category_id: [
        {
            required: true,
            message: '请选择所属类目'
        }
    ],
    image: [
        {
            required: true,
            message: '请选择模型图标'
        }
    ],
    form: [
        {
            type: 'array',
            required: true,
            message: '请添加表单内容'
        }
    ]
}
const formDesignerRef = shallowRef()
//添加
const handleAdd = async () => {
    await nextTick()
    formDesignerRef.value?.open('add')
}
const handleFormAdd = useThrottleFn((value: any) => {
    const isSameField = !!formData.value.form.find(
        (item: any) => item.props.field === value.props.field
    )
    if (isSameField) {
        return feedback.msgError('字段值重复，请修改字段值')
    }
    formData.value.form.push(value)
    formDesignerRef.value?.close()
})
const currentIndex = ref(0)
const handleEdit = (value: any, index: number) => {
    currentIndex.value = index
    formDesignerRef.value?.open('edit')
    formDesignerRef.value?.setFormData(value)
}

const handleDelete = async (index: number) => {
    await feedback.confirm('确定删除当前项？')
    formData.value.form.splice(index, 1)
}

const handleFormEdit = useThrottleFn((value: any) => {
    formData.value.form[currentIndex.value] = value
    formDesignerRef.value?.close()
})

const { optionsData } = useDictOptions<{ categoryList: any[] }>({
    categoryList: {
        api: getCreationCategoryList,
        params: {
            page_type: 0
        },
        transformData(data) {
            return data.lists
        }
    }
})

//提交
const handleSave = async () => {
    await formRef.value?.validate()
    emit('submit')
}

onMounted(() => {
    nextTick(() => {
        initSortable()
    })
})
</script>
