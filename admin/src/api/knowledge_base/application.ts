import request from '@/utils/request'

//应用列表
export function getApplyManageLists(params: any) {
    return request.get({ url: '/kb.robot/lists', params })
}

//全部应用列表
export function getApplyManageAll() {
    return request.get({ url: '/know.applyManage/all' })
}

//应用详情
export function getApplyManageDetail(params: any) {
    return request.get({ url: '/kb.robot/detail', params })
}

//应用删除
export function applyManageDelete(params: any) {
    return request.post({ url: '/kb.robot/del', params })
}

//修改状态
export function applyManageStatus(params: any) {
    return request.post({ url: '/kb.robot/changeStatus', params })
}
