import { defineStore } from 'pinia'
import { getConfig, getDecorate } from '@/api/app'
import { getFontsList } from '@/api/digital_human'
interface AppState {
    config: Record<string, any>
    isMobile: boolean
    isCollapsed: boolean
    fontList: {
        name: string
        code: string
        url: string
    }[]
    pageIndex: any[]
    pageAside: Record<string, any>
}
export const useAppStore = defineStore({
    id: 'appStore',
    state: (): AppState => ({
        config: {},
        isMobile: true,
        isCollapsed: false,
        fontList: [],
        pageIndex: [],
        pageAside: {
            nav: []
        }
    }),
    getters: {
        getImageUrl: (state) => (url: string) =>
            url ? `${state.config.domain}${url}` : '',
        getWebsiteConfig: (state) => state.config.website || {},
        getLoginConfig: (state) => state.config.login || {},
        getChatConfig: (state) => state.config.chat || {},
        getCopyrightConfig: (state) => state.config.copyright || [],
        getQrcodeConfig: (state) => state.config.qrcode || {},
        getAdminUrl: (state) => state.config.admin_url,
        getIsShowVip: (state) => state.config.member_package_status || false,
        getIsShowRecharge: (state) =>
            state.config.switch?.recharge_status || false,
        getIsShowMember: (state) => state.config?.member_package_open || false,
        getBulletinConfig: (state) => state.config.bulletin || {},
        getPcStatus: (state) => state.config.switch?.pc_status || false,
        getAccountCancelledStatus: (state) =>
            state.config.switch?.account_cancelled || false,
        getIsVoiceOpen: (state) => !!state.config.switch?.voice_status || false,
        getHeaderConfig: (state) =>
            state.pageIndex.find((item) => item.name == 'header')?.prop || {},
        getAvatarConfig: (state) => state.config.avatar_etting || {},
        getManualKf: (state) => state.config.manualKf || {},
        getOnlineKf: (state) => state.config.onlineKf || {},
        getTokenUnit: (state) => state.config.chat?.price_unit || '电力值',
        getIsInstall: (state) => state.config.install ?? true,
        getRedeemCode: (state) => state.config.card_code || {},
        getSquareConfig: (state) => state.config.square_config || {},
    },
    actions: {
        async getConfig() {
            const config = await getConfig()
            if (isObject(config)) {
                this.config = config
            }
        },
        setMobile(value: boolean) {
            this.isMobile = value
        },
        toggleCollapsed(toggle?: boolean) {
            this.isCollapsed = toggle ?? !this.isCollapsed
        },
        async getPageIndex() {
            try {
                const data = await getDecorate({ id: 1 })
                this.pageIndex = JSON.parse(data.data)
            } catch (error) {
                console.error(error)
            }
        },
        async getPageAside() {
            try {
                const data = await getDecorate({ id: 8 })
                this.pageAside = JSON.parse(data.data)
            } catch (error) {
                console.error(error)
            }
        },

        async init() {
            await Promise.all([
                this.getConfig(),
                // this.initFont(),
                this.getPageAside(),
                this.getPageIndex()
            ])
        },
        async initFont() {
            try {
                const data = await getFontsList()
                this.fontList = data
                const fontContent = this.fontList.reduce((prev, item) => {
                    prev = `${prev}
  @font-face {
    font-family: ${item.code};
    src: url(${item.url})
  }
          `
                    return prev
                }, '')
                const style = document.createElement('style')
                style.innerHTML = fontContent
                document.head.appendChild(style)
            } catch (error) {
                console.error(error)
            }
        }
    }
})
