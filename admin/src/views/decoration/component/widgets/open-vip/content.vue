<template>
    <div class="open-vip mt-[-30px] p-[10px]">
        <div
            class="container rounded-t-[8px]"
            :style="{
                'background-image': `url(${getImageUrl(content.bg)})`
            }"
        >
            <div class="flex justify-between items-center">
                <div class="flex">
                    <decoration-img width="40px" height="40px" :src="content.icon" alt="" />
                    <div class="ml-2 text-white">
                        <div class="font-bold">{{ content.title }}</div>
                        <div class="text-[12px]">{{ content.sub_title }}</div>
                    </div>
                </div>

                <div class="bg-white text-[#445df4] py-[5px] px-[10px] rounded-full text-[12px]">
                    {{ content.btn }}
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'
import type options from './options'
import DecorationImg from '../../decoration-img.vue'
import useAppStore from "@/stores/modules/app";
type OptionsType = ReturnType<typeof options>
const props = defineProps({
    content: {
        type: Object as PropType<OptionsType['content']>,
        default: () => ({})
    },
    styles: {
        type: Object as PropType<OptionsType['styles']>,
        default: () => ({})
    }
})

const appStore = useAppStore()
const { getImageUrl } = appStore
</script>

<style lang="scss" scoped>
.container {
    background-size: cover;
    background-repeat: no-repeat;
    padding: 10px;
}
</style>
