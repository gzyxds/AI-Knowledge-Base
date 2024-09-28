import {defineStore} from 'pinia'
import {useAppStore} from './app'
import {getUserCenter, shareInvite, shareClick} from '@/api/user'
import {TOKEN_KEY, VISITOR_ID} from '@/enums/cacheEnums'
import {LoginPopupTypeEnum} from '~/enums/appEnums'
import {useLocalStorage} from '@vueuse/core'

interface UserState {
    userInfo: Record<string, any>
    token: string | undefined | null
    temToken: string | null
    showLogin: boolean
    loginPopupType: LoginPopupTypeEnum
    showRecharge: boolean
    visitorId: string
}

export const useUserStore = defineStore({
    id: 'userStore',
    state: (): UserState => {
        const TOKEN = useCookie(TOKEN_KEY)
        const visitorId = useLocalStorage(VISITOR_ID, '')
        return {
            visitorId: visitorId.value || '',
            userInfo: {},
            token: TOKEN.value,
            temToken: null,
            showLogin: false,
            loginPopupType: LoginPopupTypeEnum.LOGIN,
            showRecharge: false
        }
    },
    getters: {
        isLogin: (state) => !!state.token,
        isTourist: (state) => !!state.userInfo.is_tourist
    },
    actions: {
        async getUser() {
            const data = await getUserCenter()
            this.userInfo = data
            this.checkShare()
        },
        //弹起登录二维码
        toggleShowLogin(toggle?: boolean) {
            this.showLogin = toggle ?? !this.showLogin
        },
        //弹起开通vip
        toggleShowRecharge(toggle?: boolean) {
            const appStore = useAppStore()
            if (this.isTourist && appStore.getLoginConfig.tourist_order !== 1) {
                this.toggleShowLogin(true)
                this.setLoginPopupType()
                return
            }
            this.showRecharge = toggle ?? !this.showRecharge
        },
        setLoginPopupType(type: LoginPopupTypeEnum = LoginPopupTypeEnum.LOGIN) {
            this.loginPopupType = type
        },
        setUser(userInfo: any) {
            this.userInfo = userInfo
        },
        login(token: string) {
            const oneYear = 360 * 24 * 60 * 60 * 1000
            const TOKEN = useCookie(TOKEN_KEY, {
                expires: new Date(Date.now() + oneYear)
            })

            this.token = token
            TOKEN.value = token
        },
        logout() {
            const TOKEN = useCookie(TOKEN_KEY)
            this.token = null
            this.userInfo = {}
            TOKEN.value = null
        },
        async getFingerprint() {
            const visitorId = useLocalStorage(VISITOR_ID, '')
            if (this.visitorId) return this.visitorId
            this.visitorId = uniqueId()
            visitorId.value = this.visitorId
            return this.visitorId
        },
        //分享/邀请
        async checkShare() {
            // 获取 Cookie 值
            const user_sn = useCookie('user_sn');
            const share_id = useCookie('share_id');

            // 判断是否存在需要处理的值
            const hasShareId = share_id.value;
            const hasUserSn = user_sn.value;

            // 处理分享点击
            if (hasShareId) {
                shareClick({ share_id: share_id.value });
            }

            // 处理邀请
            if (this.isLogin && (hasUserSn || hasShareId)) {
                await shareInvite({
                    share_id: share_id.value,
                    user_sn: user_sn.value
                });
            }

            // 清空 Cookie 值
            [share_id, user_sn].forEach(cookie => cookie.value = null);
        }
    }
})
