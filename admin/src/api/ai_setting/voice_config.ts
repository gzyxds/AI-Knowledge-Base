import request from '@/utils/request'

export function getVoiceConfig() {
    return request.get({ url: '/setting.voice/detail' })
}

export function setVoiceConfig(params: any) {
    return request.post({ url: '/setting.voice/save', params })
}
