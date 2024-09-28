<template>
  <div class="h-full">
    <ElScrollbar>
      <div class="p-main">
        <div class="sm:p-[30px] lg:p-[40px] xl:p-[60px]">
          <div class="flex flex-col items-center justify-center">
            <h1 class="text-[32px] font-medium">
              {{ appStore.getAvatarConfig.title }}
            </h1>
            <p class="max-w-[850px] mt-[24px] text-center text-lg">
              {{ appStore.getAvatarConfig.intro }}
            </p>
            <div class="mt-[40px]">
              <div
                class="p-main bg-white shadow-[0_0_16px_#006cff0f] rounded-2xl flex flex-col items-center"
              >
                <img
                  class="w-[260px] h-[220px]"
                  src="@/assets/image/avatar_example.png"
                  alt=""
                />
                <div class="text-2xl my-[10px]">我的帐户</div>
                <div class="text-tx-regular">
                  剩余：{{ userStore.userInfo.video_num || 0 }}分钟
                </div>
                <div class="my-[20px]">
                  <NuxtLink to="/digital_human/aside/video_compositing">
                    <ElButton
                      type="primary"
                      class="enter-btn hover-to-right"
                      round
                    >
                      前往制作
                      <Icon
                        class="ml-[5px] target"
                        size="18"
                        name="el-icon-Right"
                      />
                    </ElButton>
                  </NuxtLink>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </ElScrollbar>
  </div>
</template>
<script setup lang="ts">
import { useAppStore } from '@/stores/app'
import { useUserStore } from '@/stores/user'

const userStore = useUserStore()
const appStore = useAppStore()
</script>
<style lang="scss" scoped>
.enter-btn {
  background: linear-gradient(90deg, #54c6ee 0%, #3c5efd 100%);
  border: none;
  padding: 10px 28px;
}
</style>
