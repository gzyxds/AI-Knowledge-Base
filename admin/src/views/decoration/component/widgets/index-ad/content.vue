<template>
    <div class="index-ad px-[15px]">
        <div
            class="grid mx-[-7px]"
            :style="{
                'grid-template-columns': `repeat(${ content.showType }, minmax(0, 1fr))`
            }"
            v-if="getShowData.length"
        >
            <div
                class="w-full px-[7px] mt-[15px]"
                v-for="(item, index) in getShowData"
                :key="index"
            >
                <div class="h-full bg-white rounded-[10px] overflow-hidden">
                    <div :class="[content.showType == 2 ? 'h-[122px]' : 'h-[80px]']">
                        <DecorationImg :src="item.image" width="100%" height="100%" fit="cover" />
                    </div>
                    <div class="p-[10px] text-center">
                        <div class="text-xl line-clamp-1 font-medium">{{ item.title }}</div>
                        <div class="text-tx-secondary line-clamp-1 mt-[2px] text-sm">
                            {{ item.desc }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="min-h-[100px] flex items-center justify-center">广告区域</div>
    </div>
</template>
<script lang="ts" setup>
import { useVModel } from '@vueuse/core'
import type options from './options'
import { computed } from 'vue'
import DecorationImg from '../../decoration-img.vue'
type OptionsType = ReturnType<typeof options>
const props = defineProps<{
    isHidden: boolean
    content: OptionsType['content']
}>()
const emit = defineEmits<{
    (event: 'update:isHidden', value: boolean): void
}>()

const getShowData = computed(() => props.content.data.filter((item) => item.isShow))
</script>

<style lang="scss" scoped></style>
