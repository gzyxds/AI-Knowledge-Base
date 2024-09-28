import request from '@/utils/request'

// 拆分列表
export function getQaLists(params?: any) {
    return request.get({ url: '/know.qa/lists', params })
}

// 导入数据
export function postQa(params?: any) {
    return request.post({ url: '/know.qa/add', params })
}

// 删除数据
export function delQa(params?: any) {
    return request.post({ url: '/know.qa/del', params })
}

// 导出数据
export function exportQa(params?: any) {
    return request.post({ url: '/know.qa/export', params })
}

// 拆分结果
export function getQaResultsLists(params?: any) {
    return request.get({ url: '/know.qa/results', params })
}

// 删除结果数据
export function delQaResults(params?: any) {
    return request.post({ url: '/know.qa/delete', params })
}

// 数据修正
export function putQaResults(params?: any) {
    return request.post({ url: '/know.qa/adjust', params })
}
