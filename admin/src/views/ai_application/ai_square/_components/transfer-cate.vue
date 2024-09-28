<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            title="批量移动分类"
            :async="true"
            width="550px"
            @confirm="handleSubmit"
            @close="handleClose"
        >
            <el-form ref="formRef" :model="formData" label-width="84px" :rules="rules">
                <el-form-item label="选择分类" prop="category_id">
                    <el-select class="w-full" v-model="formData.category_id">
                        <el-option label="全部" value=""/>
                        <el-option
                            v-for="item in catePager.lists"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import type {FormInstance} from 'element-plus'
import Popup from '@/components/popup/index.vue'
import {
    getSquareCategoryAll,
    putVideoSquareTransferCategory,
    putDrawSquareTransferCategory,
    putMusicSquareTransferCategory
} from '@/api/ai_application/ai_square'

const emit = defineEmits(['success', 'close'])

const props = defineProps({
    type: {
        type: [Number, String],
        default: 1
    }
})

const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()

const catePager = reactive({
    loading: true,
    lists: []
})

const formData = reactive<{
    category_id: string,
    ids: string[] | number[],
}>({
    category_id: '',
    ids: []
})

//校验规则
const rules = {
    category_id: [
        {
            required: true,
            message: '请选择分类',
            trigger: ['blur']
        }
    ]
}

const handleSubmit = async () => {
    await formRef.value?.validate()
    if (props.type == 1) {
        await putDrawSquareTransferCategory(formData)
    } else if (props.type == 2) {
        await putMusicSquareTransferCategory(formData)
    } else if (props.type == 3) {
        await putVideoSquareTransferCategory(formData)
    }
    popupRef.value?.close()
    emit('success')
}

const getData = async () => {
    catePager.loading = true
    try {
        const lists = await getSquareCategoryAll({
            type: props.type
        })
        catePager.lists = lists
        catePager.loading = false
    } catch (error) {
        catePager.loading = false
        console.log('获取分类失败=>', error)
    }
}

const open = (ids: number[] | string[]) => {
    getData()
    formData.ids = ids
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

defineExpose({
    open,
    setFormData
})
</script>
