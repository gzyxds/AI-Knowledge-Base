import request from '@/utils/request'

/**
 * @description 角色分类列表
 */
export function getRoleCateList() {
    return request.get({
        url: '/chat.skill/categoryLists'
    })
}

/**
 * @description 角色列表
 */
export function getRoleMobileList(data: any) {
    return request.get({
        url: '/chat.skill/mobileLists',
        data
    })
}


/**
 * @description 角色详情
 */
export function getRoleDetail(data: any) {
    return request.get({
        url: '/chat.skill/detail',
        data
    })
}