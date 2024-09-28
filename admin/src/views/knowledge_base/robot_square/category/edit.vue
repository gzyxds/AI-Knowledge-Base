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
            <el-form
                class="ls-form"
                ref="formRef"
                :rules="rules"
                :model="formData"
                label-width="100px"
            >
                <el-form-item label="分类图标" prop="image">
                    <material-picker v-model="formData.image" :limit="1" />
                </el-form-item>
                <el-form-item label="分类名称" prop="name">
                    <el-input
                        class="ls-input"
                        v-model="formData.name"
                        placeholder="请输入分类名称"
                        clearable
                    />
                </el-form-item>
                <el-form-item label="排序" prop="sort">
                    <div>
                        <el-input-number v-model="formData.sort" :min="0" :max="9999" />
                        <div class="form-tips">默认为0，数值越大越排前面</div>
                    </div>
                </el-form-item>
                <el-form-item label="状态" prop="sort">
                    <el-switch v-model="formData.is_enable" :active-value="1" :inactive-value="0" />
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance } from 'element-plus'
import Popup from '@/components/popup/index.vue'
import { postRobotCate, putRobotCate } from '@/api/knowledge_base/robot_square'
import { isEqual } from 'lodash-es'
const emit = defineEmits(['success', 'close'])
const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const mode = ref('add')
const popupTitle = computed(() => {
    return mode.value == 'edit' ? '编辑智能体分类' : '新增智能体分类'
})
const formData = reactive({
    id: '',
    name: '',
    image: '',
    sort: 0,
    is_enable: 1
})

const rules = {
    name: [
        {
            required: true,
            message: '请输入分类名称'
        }
    ]
}

const handleSubmit = async () => {
    await formRef.value?.validate()
    mode.value == 'edit' ? await putRobotCate(formData) : await postRobotCate(formData)
    popupRef.value?.close()
    emit('success')
}

const handleClose = () => {
    emit('close')
}

const open = (type = 'add') => {
    mode.value = type
    popupRef.value?.open()
    setTimeout(() => {
        if (type == 'add') {
            formRef.value?.resetFields()
        }
    }, 100)
}

const setFormData = async (data: Record<any, any>) => {
    for (const key in formData) {
        if (data[key] != null && data[key] != undefined) {
            if (!isEqual((formData as any)[key], data[key])) {
                ;(formData as any)[key] = data[key]
            }
        }
    }
}

defineExpose({
    open,
    setFormData
})
</script>
