import request from '@/utils/request'

//问题分类列表
export function getQuestionSampleList(params: any) {
    return request.get({ url: '/chat.chat_sample/lists', params })
}

//新增问题示例
export function addQusetionSample(params: any) {
    return request.post({ url: '/chat.chat_sample/add', params })
}

//编辑问题示例
export function editQusetionSample(params: any) {
    return request.post({ url: '/chat.chat_sample/edit', params })
}

//删除问题示例
export function delQusetionSample(params: any) {
    return request.post({ url: '/chat.chat_sample/del', params })
}
//修改状态
export function editQusetionSampleStatus(params: any) {
    return request.post({ url: '/chat.chat_sample/status', params })
}
