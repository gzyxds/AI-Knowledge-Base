import request from '@/utils/request'
//分享列表
export function getAgentLists(params: any) {
    return request.get({ url: '/market.task/robotLogLists', params }, { ignoreCancelToken: true })
}
//设置详情
export function getAgentConfig(params?: any) {
    return request.get({ url: '/market.activityReward/getRobotSetting', params })
}
//编辑分享设置
export function editAgentConfig(params: any) {
    return request.post({ url: '/market.activityReward/setRobotSetting', params })
}