import request from '@/utils/request'

export function getCreationLists(data?: any) {
    return request.get({ url: '/chat.creation/lists', data })
}
export function getCategoryList(data?: any) {
    return request.get({ url: '/chat.creation/category', data })
}

export function getCreationList(data?: any) {
    return request.get({ url: '/chat.creation/lists', data })
}

export function getCreationDetail(data: any) {
    return request.get({ url: '/chat.creation/detail', data })
}

//收藏列表
export function collection(data: any) {
    return request.post({ url: '/chat.creation/collect', data })
}
