import request from '@/utils/request'
//邀请列表
export function getInviteLists(params: any) {
    return request.get({ url: '/market.task/inviteLogLists', params }, { ignoreCancelToken: true })
}
//设置详情
export function getInviteConfig(params?: any) {
    return request.get({ url: '/market.activityReward/getInviteSetting', params })
}
//编辑设置
export function editInviteConfig(params: any) {
    return request.post({ url: '/market.activityReward/setInviteSetting', params })
}
