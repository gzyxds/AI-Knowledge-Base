<template>
    <Popup ref="popRef" title="批量调整分类" async @confirm="submit">
        <el-form ref="formRef" :rules="formRules" :model="formData">
            <el-form-item label="所属分类" prop="category_id">
                <el-select v-model="formData.category_id">
                    <el-option
                        v-for="item in categoryList"
                        :key="item.id"
                        :label="item.name"
                        :value="item.id"
                    />
                </el-select>
            </el-form-item>
        </el-form>
    </Popup>
</template>

<script setup lang="ts">
import { getCategoryLists, bathEditSticker } from '@/api/digital_human/sticker'
import type { FormInstance } from 'element-plus'
const emits = defineEmits(['success'])

const popRef = shallowRef()

const ids = ref([])

const categoryList: any = ref([])
const formRef = shallowRef<FormInstance>()
const formData = ref({
    category_id: ''
})
const formRules = {
    category_id: [
        {
            required: true,
            message: '请选择分类'
        }
    ]
}
const open = (value: any) => {
    popRef.value.open()
    ids.value = value
    getCategory()
}

//获取分类
const getCategory = async () => {
    categoryList.value = await getCategoryLists()
}

const submit = async () => {
    await formRef.value?.validate()
    await bathEditSticker({ id: ids.value, ...formData.value })
    emits('success')
    popRef.value.close()
}

defineExpose({ open })
</script>

<style scoped lang="scss"></style>
