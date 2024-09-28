import request from '@/utils/request'

/**
 * @description 智能体分类全部
 */
export function getRobotCateAll(params: any) {
    return request.get({
        url: '/kb.robotCate/all',
        params
    })
}

/**
 * @description 智能体分类列表
 */
export function getRobotCateLists(params: any) {
    return request.get({
        url: '/kb.robotCate/lists',
        params
    })
}

/**
 * @description 智能体分类详情
 */
export function getRobotCateDetail(params: any) {
    return request.get({
        url: '/kb.robotCate/detail',
        params
    })
}

/**
 * @description 智能体分类添加
 */
export function postRobotCate(params: any) {
    return request.post({
        url: '/kb.robotCate/add',
        params
    })
}

/**
 * @description 智能体分类编辑
 */
export function putRobotCate(params: any) {
    return request.post({
        url: '/kb.robotCate/edit',
        params
    })
}

/**
 * @description 智能体分类删除
 */
export function delRobotCate(params: any) {
    return request.post({
        url: '/kb.robotCate/del',
        params
    })
}

/**
 * @description 智能体分类状态修改
 */
export function putRobotCateStatus(params: any) {
    return request.post({
        url: '/kb.robotCate/changeStatus',
        params
    })
}

//智能体广场列表
export function getRobotSquareLists(params: any) {
    return request.get({ url: '/kb.square/lists', params })
}

/**
 * @description 智能体广场编辑
 */
export function putRobotSquare(params: any) {
    return request.post({
        url: '/kb.square/edit',
        params
    })
}

/**
 * @description 智能体广场删除
 */
export function delRobotSquare(params: any) {
    return request.post({
        url: '/kb.square/del',
        params
    })
}

/**
 * @description 智能体广场状态修改
 */
export function putRobotSquareStatus(params: any) {
    return request.post({
        url: '/kb.square/setStatus',
        params
    })
}

/**
 * @description 智能体广场排序
 */
export function putRobotSquareSort(params: any) {
    return request.post({
        url: '/kb.square/setSort',
        params
    })
}

/**
 * @description 智能体广场设置获取
 */
export function getRobotSquareConfig() {
    return request.post({
        url: '/kb.square/getConfig'
    })
}

/**
 * @description 智能体广场设置
 */
export function setRobotSquareConfig(params: any) {
    return request.post({
        url: '/kb.square/setConfig',
        params
    })
}

/**
 * @description 智能体广场审核
 */
export function auditAgentSquare(params: any) {
    return request.post({ url: '/kb.square/verifyStatus', params })
}
