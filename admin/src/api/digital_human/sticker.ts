import request from '@/utils/request'
// 贴纸列表
export function getStickerList(params?: any) {
    return request.get({ url: '/digital.decals/lists', params })
}

// 添加贴纸
export function addSticker(params?: any) {
    return request.post({ url: '/digital.decals/add', params })
}

// 编辑贴纸
export function editSticker(params?: any) {
    return request.post({ url: '/digital.decals/edit', params })
}

// 修改贴纸状态
export function changMusicStatus(params?: any) {
    return request.post({ url: '/digital.decals/status', params })
}

// 删除贴纸
export function delSticker(params?: any) {
    return request.post({ url: '/digital.decals/del', params })
}

// 获取分类列表
export function getCategoryLists(params?: any) {
    return request.get({ url: '/digital.decals/getCategoryLists', params })
}

//批量修改贴纸
export function bathEditSticker(params?: any) {
    return request.post({ url: '/digital.decals/batchEdit', params })
}

//批量删除贴纸
export function bathDelSticker(params?: any) {
    return request.post({ url: '/digital.decals/batchDel', params })
}

// 贴纸分类列表
export function getStickerCategoryList(params?: any) {
    return request.get({ url: '/digital.decalsCategory/lists', params })
}

// 添加分类
export function addCategory(params?: any) {
    return request.post({ url: '/digital.decalsCategory/add', params })
}

// 编辑分类
export function editCategory(params?: any) {
    return request.post({ url: '/digital.decalsCategory/edit', params })
}

// 修改分类状态
export function changCategoryStatus(params?: any) {
    return request.post({ url: '/digital.decalsCategory/status', params })
}

// 删除分类
export function delCategory(params?: any) {
    return request.post({ url: '/digital.decalsCategory/del', params })
}
