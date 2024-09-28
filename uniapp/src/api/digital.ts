import request from '@/utils/request'

/**
 * @description 配音角色
 */
export function getDubbingList() {
    return request.get({
        url: '/kb.digital/dubbing'
    })
}

/**
 * @description 形象列表
 */
export function getDigitalList(data: any) {
    return request.get({
        url: '/kb.digital/lists',
        data
    })
}

/**
 * @description 形象详情
 */
export function getDigitalDetail(data: any) {
    return request.get({
        url: '/kb.digital/detail',
        data
    })
}

/**
 * @description 形象添加
 */
export function postDigital(data: any) {
    return request.post({
        url: '/kb.digital/add',
        data
    })
}

/**
 * @description 形象编辑
 */
export function putDigital(data: any) {
    return request.post({
        url: '/kb.digital/edit',
        data
    })
}

/**
 * @description 形象删除
 */
export function delDigital(data: any) {
    return request.post({
        url: '/kb.digital/del',
        data
    })
}
