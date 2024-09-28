/**
 * @description 角色列表
 * @param { Object } params
 * @return { Promise }
 */
export function getRoleList(params?: any) {
    return $request.get({ url: '/chat.skill/lists', params })
}

/**
 * @description 角色详情
 * @param { Object } params
 * @return { Promise }
 */
export function getRoleDetail(params: any) {
    return $request.get({ url: '/chat.skill/detail', params })
}