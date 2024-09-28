<template>
    <div class="index-banner px-[15px] pt-[10px]">
        <div class="banner-image rounded-[10px] overflow-hidden">
            <decoration-img width="100%" height="140px" :src="getImage" fit="cover" />
        </div>
    </div>
</template>
<script lang="ts" setup>
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

const getImage = computed(() => {
    const data = getShowData.value
    if (Array.isArray(data)) {
        return data[0] ? data[0].image : ''
    }
    return ''
})
const getShowData = computed(() => props.content.data.filter((item) => item.isShow))
</script>

<style lang="scss" scoped></style>
