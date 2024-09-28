<template>
        <view 
            class="bg-white mx-[20rpx] rounded-b-lg mb-[20rpx]"
            :class="[appStore.config?.member_package_open ? 'rounded-b-lg' : 'rounded-lg']"
        >
            <div class="flex justify-around px-[30rpx] py-[30rpx] text-center">
                <navigator
                    class="flex-1"
                    hover-class="none"
                    url="/packages/pages/use_list/use_list?type=1"
                >
                    <div class="mb-2 text-2xl font-medium">
                        {{ userStore.userInfo.balance || '0' }}
                    </div>
                    <div class="text-xs text-muted">
                        {{ getChatConfig.price_unit }}数量
                    </div>
                </navigator>

                <navigator
                    class="flex-1"
                    hover-class="none"
                    url="/packages/pages/use_list/use_list?type=2"
                >
                    <div class="mb-2 text-2xl font-medium">
                        {{ userStore.userInfo.robot_num || '0' }}
                    </div>
                    <div class="text-xs text-muted">智能体</div>
                </navigator>

                <navigator
                    class="flex-1"
                    hover-class="none"
                    url="/packages/pages/user_works/user_works"
                >
                    <div class="mb-2 text-2xl font-medium">
                        {{ userStore.userInfo.my_works || '0' }}
                    </div>
                    <div class="text-xs text-muted">我的作品</div>
                </navigator>
            </div>
        </view>
</template>

<script setup lang="ts">
import { useAppStore } from '@/stores/app'
import { useUserStore } from '@/stores/user'

const appStore = useAppStore()
const userStore = useUserStore()

const { getChatConfig, getSwitchConfig } = useAppStore()
</script>
