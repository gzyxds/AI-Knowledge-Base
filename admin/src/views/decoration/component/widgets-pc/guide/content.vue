<template>
    <div
        class="bg-center bg-cover"
        :style="{
          backgroundImage: `url(${appStore.getImageUrl(prop.bgImage)})`
        }"
    >
        <div class="flex flex-col items-center mx-auto max-w-[1200px] ">
            <div
                class="grid grid-cols-1 lg:grid-cols-3 xl:gap-x-10 sm:py-10 lg:max-w-[1150px] lg:mx-auto"
                :class="{
                    'lg:grid-cols-2': !prop.isShowLeft
                }"
            >
                <div
                    v-if="prop.isShowLeft"
                    class="flex justify-center flex-col mt-4 sm:mt-0"
                >
                    <div class="flex items-center">
                        <img
                            class="w-[34px] h-[34px]"
                            :src="appStore.getImageUrl(prop.logoImage)"
                            alt=""
                        />
                        <span class="text-[20px] font-medium ml-2">
                            {{ appStore.config.web_name }}
                        </span>
                    </div>
                    <div class="flex sm:mt-3 sm:flex-col">
                        <div class="mt-5 sm:mb-5">
                            {{ prop.content }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 mt-10 sm:mt-0">
                    <div class="mt-4 text-center">
                        <div class="text-[18px] font-bold">{{ prop.column1 }}</div>
                        <div class="mt-2 text-[16px] text-[#666]">
                            <ul v-for="(item, index) in getShowColumn1Data" :key="index">
                                <li class="mt-[15px]">
                                    {{ item.title }}
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <div class="text-[18px] font-bold">{{ prop.column2 }}</div>
                        <div class="mt-2 text-[16px] text-[#666]">
                            <ul v-for="(item, index) in getShowColumn2Data" :key="index">
                                <li class="mt-[15px]">
                                    {{ item.title  }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex items-center sm:m-0 m-5 sm:mt-[25px] mt-[35px]">
                    <div class="mr-10 text-center sm:mr-12" v-if="prop.rightQrcodeShow1">
                        <img
                            class="w-[120px] h-[120px]"
                            :src="appStore.getImageUrl(prop.rightQrcode1)"
                            alt="码多多"
                        />
                        <div class="mt-3 text-white">
                            {{ prop.rightQrcodeTitle1 }}
                        </div>
                    </div>
                    <div class="text-center" v-if="prop.rightQrcodeShow2">
                        <img
                            class="w-[120px] h-[120px]"
                            :src="appStore.getImageUrl(prop.rightQrcode2)"
                            alt="码多多"
                        />
                        <div class="mt-3 text-white">
                            {{ prop.rightQrcodeTitle2 }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type { Prop } from './config'
import useAppStore from '@/stores/modules/app'
const appStore = useAppStore()
const props = defineProps<{
    prop: Prop
}>()

const getShowColumn1Data = computed(() => props.prop.columnMenu1.filter((item) => item.isShow))
const getShowColumn2Data = computed(() => props.prop.columnMenu2.filter((item) => item.isShow))
</script>

<style lang="scss" scoped>
.enter-btn {
    background: linear-gradient(90deg, #54c6ee 0%, #3c5efd 100%);
    border: none;
    padding: 10px 30px;
    border-radius: 8px;
}
</style>
