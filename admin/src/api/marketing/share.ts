import request from '@/utils/request'
//分享列表
export function getShareLists(params: any) {
    return request.get({ url: '/market.task/ShareLogLists', params }, { ignoreCancelToken: true })
}
//设置详情
export function getShareConfig(params?: any) {
    return request.get({ url: '/market.activityReward/getShareSetting', params })
}
//编辑分享设置
export function editShareConfig(params: any) {
    return request.post({ url: '/market.activityReward/setShareSetting', params })
}
