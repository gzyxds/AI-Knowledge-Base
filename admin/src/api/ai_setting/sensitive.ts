import request from '@/utils/request'

// 敏感词列表
export function getlists(params: any) {
    return request.get({ url: '/setting.sensitiveWord/lists', params })
}
export function addSensitive(data: any) {
    return request.post({ url: '/setting.sensitiveWord/add', data })
}
export function editSensitive(data: any) {
    return request.post({ url: '/setting.sensitiveWord/edit', data })
}
export function delSensitive(data: any) {
    return request.post({ url: '/setting.sensitiveWord/del', data })
}

export function setSensitiveConfig(data: any) {
    return request.post({ url: '/setting.sensitiveWord/setConfig', data })
}

export function getSensitiveConfig() {
    return request.get({ url: '/setting.sensitiveWord/getConfig' })
}
