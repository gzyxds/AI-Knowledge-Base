import request from '@/utils/request'
//分享列表
export function getWorksLists(params: any) {
    return request.get({ url: '/market.task/workdsLogLists', params }, { ignoreCancelToken: true })
}
//设置详情
export function getWorksConfig(params?: any) {
    return request.get({ url: '/market.activityReward/getWorkSetting', params })
}
//编辑分享设置
export function editWorksConfig(params: any) {
    return request.post({ url: '/market.activityReward/setWorkSetting', params })
}
