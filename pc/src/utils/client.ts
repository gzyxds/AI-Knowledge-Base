import isMobileJs from 'ismobilejs'
import { ClientEnum } from '@/enums/appEnums'

/**
 * @description 判断是否为微信环境
 * @return { Boolean }
 */
export const isWeixinClient = () => {
    return /MicroMessenger/i.test(navigator.userAgent)
}

/**
 * @description 获取当前是什么端
 * @return { Object }
 */

export const getClient = () => {
    if (isMobile) {
        if (isWeixinClient()) {
            return ClientEnum.WEIXIN_OA
        }
        return ClientEnum.H5
    } else {
        return ClientEnum.PC
    }
}

interface ClientCallbacks {
    PC?(): any

    H5?(): any

    WEIXIN_OA?(): any
}

export const handleClientCallback = (callbacks: ClientCallbacks) => {
    const c = ClientEnum[client]
    const callback = callbacks[c as keyof ClientCallbacks] || function () {}

    return callback()
}

export const isMobile = (() => {
    if (process.client) {
        return isMobileJs().any
    }
})()

export const client = getClient()
