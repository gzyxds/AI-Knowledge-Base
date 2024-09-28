import { getUserCenter } from '@/api/user'
import { TOKEN_KEY, VISITOR_ID } from '@/enums/constantEnums'
import { getToken } from '@/utils/auth'
import cache from '@/utils/cache'
import { defineStore } from 'pinia'
import { uniqueId } from '@/utils/unique-id'
interface UserSate {
    visitorId: string
    userInfo: Record<string, any>
    token: string | null
    temToken: string | null
}
export const useUserStore = defineStore({
    id: 'userStore',
    state: (): UserSate => ({
        visitorId: cache.get(VISITOR_ID) || '',
        userInfo: {},
        token: getToken() || null,
        temToken: null
    }),
    getters: {
        isLogin: (state) => !!state.token
    },
    actions: {
        async getUser() {
            const data = await getUserCenter({
                token: this.token
            })
            this.userInfo = data
        },
        login(token: string) {
            this.token = token
            cache.set(TOKEN_KEY, token)
        },
        logout() {
            this.token = ''
            this.userInfo = {}
            cache.remove(TOKEN_KEY)
        },
        async getFingerprint() {
            if (this.visitorId) return this.visitorId
            this.visitorId = uniqueId()
            cache.set(VISITOR_ID, this.visitorId)
            return this.visitorId
        }
    }
})
