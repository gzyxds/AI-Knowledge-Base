<template>
    <div>
        <sidbar-item-title
            title="主要模型"
            required
            tips="让AI根据此模型的风格绘制图片，修改合适的描述词和参数可以让生成效果更加精美"
        />
        <el-scrollbar>
            <div class="mb-2" style="--el-border-radius-base: 12px">
                <el-segmented
                    :block="false"
                    class="h-[38px] !bg-[transparent]"
                    id="ddddd"
                    v-model="modelCategory"
                    :options="modelCategoryList"
                    @change="categoryChange"
                >
                </el-segmented>
            </div>
        </el-scrollbar>
        <el-scrollbar max-height="360px">
            <div v-if="modelList.length > 0" class="grid grid-cols-2 gap-4">
                <div
                    v-for="(item, index) in modelList"
                    :key="item.id"
                    class="flex flex-col gap-2"
                    @click="selectModel(index)"
                >
                    <div class="relative rounded-[12px] overflow-hidden">
                        <aspect-ratio
                            class="rounded-[12px] overflow-hidden bg-[var(--el-bg-color-page)]"
                            :src="item.cover"
                            fit="cover"
                            :ratio="[144, 100]"
                        />

                        <div
                            class="absolute top-0 left-0 bg-[rgba(0,0,0,0.4)] w-full h-full flex justify-center items-center transition-opacity opacity-0"
                            :class="{
                                'opacity-100': item.model_name === currentModel
                            }"
                        >
                            <Icon
                                name="el-icon-CircleCheckFilled"
                                :size="20"
                                color="#fff"
                            />
                        </div>
                    </div>
                    <div class="text-hidden-2 text-center">
                        {{ item.title || item.model_name }}
                    </div>
                </div>
            </div>
            <el-empty v-else description="暂无模型数据" :image-size="50" />
        </el-scrollbar>
    </div>
</template>

<script lang="ts" setup>
import { useVModels } from '@vueuse/core'
import {
    loraList,
    modelList,
    modelCategory,
    getModel,
    getModelCategory,
    modelCategoryList
} from '../../hooks/useDrawEffect'

import sidbarItemTitle from './../common/sidbar-item-title.vue'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()

const props = withDefaults(
    defineProps<{
        modelValue?: any
    }>(),
    {
        modelValue: ''
    }
)

const { modelValue: currentModel } = useVModels(props, emit)

const selectModel = (index: number | 'clear') => {
    if (index === 'clear') {
        currentModel.value = ''
        loraList.value = []
    } else {
        currentModel.value = modelList.value[index].model_name
        loraList.value = modelList.value[index].loras
    }
}

const categoryChange = (value: number) => {
    selectModel('clear')
    modelCategory.value = value
    getModel()
}

onMounted(async () => {
    getModelCategory()
})
</script>

<style lang="scss" scoped>
:deep(.el-segmented__group) {
    gap: 12px;
    .el-segmented__item {
        background-color: var(--el-bg-color-page);
        padding: 0 16px;
    }
}
</style>
