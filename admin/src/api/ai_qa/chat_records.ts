import request from '@/utils/request'

//对话记录列表
export function chatRecordsLists(params?: any) {
    return request.get({ url: '/chat.chat_record/lists', params })
}

//删除对话记录
export function chatRecordsDel(params?: any) {
    return request.post({ url: '/chat.chat_record/del', params })
}
