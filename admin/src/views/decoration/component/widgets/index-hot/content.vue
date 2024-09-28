<template>
    <div class="index-menu px-[15px] pt-[15px]">
        <div class="text-xl font-medium mb-[15px]">
            {{ content.title }}
        </div>
        <div
            class="grid gap-4"
            :style="{
                'grid-template-columns': `repeat(${ content.showType }, minmax(0, 1fr))`
            }"
        >
            <template v-if="content.dataType == 1">
                <div
                    class="rounded-[5px] p-[15px] bg-white overflow-hidden"
                    v-for="(item, index) in content.dataNum"
                    :key="index"
                >
                    <div class="flex items-center">
                        <DecorationImg radius="50%" width="40px" height="40px" />
                        <div class="text-xl font-medium ml-[10px]">创作名称</div>
                    </div>

                    <div class="mt-[9px] text-[#999] line-clamp-3">这里是创作描述</div>
                    <div class="flex justify-end">
                        <div class="text-tx-secondary text-sm">🔥 0</div>
                    </div>
                </div>
            </template>
            <template v-if="content.dataType == 2">
                <div
                    class="flex flex-col min-h-0 rounded-[5px] p-[15px] bg-white overflow-hidden"
                    v-for="(item, index) in content.data"
                    :key="index"
                >
                    <div class="flex items-center">
                        <div class="flex-none">
                            <el-image :src="item.image" class="w-[40px] h-[40px] rounded-[50%]" />
                        </div>
                        <div class="text-xl font-medium ml-[10px] line-clamp-2">
                            {{ item.name }}
                        </div>
                    </div>

                    <div class="flex-1 mt-[9px] text-[#999] line-clamp-3">{{ item.tips }}</div>
                    <div class="flex justify-end">
                        <div class="text-tx-secondary text-sm">🔥 {{ item.all_use_count }}</div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type options from './options'
import DecorationImg from '../../decoration-img.vue'
type OptionsType = ReturnType<typeof options>
const props = defineProps<{
    isHidden: boolean
    content: OptionsType['content']
}>()
const emit = defineEmits<{
    (event: 'update:isHidden', value: boolean): void
}>()
</script>

<style lang="scss" scoped></style>
