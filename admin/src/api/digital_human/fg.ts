import request from '@/utils/request'

// 前景列表
export function getFGList(params?: any) {
    return request.get({ url: '/digital.preposition/lists', params })
}

// 添加前景图
export function addFG(params?: any) {
    return request.post({ url: '/digital.preposition/add', params })
}

// 添加前景图
export function editFG(params?: any) {
    return request.post({ url: '/digital.preposition/edit', params })
}

// 删除前景图
export function deLFG(params?: any) {
    return request.post({ url: '/digital.preposition/del', params })
}

// 删除前景图
export function batchDeLFG(params?: any) {
    return request.post({ url: '/digital.preposition/batchDel', params })
}

// 修改状态
export function changeStatus(params?: any) {
    return request.post({ url: '/digital.preposition/status', params })
}
