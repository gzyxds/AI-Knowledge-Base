<template>
    <Popup
        ref="popRef"
        :title="formData.id ? '编辑音乐' : '新增音乐'"
        width="600px"
        async
        @confirm="submit"
        @close="$emit('close')"
        :confirm-loading="confirmLoading"
    >
        <el-form label-width="90px" ref="formRef" :model="formData" :rules="rules">
            <el-form-item label="音乐封面" prop="cover">
                <MaterialPicker :limit="1" v-model="formData.cover" />
            </el-form-item>
            <el-form-item label="音乐" prop="music">
                <div class="flex items-center">
                    <MaterialPicker
                        v-model="formData.url"
                        @change="musicChange"
                        type="audio"
                        :limit="1"
                        with-name
                        :data="{ use_type: 2 }"
                    />
                    <div>{{ withNameList[0]?.name }}</div>
                </div>
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
                <el-switch v-model="formData.status" :active-value="1" :inactive-value="0" />
            </el-form-item>
        </el-form>
    </Popup>
</template>

<script setup lang="ts">
import type { FormRules } from 'element-plus'
import { addMusic, getCategoryLists, editMusic } from '@/api/digital_human/music'
import { shallowRef } from 'vue'

const emits = defineEmits(['success', 'close'])

const formRef = shallowRef()

const confirmLoading = ref(false)
const popRef = shallowRef()
const formData = ref({
    id: '',
    cover: '',
    status: 1,
    sort: '0',
    url: '',
    category_id: 0
})

const withNameList: any = ref([])

//分类列表
const categoryList = ref()

//获取分类列表
const getCategoryList = async () => {
    categoryList.value = await getCategoryLists()
}

const rules: FormRules = {
    music: [{ required: true }],
    cover: [{ required: true }]
}

//选择音频修改
const musicChange = async (value: any) => {
    await nextTick()
    withNameList.value = value
    console.log(value)
}

//提交
const submit = async () => {
    try {
        confirmLoading.value = true
        if (formData.value.id) {
            await editMusic({ ...formData.value })
        } else {
            await addMusic({ ...formData.value })
        }
        emits('success')
    } catch (error) {
        confirmLoading.value = false
    }
}

const open = async (row?: any) => {
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
