<template>
    <ElConfigProvider v-bind="config">
        <NuxtLayout>
            <NuxtLoadingIndicator
                color="var(--el-color-primary)"
                :height="2"
                :throttle="0"
            />
            <NuxtPage />
        </NuxtLayout>
    </ElConfigProvider>
</template>

<script lang="ts" setup>
import { ID_INJECTION_KEY } from 'element-plus'
import zhCn from 'element-plus/es/locale/lang/zh-cn'
import { useWindowSize, watchThrottled } from '@vueuse/core'
import { indexVisit } from './api/app'
import { useAppStore } from './stores/app'
import { ScreenEnum } from './enums/appEnums'
import { useSettingStore } from './stores/setting'
import { useUserStore } from '~/stores/user'

const router = useRouter()
const route = useRoute()
const appStore = useAppStore()
const userStore = useUserStore()
const settingStore = useSettingStore()

provide(ID_INJECTION_KEY, {
    prefix: 100,
    current: 0
})
const config = {
    locale: zhCn
}

if (process.client) {
    settingStore.setTheme()
}

await appStore.init()
const { pc_ico, pc_title, pc_desc, pc_key } = appStore.getWebsiteConfig
useHead({
    title: pc_title,
    link: [
        {
            rel: 'icon',
            href: pc_ico
        }
    ],
    meta: [
        {
            name: 'keywords',
            content: pc_key
        },
        {
            name: 'description',
            content: pc_desc
        }
    ]
})

const { width } = useWindowSize()
watchThrottled(
    width,
    (value) => {
        if (value > ScreenEnum.SM) {
            appStore.setMobile(false)
            appStore.toggleCollapsed(false)
        } else {
            appStore.setMobile(true)
            appStore.toggleCollapsed(true)
        }
    },
    {
        immediate: true
    }
)
// 重定向到安装路径，由于将跟路径/pc/ => 改为 /
if (!appStore.getIsInstall) {
    window.location.replace('/install/install.php')
    // await router.replace({ path: '' })
} else if (!appStore.getPcStatus) {
    await router.replace({ path: '/empty' })
}

// 保存分享参数
if (route.query.share_id || route.query.user_sn) {
    const user_sn: any = useCookie('user_sn')
    const share_id: any = useCookie('share_id')

    user_sn.value = route.query.user_sn
    share_id.value = route.query.share_id

    await nextTick()
    userStore.checkShare()
}

indexVisit()
</script>
