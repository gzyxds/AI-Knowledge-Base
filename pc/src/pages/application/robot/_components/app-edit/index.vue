<template>
    <el-form
        v-if="isRenderForm"
        ref="formRef"
        :model="formData"
        label-width="140px"
        :rules="formRules"
        class="app-edit flex flex-col"
    >
        <div class="flex-1 min-h-0">
            <el-tabs :model-value="'base'">
                <el-tab-pane label="基本配置" name="base">
                    <ElScrollbar>
                        <BaseConfig v-model="formData"/>
                    </ElScrollbar>
                </el-tab-pane>

                <el-tab-pane label="AI模型/搜索配置" name="search">
                    <ElScrollbar>
                        <SearchConfig v-model="formData"/>
                    </ElScrollbar>
                </el-tab-pane>
                <el-tab-pane label="界面配置" name="interface">
                    <ElScrollbar>
                        <InterfaceConfig v-model="formData"/>
                    </ElScrollbar>
                </el-tab-pane>
                <el-tab-pane label="形象配置" name="digital">
                    <ElScrollbar>
                        <DigitalConfig v-model="formData"/>
                    </ElScrollbar>
                </el-tab-pane>
            </el-tabs>
        </div>
        <div class="my-[15px] flex justify-center">
            <el-button type="primary" @click="handelSubmit"> 保存</el-button>
        </div>
    </el-form>
</template>

<script setup lang="ts">
import type {FormInstance, FormRules} from 'element-plus'
import {cloneDeep} from 'lodash-es'
import BaseConfig from './base-config.vue'
import SearchConfig from './search-config.vue'
import InterfaceConfig from './interface-config.vue'
import DigitalConfig from './digital-config.vue'
import feedback from '@/utils/feedback'
import {putRobot} from '@/api/robot'

const props = defineProps<{
    modelValue: Record<string, any>
}>()
const emit = defineEmits<{
    (event: 'success'): void
}>()

const formData = ref({})

const isRenderForm = computed(() => !!Object.keys(formData.value).length)
watchEffect(() => {
    formData.value = cloneDeep(props.modelValue)
})
const formRef = shallowRef<FormInstance>()
const formRules = shallowReactive<FormRules>({
    image: [
        {
            required: true,
            type: 'string',
            message: '请选择应用图标'
        }
    ],
    name: [
        {
            required: true,
            message: '请输入应用名称'
        }
    ],
    model_id: [
        {
            required: true,
            message: '请选择AI通道',
            trigger: ['blur']
        }
    ],
    model_sub_id: [
        {
            required: true,
            message: '请选择AI模型',
            trigger: ['blur']
        }
    ],
    cate_id: [
        {
            required: true,
            message: '请选择分类',
            trigger: ['blur']
        }
    ],
    digital_id: [
        {
            required: true,
            message: '请选择形象',
            trigger: ['change']
        },
        {
            validator(rule: any, value: any, callback: any) {
                if (Number(value) === 0) {
                    callback(new Error('请选择形象'))
                }
                callback()
            }
        }
    ]
})

const handelSubmit = async () => {
    try {
        await formRef.value?.validate()
        await putRobot(formData.value)
        emit('success')
    } catch (error: any) {
        for (const err in error) {
            const isInRules = Object.keys(formRules).includes(err)
            isInRules && feedback.msgError(error[err][0]?.message)
            break
        }
    }
}
</script>

<style lang="scss" scoped>
.app-edit {
    height: 100%;

    :deep(.el-tabs) {
        --el-tabs-header-height: 50px;
        height: 100%;
        display: flex;
        flex-direction: column;

        .el-tabs__nav {
            padding: 0 20px;
        }

        .el-tabs__content,
        .el-tab-pane {
            min-height: 0;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .el-tabs__item {
            font-size: 15px;
        }
    }
}
</style>
