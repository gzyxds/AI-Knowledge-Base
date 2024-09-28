import request from '@/utils/request'

// 会员套餐列表
export function memberPackageList() {
    return request.get({ url: '/memberPackage/lists' }, { isAuth: true })
}


// 会员套餐购买
export function memberPackageBuy(data: { member_price_id: number }) {
    return request.post({ url: '/memberPackage/buy', data })
}

// 会员套餐购买记录
export function memberPackageBuyLog() {
    return request.get({ url: '/memberPackage/buyLog' }, { isAuth: true })
}