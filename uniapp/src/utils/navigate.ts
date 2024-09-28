import { useNavigationBarTitleStore } from '@/stores/navigationBarTitle'
import { objectToQuery } from './util'
import router from '@/router'

/**
 * @description 后台选择链接专用跳转
 */
interface Link {
    path: string
    name?: string
    type: string
    isTab: boolean
    query?: Record<string, any>
}

export enum LinkTypeEnum {
    'SHOP_PAGES' = 'shop',
    'CUSTOM_LINK' = 'custom'
}

export function navigateTo(link: Link) {
    let { path, query, type } = link
    if (type === LinkTypeEnum.CUSTOM_LINK) {
        query = { url: path }
        path = '/pages/webview/webview'
    }

    const navigationBarTitleStore = useNavigationBarTitleStore()
    navigationBarTitleStore.add({
        path: path,
        title: link.name as string
    })
    const routeRaw = {
        path,
        query
    }
    const route = router.resolve(routeRaw)
    if (route?.meta.isTab) {
        router.switchTab(routeRaw)
    } else if (link.isTab) {
        router.reLaunch(routeRaw)
    } else {
        router.navigateTo(routeRaw)
    }
}
