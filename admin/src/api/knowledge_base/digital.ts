import request from '@/utils/request'
/**
 * @description 形象列表
 */
export function getDigitalLists(params: any) {
    return request.get({
        url: '/kb.digital/lists',
        params
    })
}
/**
 * @description 智能体分类详情
 */
export function getDigitalDetail(params: any) {
    return request.get({
        url: '/kb.digital/detail',
        params
    })
}

/**
 * @description 智能体分类删除
 */
export function delDigital(params: any) {
    return request.post({
        url: '/kb.digital/del',
        params
    })
}

/**
 * @description 智能体分类状态修改
 */
export function putDigitalStatus(params: any) {
    return request.post({
        url: '/kb.digital/changeStatus',
        params
    })
}
