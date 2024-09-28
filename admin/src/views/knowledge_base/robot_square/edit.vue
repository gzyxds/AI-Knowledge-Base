<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            title="编辑"
            :async="true"
            width="550px"
            @confirm="handleSubmit"
            @close="handleClose"
        >
            <el-form class="ls-form" ref="formRef" :model="formData" label-width="100px">
                <el-form-item label="所属分类" prop="cid">
                    <el-select class="w-[280px]" v-model="formData.cid" clearable>
                        <el-option label="全部" :value="0" />
                        <el-option
                            v-for="item in cateLists"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="排序" prop="sort">
                    <div>
                        <el-input-number v-model="formData.sort" :min="0" :max="9999" />
                        <div class="form-tips">默认为0，数值越大越排前面</div>
                    </div>
                </el-form-item>
                <el-form-item label="是否显示" prop="is_show">
                    <el-switch v-model="formData.is_show" :active-value="1" :inactive-value="0" />
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance } from 'element-plus'
import Popup from '@/components/popup/index.vue'
import { putRobotSquare } from '@/api/knowledge_base/robot_square'
import { isEqual } from 'lodash-es'
const props = defineProps<{
    cateLists: any[]
}>()
const emit = defineEmits(['success', 'close'])
const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()

const formData = reactive({
    id: '',
    cid: '',
    sort: 0,
    is_show: 1
})

const handleSubmit = async () => {
    await formRef.value?.validate()
    await putRobotSquare(formData)
    popupRef.value?.close()
    emit('success')
}

const handleClose = () => {
    emit('close')
}

const open = () => {
    popupRef.value?.open()
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
