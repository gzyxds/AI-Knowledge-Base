<template>
    <div class="index-menu px-[15px] pt-[15px]">
        <div v-if="getShowData.length" class="bg-white rounded-[10px] overflow-hidden">
            <div class="flex flex-wrap pt-[20px] pb-[10px]">
                <div
                    v-for="(item, index) in content.data"
                    :key="index"
                    class="flex flex-col items-center w-1/4 mb-[15px]"
                >
                    <decoration-img width="26px" height="26px" :src="item.image" alt="" />
                    <div class="mt-[7px]">{{ item.title }}</div>
                </div>
            </div>
        </div>
        <div v-else class="min-h-[100px] flex items-center justify-center">功能菜单</div>
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
