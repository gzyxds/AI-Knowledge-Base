import request from '@/utils/request'

// 获取公告配置
export function getChatConfig(params?: any) {
    return request.get({ url: '/setting.ai.chat/detail', params })
}

// 设置公告配置
export function setChatConfig(params?: any) {
    return request.post({ url: '/setting.ai.chat/save', params })
}
