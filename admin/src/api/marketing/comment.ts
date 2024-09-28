import request from '@/utils/request'
//评价列表
export function commentLists(params: any) {
    return request.get(
        { url: '/member.member_package_comment/lists', params },
        {
            ignoreCancelToken: true
        }
    )
}
//删除评价
export function commentDel(params: { id: number }) {
    return request.post({ url: '/member.member_package_comment/del', params })
}
//添加评价
export function commentAdd(params: any) {
    return request.post({ url: '/member.member_package_comment/add', params })
}
