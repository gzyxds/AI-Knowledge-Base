import request from '@/utils/request'
//分享列表
export function getSignLists(params: any) {
    return request.get({ url: '/market.task/signLogLists', params }, { ignoreCancelToken: true })
}
//设置详情
export function getSignConfig(params?: any) {
    return request.get({ url: '/market.activityReward/getSignSetting', params })
}
//编辑分享设置
export function editSignConfig(params: any) {
    return request.post({ url: '/market.activityReward/setSignSetting', params })
}
