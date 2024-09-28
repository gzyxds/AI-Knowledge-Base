import type { FileParams } from 'ofetch'
import { terminal } from '@/config'
//发送短信
export function smsSend(params: any) {
    return $request.post({ url: '/sms/sendCode', params })
}

// 获取配置
export function getConfig() {
    return $request.get({ url: '/index/config' })
}

// 获取配置
export function getCustomerConfig() {
    return $request.get({ url: '/index/getCustomerService' })
}

// 上传图片
export function uploadImage(params: any) {
    return $request.uploadFile({ url: '/upload/image' }, params)
}

// 上传文件
type type = 'image' | 'video' | 'file' | 'audio'
export function uploadFile(type: type, params: FileParams) {
    return $request.uploadFile(
        {
            url: `/upload/${type}`,
            headers: params.header
        },
        params
    )
}

// 获取协议
export function getPolicy(params: any) {
    return $request.get({ url: '/index/policy', params })
}

export function indexVisit() {
    return $request.post({
        url: '/index/visit',
        params: { terminal: getClient() }
    })
}

// 装修页面
export function getDecorate(params: any) {
    return $request.get({ url: '/index/decorate', params })
}

// 装修页面
export function getAiModels(params?: any) {
    return $request.get({ url: '/index/models', params })
}

export function getMindMapConfig(params?: any) {
    return $request.get({ url: '/index/mindMapConfig', params })
}

// 通知列表
export function noticeLists(params?: any) {
    return $request.get({ url: '/notice/lists', params })
}

// 标记已读
export function noticeRead(params?: { id: number }) {
    return $request.post({ url: '/notice/read', params })
}

// 全部已读
export function noticeAllRead() {
    return $request.post({ url: '/notice/allRead' })
}

// 清空所有已读
export function noticeClear() {
    return $request.post({ url: '/notice/read' })
}
