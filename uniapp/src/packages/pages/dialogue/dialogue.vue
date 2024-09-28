<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="h-full flex flex-col min-h-0">
        <view class="bg-white flex items-center pr-[20rpx]">
            <view class="flex items-center text-lg w-[110rpx]">
                <!-- #ifdef H5 -->
                <view class="px-[20px] py-[16rpx]" @click="back">
                    <u-icon name="arrow-left" :size="34" />
                </view>
                <!-- #endif -->
            </view>
            <view
                class="flex-1 flex items-center justify-center py-[24rpx]"
                :class="{ 'pr-[100rpx]': active == 1 }"
            >
                <view
                    v-for="(item, index) in list"
                    :key="index"
                    class="flex items-center mx-[24rpx] px-[10rpx] dialogue_tabs"
                    :class="{
                        dialogue_tabs_active: active === index
                    }"
                    @click="active = index"
                >
                    {{ item }}
                </view>
            </view>
            <view
                v-if="active === 0"
                class="flex items-center"
                @click="chatRef.showPopup = true"
            >
                <u-icon name="plus-circle" :size="36" />
                <view class="flex-1 line-clamp-1 ml-[8rpx]">新建</view>
            </view>
        </view>

        <!--    问答AI    -->
        <view v-show="active === 0" class="h-full flex flex-col">
            <chat
                class="h-full flex flex-col"
                ref="chatRef"
                :active="active"
                :modelKey="modelKey"
            />
        </view>

        <!--    角色对话    -->
        <view v-show="active === 1" class="h-full flex flex-col">
            <role class="h-full flex flex-col" />
        </view>

        <tabbar />
    </view>
</template>

<script lang="ts" setup>
import chat from './components/chat.vue'
import role from './components/role.vue'
import { ref } from 'vue'
import { useRouter } from 'uniapp-router-next'

const router = useRouter()
const chatRef = ref()
const list = ref<string[]>(['问答', '角色'])
const active = ref<number>(0)
const modelKey = ref<string>('')

const back = () => {
    router.navigateBack()
}
</script>

<style lang="scss">
page {
    height: 100%;
    background: #f4f8fd;
}

.dialogue_tabs {
    position: relative;

    &_active {
        @apply text-primary;
        &::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -10rpx;
            width: 100%;
            height: 2px;
            border-radius: 5px;
            @apply bg-primary;
        }
    }
}
</style>
