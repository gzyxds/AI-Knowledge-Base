<template>
    <Popup
        ref="popRef"
        :title="formData.id ? '编辑背景' : '新增背景'"
        width="600px"
        async
        @confirm="submit"
        @close="$emit('close')"
        :confirm-loading="confirmLoading"
    >
        <el-form label-width="90px" ref="formRef" :model="formData" :rules="rules">
            <el-form-item label="背景图" prop="url">
                <div>
                    <MaterialPicker
                        :limit="formData.id ? 1 : 10"
                        v-model="formData.url"
                        :data="{ use_type: 2 }"
                    />
                    <div class="form-tips">背景图要求：横版：1920*1080，竖版：1080*1920</div>
                </div>
            </el-form-item>
            <el-form-item label="选择版型" prop="type">
                <el-radio-group v-model="formData.type">
                    <el-radio :label="1">竖版</el-radio>
                    <el-radio :label="2">横版</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="所属分类">
                <el-select v-model="formData.category_id">
                    <el-option label="无分类" :value="0" />
                    <el-option
                        v-for="item in categoryList"
                        :label="item.name"
                        :key="item.id"
                        :value="item.id"
                    />
                </el-select>
            </el-form-item>
            <el-form-item label="排序">
                <div>
                    <el-input v-model="formData.sort" class="w-[320px]" />
                    <div class="form-tips">默认为0，数值越大越排前面</div>
                </div>
            </el-form-item>
            <el-form-item label="状态">
                <el-switch :active-value="1" :inactive-value="0" v-model="formData.status" />
            </el-form-item>
        </el-form>
    </Popup>
</template>

<script setup lang="ts">
import type { ElForm, FormRules } from 'element-plus'
import { shallowRef } from 'vue'
import { addBG, getCategoryLists, editBG } from '@/api/digital_human/bg'
import { isString } from 'lodash-es'

const emits = defineEmits(['success', 'close'])

const formRef = shallowRef<InstanceType<typeof ElForm>>()
const popRef = shallowRef()
const confirmLoading = ref(false)
const formData = ref({
    id: '',
    status: 1,
    sort: '0',
    url: [] as string[],
    category_id: 0,
    type: 1 //1-竖屏 2-横屏
})

const rules: FormRules = {
    type: [{ required: true }],
    url: [{ required: true, message: '背景图不能为空！', trigger: ['change', 'blur'] }]
}

//分类列表
const categoryList = ref()

//获取分类列表
const getCategoryList = async () => {
    categoryList.value = await getCategoryLists()
}

//提交
const submit = async () => {
    try {
        confirmLoading.value = true
        await formRef.value?.validate()
        const params = {
            ...formData.value
        }

        if (formData.value.id) {
            await editBG(params)
        } else {
            await addBG(params)
        }
        emits('success')
    } catch (error) {
        confirmLoading.value = false
    }
}

const open = async (row: any) => {
    popRef.value.open()
    if (row) {
        Object.keys(formData.value).map((item) => {
            //@ts-ignore
            formData.value[item] = row[item]
        })
        formData.value.id = row.id
    }

    await getCategoryList()
}

defineExpose({ open })
</script>

<style scoped lang="scss"></style>
