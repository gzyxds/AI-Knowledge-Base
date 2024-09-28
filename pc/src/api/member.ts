// 会员套餐列表
export function memberPackageList() {
    return $request.get({ url: '/memberPackage/lists' })
}

// 会员套餐购买
export function memberPackageBuy(params: { member_price_id: number }) {
    return $request.post({ url: '/memberPackage/buy', params })
}

// 会员套餐列表
export function memberPackageRecord() {
    return $request.get({ url: '/memberPackage/buyLog' })
}
