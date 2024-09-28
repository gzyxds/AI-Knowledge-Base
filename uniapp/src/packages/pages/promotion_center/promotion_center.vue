<template>
    <page-meta :page-style="$theme.pageStyle"> </page-meta>
    <page-status :status="status">
        <view class="promotion-center">
            <!--  已通过  -->
            <content
                v-if="isDistributor && isOpen"
                :user-info="userInfo"
                :config="config"
                :withdraw-config="withdrawConfig"
            />
            <!--  申请表单  -->
            <apply
                v-else-if="!isDistributor && isOpen"
                :apply-detail="applyDetail"
                @reload="getDistributionData"
            />
            <!--  功能未开启  -->
            <view class="mt-[45%]" v-if="!isOpen">
                <u-empty
                    font-size="28"
                    icon-size="300"
                    text="该功能还未开启～"
                    :src="EmptyDistributeCenterImage"
                ></u-empty>
            </view>
        </view>
        <!-- #ifdef H5 -->
        <!--    悬浮菜单    -->
        <floating-menu></floating-menu>
        <!-- #endif -->
    </page-status>
</template>
<script setup lang="ts">
import { getDistributionIndex } from '@/api/promotion'
import { useThemeStore } from '@/stores/theme'
import { onPullDownRefresh, onShow } from '@dcloudio/uni-app'
import { computed, ref, shallowRef } from 'vue'
import { PageStatusEnum } from '@/enums/appEnums'
import Content from './components/content.vue'
import Apply from './components/apply.vue'
import EmptyDistributeCenterImage from '@/packages/static/empty/distribute_center.png'
import FloatingMenu from "@/components/floating-menu/floating-menu.vue";

const status = ref(PageStatusEnum.LOADING)
const userInfo = ref<any>({})
const applyDetail = ref<any>({})
const config = shallowRef<any>({})
const withdrawConfig = ref<any>({})
const isOpen = ref(false)
const isDistributor = ref(false)
const getDistributionData = async () => {
    const data = await getDistributionIndex()
    userInfo.value = data.user
    applyDetail.value = data.apply_detail
    isOpen.value = data.is_open
    config.value = data.config
    withdrawConfig.value = data.withdraw_config
    isDistributor.value = !!userInfo.value.is_distribution
}

const isFixed = ref(false)
const themeStore = useThemeStore()
const getNavBg = computed(() => {
    return isFixed.value ? themeStore.primaryColor : 'transparent'
})

onShow(async () => {
    try {
        await getDistributionData()
        status.value = PageStatusEnum.NORMAL
    } catch (error) {
        status.value = PageStatusEnum.ERROR
    }
})

onPullDownRefresh(async () => {
    try {
        await getDistributionData()
        status.value = PageStatusEnum.NORMAL
    } catch (error) {
        status.value = PageStatusEnum.ERROR
    }
    uni.stopPullDownRefresh()
})
</script>

<style>
page {
    height: 100%;
}
</style>
<style scoped>
.promotion-center {
    height: 100%;
    background-image: url("../../static/images/distribution_bg.png");
    background-size: cover;
}
</style>
