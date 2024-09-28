<template>
    <div>
        <div class="flex">
            <Dialog v-model="selectData" :disabled="disabled" :limit="limit">
                <slot />
            </Dialog>
            <div class="mr-[20px]">
                <span class="text-tx-secondary ml-[20px]">最多添加{{ limit }}条</span>
            </div>
            <div class="clear" v-if="selectData.length">
                <el-button size="small" type="text" @click="selectData = []" :disabled="disabled">
                    清空
                </el-button>
            </div>
        </div>
        <div class="list pt-[20px]">
            <draggable class="flex flex-wrap" v-model="selectData" animation="300">
                <template v-slot:item="{ element: item, index }">
                    <del-wrap
                        :key="index"
                        @close="handleDelete(index)"
                        class="w-[64px] h-[64px] cursor-move mr-[10px] mb-[10px]"
                    >
                        <el-image style="width: 100%; height: 100%" fit="cover" :src="item.image" />
                    </del-wrap>
                </template>
            </draggable>
        </div>
    </div>
</template>
<script lang="ts" setup>
import { useVModel } from '@vueuse/core'
import Dialog from './dialog.vue'
import draggable from 'vuedraggable'

const props = withDefaults(
    defineProps<{
        modelValue: any[]
        disabled?: boolean
        limit: number
    }>(),
    {
        disabled: false,
        modelValue: () => [],
        limit: 50
    }
)

const emit = defineEmits<{
    (event: 'update:modelValue', value: any): void
}>()
const selectData = useVModel(props, 'modelValue', emit)

const handleDelete = (index: number) => {
    selectData.value.splice(index, 1)
}
</script>
