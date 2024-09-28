import request from '@/utils/request'

// 音乐列表
export function getMusicList(params?: any) {
    return request.get({ url: '/digital.music/lists', params })
}

// 添加音乐
export function addMusic(params?: any) {
    return request.post({ url: '/digital.music/add', params })
}

// 编辑音乐
export function editMusic(params?: any) {
    return request.post({ url: '/digital.music/edit', params })
}

// 修改音乐状态
export function changMusicStatus(params?: any) {
    return request.post({ url: '/digital.music/status', params })
}

// 删除音乐
export function delMusic(params?: any) {
    return request.post({ url: '/digital.music/del', params })
}

// 获取音乐分类列表
export function getCategoryLists(params?: any) {
    return request.post({ url: '/digital.music/getCategoryLists', params })
}

//批量修改
export function batchEdit(params?: any) {
    return request.post({ url: '/digital.music/batchEdit', params })
}

//批量删除
export function batchDel(params?: any) {
    return request.post({ url: '/digital.music/batchDel', params })
}

// 音乐分类列表
export function getMusicCategoryList(params?: any) {
    return request.get({ url: '/digital.musicCategory/lists', params })
}

// 添加分类
export function addCategory(params?: any) {
    return request.post({ url: '/digital.musicCategory/add', params })
}

// 编辑分类
export function editCategory(params?: any) {
    return request.post({ url: '/digital.musicCategory/edit', params })
}

// 修改分类状态
export function changCategoryStatus(params?: any) {
    return request.post({ url: '/digital.musicCategory/status', params })
}

// 删除分类
export function delCategory(params?: any) {
    return request.post({ url: '/digital.musicCategory/del', params })
}
