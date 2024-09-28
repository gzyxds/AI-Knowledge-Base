<template>
  <div class="flex flex-col justify-center items-center h-[60vh]">
    <el-image class="w-[200px] h-[200px]" :src="noAuth" />
    <div class="text-tx-regular mb-4">暂无查看权限，请登录账号后查看</div>
    <ElButton type="primary" @click="toLogin"> 点击登录 </ElButton>
  </div>
</template>
<script lang="ts" setup>
import { useUserStore } from '@/stores/user'
import noAuth from '@/assets/image/noAuth.png'
const { toggleShowLogin, setLoginPopupType } = useUserStore()
const toLogin = () => {
  setLoginPopupType()
  toggleShowLogin()
}
</script>

<style lang="scss" scoped></style>
