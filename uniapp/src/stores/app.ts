import { defineStore } from 'pinia'
import { getConfig } from '@/api/app'
import { getDecorate } from '@/api/shop'
interface AppSate {
    config: Record<string, any>
    tabbar: Record<string, any>
}
export const useAppStore = defineStore({
    id: 'appStore',
    state: (): AppSate => ({
        config: {},
        tabbar: {
            lists: [],
            style: {}
        }
    }),
    getters: {
        getCardCodeConfig: (state) => state.config.card_code || {},
        getBulletinConfig: (state) => state.config.bulletin_config || {},
        getWebsiteConfig: (state) => state.config.website || {},
        getLoginConfig: (state) => state.config.login || {},
        getTabbarConfig: (state) => state.tabbar || {},
        getH5Config: (state) => state.config.webPage || {},
        getShareConfig: (state) => state.config.share || {},
        getIsShowVip: (state) => state.config.member_package_status || false,
        getIsShowRecharge: (state) =>
            state.config.switch?.recharge_status || false,
        getChatConfig: (state) => state.config.chat || {},
        getDrawConfig: (state) => state.config.draw || {},
        getDrawSquareConfig: (state) => state.config.draw_square_config || {},
        getMindMapConfig: (state) => state.config.mindmap_config || {},
        getSwitchConfig: (state) => state.config.switch || {},
        //语音播报
        getIsVoiceOpen: (state) => !!state.config.switch?.voice_status || false,
        getTokenUnit: (state) => state.config.chat?.price_unit || '电力值',
        getSquareConfig: (state) => state.config.square_config || {},
        getCopyrightConfig: (state) => state.config.copyright || [],
    },
    actions: {
        getImageUrl(url: string) {
            return url.indexOf('http') ? `${this.config.domain}${url}` : url
        },
        async getConfig(payload?: any) {
            const data = await getConfig(payload)
            this.config = data
        },
        async getTabbar() {
            const data = await getDecorate({
                id: 4
            })
            this.tabbar = JSON.parse(data?.data)
        }
    }
})
