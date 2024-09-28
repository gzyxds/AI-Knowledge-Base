<template>
    <Popup
        ref="popRef"
        :title="formData.id ? '编辑前景' : '新增前景'"
        width="600px"
        async
        @confirm="submit"
        :confirm-loading="confirmLoading"
        @close="$emit('close')"
    >
        <el-form label-width="90px" ref="formRef" :model="formData" :rules="rules">
            <el-form-item label="前景图" prop="cover">
                <div>
                    <MaterialPicker
                        :limit="formData.id ? 1 : 10"
                        v-model="formData.url"
                        :data="{ use_type: 2 }"
                    />
                    <div class="form-tips">批量添加一次最多支持10张</div>
                </div>
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
import { shallowRef } from 'vue'
import { addFG, editFG } from '@/api/digital_human/fg'

const emits = defineEmits(['success', 'close'])

const formRef = shallowRef()

const confirmLoading = ref(false)
const popRef = shallowRef()
const formData = ref({
    id: '',
    status: 1,
    sort: '0',
    url: []
})

const rules: FormRules = {
    music: [{ required: true }],
    cover: [{ required: true }]
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
}

//提交
const submit = async () => {
    try {
        confirmLoading.value = true
        if (formData.value.id) {
            await editFG({ ...formData.value })
        } else {
            await addFG({ ...formData.value })
        }
        emits('success')
        popRef.value.close()
    } catch (error) {
        confirmLoading.value = false
    }
}

defineExpose({ open })
</script>

<style scoped lang="scss"></style>
