import request, { RequestEventStreamConfig } from '@/utils/request'

//发送短信
export function smsSend(data: any) {
    return request.post({ url: '/sms/sendCode', data: data })
}

export function getConfig(data: any) {
    return request.get({ url: '/index/config', data })
}

export function getPolicy(data: any) {
    return request.get({ url: '/index/policy', data: data })
}

export function getMnpQrCode(data: any) {
    return request.post({ url: '/share/getMnpQrCode', data: data })
}

export function uploadImage(file: any, data?: any, token?: string) {
    return request.uploadFile({
        url: '/upload/image',
        filePath: file,
        name: 'file',
        header: {
            token
        },
        formData: data,
        fileType: 'image'
    })
}

export function uploadFile(
    options: Omit<UniApp.UploadFileOption, 'url'>,
    onProgress?: (progress: number) => void
) {
    return request.uploadFile(
        { ...options, url: '/upload/file', name: 'file' },
        {
            onProgress
        }
    )
}

export function uploadFileByType(
    type: 'image' | 'file' | 'video',
    options: Omit<UniApp.UploadFileOption, 'url'>,
    onProgress?: (progress: number) => void
) {
    return request.uploadFile(
        { ...options, url: `/upload/${type}`, name: 'file' },
        {
            onProgress
        }
    )
}

export function wxJsConfig(data: any) {
    return request.get({ url: '/wechat/jsConfig', data })
}

//获取聊天模型接口
export function getAiModel(data?: any) {
    return request.get({ url: '/index/models', data }, { ignoreCancel: true })
}

export function getMindMapConfig(data?: any) {
    return request.get({ url: '/index/mindMapConfig', data })
}

// 消息通知列表
export function noticeLists(data?: any) {
    return request.get({ url: '/notice/lists', data })
}

// 标记已读
export function noticeRead(data?: { id: number }) {
    return request.post({ url: '/notice/read', data })
}

// 全部已读
export function noticeAllRead() {
    return request.post({ url: '/notice/allRead' })
}

// 清空所有已读
export function noticeClear() {
    return request.post({ url: '/notice/read' })
}
