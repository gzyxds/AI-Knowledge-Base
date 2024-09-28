import request from '@/utils/request'

// 音乐风格
export function getVideoConfig() {
    return request.get({ url: '/video/config' })
}

// 音乐列表
export function getVideoLists(data: any) {
    return request.get({ url: '/video/lists', data })
}

// 歌词联想
export function postVideoGenerate(data: any) {
    return request.post({ url: '/video/generate', data }, { isAuth: true })
}

// 音乐详情
export function getVideoDetail(data: any) {
    return request.get({ url: '/video/detail', data }, { isAuth: true })
}

// 删除音乐
export function deleteVideo(data: any) {
    return request.post({ url: '/video/del', data }, { isAuth: true })
}

export function translate(data: any) {
    return request.get({ url: '/video/translate', data })
}
