<template>
  <div>
    <div @click="show = true">
      <slot />
    </div>
    <Teleport to="body">
      <div class="chat-container" v-show="show">
        <div class="close-icon" @click="show = false">
          <Icon name="el-icon-Close" :size="18" />
        </div>
        <iframe
          width="100%"
          height="100%"
          border="none"
          :src="customerData.link"
          @load="iframeOnload"
        />
      </div>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { useAppStore } from '@/stores/app'
const appStore = useAppStore()
const route = useRoute()
const show = ref(false)
const customerData = computed(() => appStore.getOnlineKf)

const iframeOnload = () => {
  //   const oneDay = 24 * 60 * 60 * 1000
  //   const cacheKf = useCookie('cache_Kf', {
  //     expires: new Date(Date.now() + oneDay)
  //   })
  setTimeout(() => {
    if (route.path == '/') {
      show.value = true
    }
  }, 5000)
}
</script>

<style scoped lang="scss">
.chat-container {
  display: block;
  position: fixed;
  z-index: 999999;
  width: 50%;
  max-width: 375px;
  height: 62vh;
  max-height: 667px;
  right: 12px;
  bottom: 12px;
  overflow: hidden;
  box-shadow: rgba(0, 0, 0, 0.16) 0px 8px 32px 0px;
  border-radius: 8px;
  background-color: #fff;
  .close-icon {
    color: #333;
    cursor: pointer;
    display: flex;
    position: absolute;
    right: 16px;
    top: 35px;
    transform: translateY(-50%);
  }
}
</style>
