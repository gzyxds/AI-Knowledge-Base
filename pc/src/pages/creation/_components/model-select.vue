<template>
    <div class="w-[300px] h-full bg-body rounded-[12px] flex flex-col">
        <div class="p-[15px]">
            <div class="flex items-center mb-4">
                <NuxtLink
                    class="flex bg-body p-[5px] text-bold rounded-[50%] text-primary shadow-light"
                    to="/creation"
                    :replace="true"
                >
                    <Icon name="el-icon-Back" :size="18" />
                </NuxtLink>
                <div class="text-xl flex-1 min-w-0 ml-[10px]">AI创作</div>
            </div>

            <el-input
                v-model="searchKeyword"
                placeholder="请输入关键词搜索"
                class="create-search"
            >
                <template #prefix>
                    <Icon name="el-icon-Search" />
                </template>
            </el-input>
        </div>

        <div class="flex-1 min-h-0">
            <ElScrollbar>
                <div class="px-[15px]">
                    <NuxtLink
                        v-for="model in currentModelList"
                        :key="model.id"
                        :to="{
                            path: '',
                            query: {
                                cateId: modelState.cateId,
                                modelId: model.id
                            }
                        }"
                        class="flex mb-[15px] rounded-[10px] px-[15px] py-[10px] items-center border border-br-light bg-body"
                        :class="{
                            'text-white border-primary !bg-primary':
                                modelState.modelId == model.id
                        }"
                        @click="emit('select', model)"
                    >
                        <el-image
                            class="w-[50px] h-[50px] rounded-[50%]"
                            :src="model.image"
                            alt=""
                        />
                        <div class="flex-1 min-w-0 ml-[15px]">
                            <div class="line-clamp-1 text-xl font-medium">
                                {{ model.name }}
                            </div>

                            <div
                                class="line-clamp-1 mt-[4px] text-tx-secondary"
                                :class="{
                                    '!text-white':
                                        modelState.modelId == model.id
                                }"
                            >
                                {{ model.tips }}
                            </div>
                        </div>
                    </NuxtLink>
                </div>
            </ElScrollbar>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useVModel } from '@vueuse/core'

const props = defineProps<{
    modelValue: string
    currentModelList: Record<string, any>[]
    modelState: Record<string, any>
}>()
const emit = defineEmits(['update:modelValue', 'select'])
const searchKeyword = useVModel(props, 'modelValue', emit)
</script>

<style lang="scss" scoped>
.dark {
    .create-search :deep(.el-input__wrapper) {
        --el-border-color: var(--el-bg-color);
        --el-input-border-color: var(--el-bg-color);
        background-color: #333333;
    }
}

.create-search :deep(.el-input__wrapper) {
    --el-input-bg-color: #f7f7f7;
    --el-input-border-color: #f7f7f7;
}
</style>
