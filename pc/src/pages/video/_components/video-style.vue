<template>
    <el-form-item>
        <template #label>
            <div class="w-full flex items-center">
                <span class="font-bold text-tx-primary flex-1"> 选择风格 </span>
            </div>
        </template>
        <div class="flex-1 overflow-hidden">
            <div class="flex flex-wrap mx-[-6px]">
                <div
                    v-for="item in styleList"
                    class="w-[25%] px-[6px]"
                    :key="item.id"
                >
                    <div
                        class="h-full cursor-pointer"
                        @click="selectStyle(item.id)"
                    >
                        <div
                            class="pt-[100%] relative h-0 rounded-[12px] overflow-hidden"
                        >
                            <div class="absolute inset-0 left-0 top-0">
                                <el-image
                                    :src="item.image"
                                    class="h-full w-full"
                                />
                            </div>
                            <div
                                v-if="
                                    styleIds.includes(item.id) ||
                                    styleIds.includes(String(item.id))
                                "
                                class="absolute bg-[var(--el-overlay-color-lighter)] inset-0 left-0 top-0 flex items-center justify-center text-white"
                            >
                                <Icon name="el-icon-SuccessFilled" :size="20" />
                            </div>
                        </div>
                        <div class="text-center text-xs">
                            {{ item.name }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </el-form-item>
</template>

<script setup lang="ts">
import { useVModels } from '@vueuse/core'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: any
        styleList: any[]
    }>(),
    {
        modelValue: () => []
    }
)
const { modelValue: styleIds } = useVModels(props, emit)
const selectStyle = (id: number) => {
    const index = styleIds.value.findIndex((item: any) => item === id)
    if (index !== -1) {
        styleIds.value.splice(index, 1)
    } else {
        styleIds.value.push(id)
    }
}
</script>
