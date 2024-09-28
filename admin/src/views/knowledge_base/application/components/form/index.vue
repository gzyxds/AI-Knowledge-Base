<template>
    <el-card class="!border-none" shadow="never">
        <el-page-header :content="headerTitle" @back="$router.back()" />
    </el-card>
    <el-card class="!border-none mt-4" shadow="never">
        <el-form
            ref="formRef"
            :model="modelValue"
            label-width="120px"
            :rules="formRules"
            :disabled="true"
        >
            <el-tabs :model-value="'base'">
                <el-tab-pane lazy label="基本配置" name="base">
                    <BaseConfig v-model="formData" />
                </el-tab-pane>
                <el-tab-pane lazy label="AI模型/搜索配置" name="search">
                    <SearchConfig v-model="formData" />
                </el-tab-pane>
                <el-tab-pane lazy label="界面配置" name="interface">
                    <InterfaceConfig v-model="formData" />
                </el-tab-pane>
                <el-tab-pane lazy label="形象配置" name="digital">
                    <DigitalConfig v-model="formData" />
                </el-tab-pane>
            </el-tabs>
        </el-form>
    </el-card>
    <!-- 
    <footer-btns>
        <el-button type="primary" @click="handelSubmit" :disabled="formData.user_id">
            保存
        </el-button>
    </footer-btns> -->
</template>

<script setup lang="ts">
import type { FormInstance, FormRules } from 'element-plus'
import BaseConfig from './base-config.vue'
import SearchConfig from './search-config.vue'
import InterfaceConfig from './interface-config.vue'
import DigitalConfig from './digital-config.vue'
import feedback from '@/utils/feedback'

const props = defineProps<{
    headerTitle: string
    modelValue: Record<string, any>
}>()
const emit = defineEmits<{
    (event: 'update:modelValue', value: Record<string, any>): void
    (event: 'submit'): void
}>()

const formRef = shallowRef<FormInstance>()
const formRules = shallowReactive<FormRules>({})
const formData = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
})
const handelSubmit = async () => {
    try {
        await formRef.value?.validate()
        emit('submit')
    } catch (error: any) {
        for (const err in error) {
            const isInRules = Object.keys(formRules).includes(err)
            isInRules && feedback.msgError(error[err][0]?.message)
            break
        }
    }
}
</script>
