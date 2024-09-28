import request from '@/utils/request'

// 获取备案信息
export function getCopyright() {
    return request.get({ url: '/setting.website/getCopyright' })
}
// 设置备案信息
export function setCopyright(params: any) {
    return request.post({ url: '/setting.website/setCopyright', params })
}
// 获取网站信息
export function getWebsite() {
    return request.get({ url: '/setting.website/getWebsite' })
}
// 设置网站信息
export function setWebsite(params: any) {
    return request.post({ url: '/setting.website/setWebsite', params })
}

// 获取政策协议
export function getProtocol() {
    return request.get({ url: '/setting.website/getAgreement' })
}
// 设置政策协议
export function setProtocol(params: any) {
    return request.post({ url: '/setting.website/setAgreement', params })
}
