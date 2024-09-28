import { ClientEnum } from '@/enums/appEnums'
import wechatoa from '../utils/wechat'
import { getClient } from '@/utils/client'

export default defineNuxtRouteMiddleware(async (to, from) => {
    if (getClient() == ClientEnum.WEIXIN_OA) {
        wechatoa.config()
    }
    const { code } = to.query
    if (code) {
        wechatoa.setAuthData({ code })
        delete to.query.code
        delete to.query.state
        return navigateTo(`${to.path}`)
    }
})
