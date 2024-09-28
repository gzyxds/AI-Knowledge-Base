import request from '@/utils/request'

//问答记录列表
export function getKnowRecordsLists(params: any) {
    return request.get({ url: '/kb.robot/chatRecord', params })
}

//问答记录删除
export function knowRecordsDelete(params: any) {
    return request.post({ url: '/kb.robot/chatClean', params })
}
