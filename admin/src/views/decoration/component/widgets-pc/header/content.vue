<template>
    <div class="header h-[60px] p-[15px] text-[#9299A6]">
        <div class="flex items-center w-[1160px] mx-auto title">
            <div class="flex items-center">
                <el-image class="w-[34px] h-[34px]" :src="pcData.pc_logo" />
                <h1 class="font-bold ml-[10px] text-[17px] text-black line-clamp-1">
                    {{ pcData.pc_name }}
                </h1>
            </div>
            <div class="flex items-center ml-[50px] flex-1">
                <template v-for="(item, index) in getShowNav" :key="index">
                    <div
                        class="ml-[20px] text-xl"
                        :class="{
                            select: index === 0
                        }"
                    >
                        <!-- @vue-skip -->
                        <span>{{ item.name }} </span>
                    </div>
                </template>
            </div>
            <div>
                <div class="mr-[10px] bg-[#FFFBF3] rounded-full flex items-center">
                    <div class="recharge-btn">充值</div>
                </div>
            </div>
            <div class="flex items-center">
                <ElAvatar :size="25" :src="avatar" />

                <span class="mx-2">用户xxxxxxx</span>
                <Icon name="el-icon-ArrowDown" />
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import { getWebsite } from '@/api/setting/website'
import type { Prop } from './config'
import avatar from './image/default_avatar.png'
const props = defineProps<{
    prop: Prop
}>()
const pcData = ref({
    pc_logo: '',
    pc_name: ''
})
const getPcData = async () => {
    const { pc_logo, pc_name } = await getWebsite()
    pcData.value = { pc_logo, pc_name }
}

const getShowNav = computed(() => props.prop.nav.filter((item) => item.isShow))
getPcData()
</script>

<style lang="scss" scoped>
.select {
    //color: var(--color-white);
    line-height: 32px;
    border-bottom: 2px solid var(--color-white);
}

.recharge-btn {
    padding: 5px 12px;
    border-radius: 100px;
    background: linear-gradient(89.14deg, #ffcb58 0%, #f7630e 100%);
    @apply text-sm text-white cursor-pointer;
}
</style>
