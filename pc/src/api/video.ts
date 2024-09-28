// 视频风格
export function getVideoConfig() {
    return $request.get({ url: '/video/config' })
}

// 视频列表
export function getVideoLists(params: any) {
    return $request.get({ url: '/video/lists', params })
}

// 视频生成
export function postVideoGenerate(params: any) {
    return $request.post({ url: '/video/generate', params })
}

// 视频详情
export function getVideoDetail(params: any) {
    return $request.get({ url: '/video/detail', params })
}

// 删除视频
export function deleteVideo(params: any) {
    return $request.post({ url: '/video/del', params })
}

export function translate(params: any) {
    return $request.get({ url: '/video/translate', params })
}
