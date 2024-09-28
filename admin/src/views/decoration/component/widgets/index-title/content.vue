<template>
    <div class="index-title">
        <div class="flex items-center px-[14px] py-[12px]">
            <el-image class="w-[25px] h-[25px]" :src="pcData.pc_logo" />
            <h1 class="font-bold ml-[6px] line-clamp-1">
                {{ pcData.pc_name }}
            </h1>
        </div>
    </div>
</template>
<script lang="ts" setup>
import { getWebsite } from '@/api/setting/website'
import { ref } from 'vue'

const pcData = ref({
    pc_logo: '',
    pc_name: ''
})
const getPcData = async () => {
    const { pc_logo, pc_name } = await getWebsite()
    pcData.value = { pc_logo, pc_name }
}
getPcData()
</script>

<style lang="scss" scoped>
.index-title {
}
</style>
