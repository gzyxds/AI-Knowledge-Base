<template>
    <div class="flex flex-col p-[16px] flex-1">
        <div class="flex pb-[20px]">
            <div
                class="text-lg font-medium flex flex-1 min-w-0 items-center mr-auto"
                @click="emit('select')"
            >
                <span class="line-clamp-1">{{ modelData.name }}</span>
                <Icon v-if="appStore.isMobile" name="el-icon-CaretBottom" />
            </div>
            <ElButton link type="primary" @click="emit('insert')">
                插入示例
            </ElButton>
        </div>
        <div class="flex-1 min-h-0">
            <ElScrollbar>
                <div class="">
                    <FormDesigner
                        ref="formDesignerRef"
                        v-model="formData"
                        :form-lists="modelData.form"
                        size="large"
                    />
                </div>
            </ElScrollbar>
        </div>

        <div>
            <div class="flex flex-col justify-center items-center">
                <slot name="actions"></slot>
                <ElButton
                    v-if="modelData.id"
                    class="create-btn"
                    type="primary"
                    :loading="loading"
                    @click="handleCreate"
                >
                    智能创作
                </ElButton>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useVModel } from '@vueuse/core'
import { useAppStore } from '@/stores/app'

const props = defineProps<{
    modelData: Record<string, any>
    modelValue: Record<string, any>
    loading: boolean
}>()
const emit = defineEmits([
    'update:modelValue',
    'insert',
    'create',
    'select',
    'update:modelKey'
])
const formDesignerRef = shallowRef()
const formData = useVModel(props, 'modelValue', emit)
const appStore = useAppStore()
const handleCreate = async () => {
    try {
        await formDesignerRef.value?.validate()
    } catch (error) {
        feedback.msgError('必填项不能为空')
        return
    }
    emit('create')
}
</script>

<style lang="scss" scoped>
.create-btn {
    width: 100%;
    height: 50px;
    display: flex;
    justify-content: center;
    align-items: center;
    border: none;
    @apply text-lg;
}
</style>