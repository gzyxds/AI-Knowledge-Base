<template>
    <view v-if="isHidden" class="relative mx-[30rpx] mt-[30rpx] bg-white rounded-[20rpx]">
        <swiper
            class="py-[20rpx] uni-swiper"
            :style="{
                height: navList[0].length > 4 ? '320rpx' : '140rpx'
            }"
            :autoplay="false"
            :indicator-dots="false"
            @change="swiperChange"
        >
            <swiper-item v-for="(sItem, sIndex) in navList" :key="sIndex">
                <view class="nav" v-if="navList.length">
                    <view
                        class="grid grid-rows-auto gap-y-3 w-full"
                        :style="{ 'grid-template-columns': `repeat(4, 1fr)` }"
                    >
                        <view
                            v-for="(item, index) in sItem"
                            :key="index"
                            class="flex flex-col items-center"
                            @click="handleClick(item.link)"
                        >
                            <u-image width="80" height="80" :src="getImageUrl(item.image)" alt=""/>
                            <view class="mt-[20rpx] text-sm text-[#222]">{{ item.title }}</view>
                        </view>
                    </view>
                </view>
            </swiper-item>
        </swiper>
        <view class="swiper-indicator" v-if="navList.length > 1">
            <view
                class="indicator-item-rect"
                :class="{
                    'indicator-item-rect-active': index == swiperCurrent
                }"
                v-for="(item, index) in navList"
                :key="index"
            ></view>
        </view>
    </view>
</template>


<script setup lang="ts">
import {ref, computed, watch} from "vue";
import {navigateTo} from "@/utils/navigate";
import {sliceArray} from '@/utils/util'
import {useAppStore} from "@/stores/app";
const {getImageUrl} = useAppStore();
const props = defineProps<{
    isHidden: boolean
    prop: Record<string, any>
}>()

const swiperCurrent = ref<number>(0)
const navList = ref<Record<string, any>>([])

watch(
    () => props.prop.data,
    (val) => {
        const filterData = val.filter((item : any) => item.isShow)
        navList.value = sliceArray(filterData, 8)
    },
    {deep: true, immediate: true}
)

const handleClick = (link: any) => {
    if (!link) return;
    navigateTo(link);
};

const swiperChange = (e: any) => {
    swiperCurrent.value = e.detail.current
}
</script>

<style lang="scss" scoped>
.swiper-indicator {
    padding: 0 24rpx;
    position: absolute;
    bottom: 16rpx;
    display: flex;
    justify-content: center;
    width: 100%;
    z-index: 1;
}
.indicator-item-rect {
    width: 10rpx;
    height: 10rpx;
    margin: 0 6rpx;
    border-radius: 4rpx;
    transition: all 0.5s;
    background-color: #F1F0F0;
}
.indicator-item-rect-active {
    width: 24rpx;
    background-color: #BABABA;
}
</style>