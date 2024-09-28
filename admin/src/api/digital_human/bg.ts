import request from '@/utils/request'

// 背景列表
export function getBGList(params?: any) {
    return request.get({ url: '/digital.background/lists', params })
}

// 添加背景
export function addBG(params?: any) {
    return request.post({ url: '/digital.background/add', params })
}

// 编辑背景
export function editBG(params?: any) {
    return request.post({ url: '/digital.background/edit', params })
}

// 修改背景状态
export function changBGStatus(params?: any) {
    return request.post({ url: '/digital.background/status', params })
}

// 删除背景
export function delBG(params?: any) {
    return request.post({ url: '/digital.background/del', params })
}

// 获取背景分类列表
export function getCategoryLists(params?: any) {
    return request.post({ url: '/digital.background/getCategoryLists', params })
}

//批量修改背景
export function batchEdit(params?: any) {
    return request.post({ url: '/digital.background/batchEdit', params })
}

//批量删除
export function batchDel(params?: any) {
    return request.post({ url: '/digital.background/batchDel', params })
}

// 背景图分类列表
export function getBGCategoryList(params?: any) {
    return request.get({ url: '/digital.backgroundCategory/lists', params })
}

// 添加分类
export function addCategory(params?: any) {
    return request.post({ url: '/digital.backgroundCategory/add', params })
}

// 编辑分类
export function editCategory(params?: any) {
    return request.post({ url: '/digital.backgroundCategory/edit', params })
}

// 修改分类状态
export function changCategoryStatus(params?: any) {
    return request.post({ url: '/digital.backgroundCategory/status', params })
}

// 删除分类
export function delCategory(params?: any) {
    return request.post({ url: '/digital.backgroundCategory/del', params })
}
