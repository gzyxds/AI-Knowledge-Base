<template>
    <view class="user-bottom flex items-center p-[20rpx] text-center">
        <view class="text-sm w-full text-content">
            <text class="">{{ content.data.title }}</text>
            <text class="mr-[4px]">{{ content.data.content }}</text>
            <image
                :src="iconCopy"
                class="w-[28rpx] h-[28rpx] align-middle"
                v-if="content?.data.canCopy"
                @click="copy(content.data.content)"
            ></image>
        </view>
    </view>
</template>
<script lang="ts" setup>
import { useCopy } from '@/hooks/useCopy'
import { useNavigationBarTitleStore } from '@/stores/navigationBarTitle'
import iconCopy from '@/static/images/icon/icon_copy.png'

const navigationBarTitleStore = useNavigationBarTitleStore()
const props = defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({})
    }
})
const { copy } = useCopy()
</script>

<style lang="scss" scoped>
.user-bottom {
}
</style>
