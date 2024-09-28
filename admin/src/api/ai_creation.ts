import request from '@/utils/request'

// 创作分类列表
export function getCreationModelList(params: any) {
    return request.get(
        { url: '/creation.creationModel/lists', params },
        {
            ignoreCancelToken: true
        }
    )
}

// 创作列表导出
export function getCreationModelListExport(params: any) {
    return request.get(
        { url: '/creation.creationModel/export', params },
        {
            ignoreCancelToken: true
        }
    )
}

// 创作分类列表
export function getCreationModelDetail(params: any) {
    return request.get({ url: '/creation.creationModel/detail', params })
}

// 新增创作分类
export function postCreationModel(params: any) {
    return request.post({ url: '/creation.creationModel/add', params })
}
// 编辑创作分类
export function putCreationModel(params: any) {
    return request.post({ url: '/creation.creationModel/edit', params })
}
// 删除创作分类
export function delCreationModel(params: any) {
    return request.post({ url: '/creation.creationModel/del', params })
}
// 删除创作分类
export function batchDelCreationModel(params: any) {
    return request.post({ url: '/creation.creationModel/batchDel', params })
}
// 更新创作分类状态
export function putCreationModelStatus(params: any) {
    return request.post({ url: '/creation.creationModel/status', params })
}

// 创作分类列表
export function getCreationCategoryList(params?: any) {
    return request.get(
        { url: '/creation.creationCategory/lists', params },
        {
            ignoreCancelToken: true
        }
    )
}

// 新增创作分类
export function postCreationCategory(params: any) {
    return request.post({ url: '/creation.creationCategory/add', params })
}
// 编辑创作分类
export function putCreationCategory(params: any) {
    return request.post({ url: '/creation.creationCategory/edit', params })
}
// 删除创作分类
export function delCreationCategory(params: any) {
    return request.post({ url: '/creation.creationCategory/del', params })
}
// 更新创作分类状态
export function putCreationCategoryStatus(params: any) {
    return request.post({ url: '/creation.creationCategory/status', params })
}
