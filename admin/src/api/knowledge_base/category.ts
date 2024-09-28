import request from '@/utils/request'

//问题分类列表
export function getCategoryList(params?: any) {
    return request.get({ url: '/know.applyCategory/lists', params })
}

//新增问题分类
export function addCategory(params: any) {
    return request.post({ url: '/know.applyCategory/add', params })
}

//新增问题分类
export function editCategory(params: any) {
    return request.post({ url: '/know.applyCategory/edit', params })
}

//删除问题分类
export function delCategory(params: any) {
    return request.post({ url: '/know.applyCategory/del', params })
}
//修改状态
export function editStatus(params: any) {
    return request.post({ url: '/know.applyCategory/status', params })
}
