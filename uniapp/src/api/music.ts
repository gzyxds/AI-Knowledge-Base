import request from '@/utils/request'

// 音乐风格
export function getMusicConfig() {
    return request.get({ url: '/music/config' })
}

// 音乐列表
export function getMusicLists(data: any) {
    return request.get({ url: '/music/lists', data })
}

// 歌词联想
export function postMusicImagine(data: any) {
    return request.post({ url: '/music/imagine', data }, { isAuth: true })
}

// 歌词联想
export function postMusicGenerate(data: any) {
    return request.post({ url: '/music/generate', data }, { isAuth: true })
}

// 音乐详情
export function getMusicDetail(data: any) {
    return request.get({ url: '/music/detail', data }, { isAuth: false })
}

// 删除音乐
export function deleteMusic(data: any) {
    return request.post({ url: '/music/del', data }, { isAuth: true })
}
