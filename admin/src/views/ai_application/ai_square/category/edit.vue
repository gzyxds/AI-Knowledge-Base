<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            :title="popupTitle"
            :async="true"
            width="550px"
            @confirm="handleSubmit"
            @close="handleClose"
        >
            <el-form ref="formRef" :model="formData" label-width="84px" :rules="rules">
                <el-form-item label="分类名称" prop="name">
                    <el-input placeholder="请输入分类名称" v-model="formData.name"/>
                </el-form-item>
                <el-form-item label="英文名称" prop="type">
                    <el-radio-group v-model="formData.type">
                        <el-radio :label="1">AI绘画</el-radio>
                        <el-radio :label="2">AI音乐</el-radio>
                        <el-radio :label="3">AI视频</el-radio>
                    </el-radio-group>
                </el-form-item>

                <el-form-item label="排序">
                    <div class="flex-1">
                        <el-input class="ls-input" v-model="formData.sort"/>
                        <div class="form-tips">默认为0，数值越大排越前面</div>
                    </div>
                </el-form-item>
                <!-- 状态 -->
                <el-form-item label="状态" required>
                    <el-switch v-model="formData.status" :active-value="1" :inactive-value="0"/>
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import type {FormInstance} from 'element-plus'
import Popup from '@/components/popup/index.vue'
import {postSquareCategory, putSquareCategory} from '@/api/ai_application/ai_square'

const emit = defineEmits(['success', 'close'])

const props = defineProps({
    type: {
        type: [Number, String],
        default: 1
    }
})

const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const mode = ref('add')
const popupTitle = computed(() => {
    return mode.value == 'add' ? '新增分类' : '编辑分类'
})
const formData = reactive<{
    name: string,
    type: string | number,
    sort: number,
    status: number,
    id: string
}>({
    name: '',
    type: '',
    sort: 0,
    status: 1,
    id: ''
})

//校验规则
const rules = {
    name: [
        {
            required: true,
            message: '请输入名称',
            trigger: ['blur']
        }
    ],
    type: [
        {
            required: true,
            message: '请选择所属应用',
            trigger: ['blur']
        }
    ]
}

const handleSubmit = async () => {
    await formRef.value?.validate()
    mode.value == 'edit' ? await putSquareCategory(formData) : await postSquareCategory(formData)
    popupRef.value?.close()
    emit('success')
}

const open = (type = 'add') => {
    mode.value = type
    popupRef.value?.open()
}

const setFormData = async (row: any) => {
    for (const key in formData) {
        if (row[key] != null && row[key] != undefined) {
            //@ts-ignore
            formData[key] = row[key]
        }
    }
}

const handleClose = () => {
    emit('close')
}

watch(
    () => props.type,
    (val: number) => {
        formData.type = val * 1
    },
    {
        immediate: true
    }
)

defineExpose({
    open,
    setFormData
})
</script>
