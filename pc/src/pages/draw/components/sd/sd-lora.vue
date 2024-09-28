<template>
    <div>
        <sidbar-item-title
            title="微调模型"
            tips="在基础模型上叠加微调模型，让画面更细腻更可控"
        />
        <el-scrollbar max-height="360px">
            <div v-if="loraList.length > 0" class="grid grid-cols-2 gap-4">
                <div
                    v-for="(item, index) in loraList"
                    :key="item.id"
                    class="flex flex-col gap-2"
                    @click="selectLora(index)"
                >
                    <div
                        class="relative rounded-[12px] overflow-hidden cursor-pointer"
                    >
                        <aspect-ratio
                            class="rounded-[12px] overflow-hidden w-auto h-full bg-[var(--el-bg-color-page)]"
                            :src="item.cover"
                            fit="cover"
                            :ratio="[144, 100]"
                        />

                        <div
                            class="absolute top-0 left-0 bg-[rgba(0,0,0,0.4)] w-full h-full flex justify-center items-center transition-opacity opacity-0"
                            :class="{
                                'opacity-100': currentLora.includes(
                                    item.model_name
                                )
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
            <el-empty
                v-else
                description="暂无关联的微调模型"
                :image-size="50"
            />
        </el-scrollbar>
    </div>
</template>

<script lang="ts" setup>
import type { loraItem } from '../../types/draw'
import { useVModels } from '@vueuse/core'
import { loraList } from '../../hooks/useDrawEffect'

import sidbarItemTitle from './../common/sidbar-item-title.vue'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string[]): void
}>()

const props = defineProps({
    modelValue: {
        type: Array as () => string[],
        default: []
    }
})

watch(
    () => loraList,
    () => {
        selectLora('clear')
    }
)

const { modelValue: currentLora } = useVModels(props, emit)

const selectLora = (index: number | 'clear') => {
    if (index === 'clear') {
        currentLora.value = []
    } else {
        if (currentLora.value.includes(loraList.value[index].model_name)) {
            currentLora.value = currentLora.value.filter(
                (item: string) => item !== loraList.value[index].model_name
            )
        } else {
            currentLora.value.push(loraList.value[index].model_name)
        }
    }
}
</script>

<style lang="scss" scoped></style>
