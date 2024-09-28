<template>
  <div class="flex flex-col h-full">
    <div class="flex-1 pt-[20px] px-[30px] min-h-0">
      <el-tabs :model-value="loginWay" @tabChange="tabChange">
        <el-tab-pane
          v-for="(item, index) in loginWayListsFilter"
          :key="index"
          :label="item.name"
          :name="item.type"
        >
            <WeixinLogin
                v-if="
                    item.type === LoginWayEnum.WEIXIN &&
                    tabCurrent === LoginWayEnum.WEIXIN
                "
            />
          <MobileLogin v-if="item.type === LoginWayEnum.MOBILE" />
          <MailboxLogin v-if="item.type === LoginWayEnum.MAILBOX" />
        </el-tab-pane>
      </el-tabs>
    </div>

    <div
        v-if="getRegisterWay.length && tabCurrent != LoginWayEnum.WEIXIN || isOpenAgreement"
        class="bg-[#f4f4f4] px-[20px] py-[15px] flex dark:bg-[#333]"
    >
      <div v-if="isOpenAgreement" class="flex-1 text-tx-secondary">
        您登录即同意
        <NuxtLink
          v-slot="{ href }"
          :to="`/policy/${PolicyAgreementEnum.SERVICE}`"
          custom
        >
          <a class="text-tx-primary" :href="href" target="_blank"> 用户协议 </a>
        </NuxtLink>
        和
        <NuxtLink
          v-slot="{ href }"
          class="text-tx-primary"
          :to="`/policy/${PolicyAgreementEnum.PRIVACY}`"
          custom
        >
          <a class="text-tx-primary" :href="href" target="_blank"> 隐私政策 </a>
        </NuxtLink>
      </div>
      <ElButton
        v-if="getRegisterWay.length && tabCurrent != LoginWayEnum.WEIXIN"
        type="primary"
        link
        @click="userStore.setLoginPopupType(LoginPopupTypeEnum.REGISTER)"
      >
        注册新账号
      </ElButton>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ElButton } from 'element-plus'
import MobileLogin from './mobile-login.vue'
import MailboxLogin from './mailbox-login.vue'
import WeixinLogin from './weixin-login.vue'
import { useAppStore } from '@/stores/app'
import { LoginPopupTypeEnum, PolicyAgreementEnum } from '@/enums/appEnums'
import { useUserStore } from '@/stores/user'

const appStore = useAppStore()
const userStore = useUserStore()
enum LoginWayEnum {
  MOBILE = '1',
  MAILBOX = '2',
  WEIXIN = '3'
}
const loginWayLists = [
  {
    name: '微信登录',
    type: LoginWayEnum.WEIXIN
  },
  {
    name: '手机号登录',
    type: LoginWayEnum.MOBILE
  },
  {
    name: '邮箱登录',
    type: LoginWayEnum.MAILBOX
  }
]
const tabCurrent = ref<string | number>(1)

const loginWayListsFilter = computed(() => {
  return loginWayLists.filter((item) => getLoginWay.value.includes(item.type))
})

const getLoginWay = computed<string[]>(
  () => appStore.getLoginConfig?.login_way || []
)

const getRegisterWay = computed<string[]>(
  () => appStore.getLoginConfig?.register_way || []
)

const isOpenAgreement = computed(
    () => appStore.getLoginConfig.is_agreement === 1
)

const loginWay = computed(() => {
    tabCurrent.value = appStore.getLoginConfig.default_login_way.toString()
    return appStore.getLoginConfig.default_login_way.toString()
})

const tabChange = (name: string | number) => {
    tabCurrent.value = name
}
</script>

<style lang="scss" scoped>
.el-tabs {
  :deep(.el-tabs__item) {
    @apply text-xl;
  }
}
</style>
