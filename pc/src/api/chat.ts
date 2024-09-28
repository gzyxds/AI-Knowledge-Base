import type { RequestOptions } from 'ofetch'

// 个人信息
export function getChatSampleLists() {
    return $request.get({ url: '/chat.chatSample/samplesLists' })
}

//清除会话
export function cleanChatRecord(params: any) {
    return $request.post({
        url: '/chat.chatRecord/chatClean',
        params
    })
}

//删除会话
export function deleteChatRecord(params: { id: number | string; content?: string }) {
    return $request.post({
        url: '/chat.chatRecord/del',
        params
    })
}

//聊天记录
export function getChatRecord(params: any) {
    return $request.get({
        url: '/chat.chatRecord/chatRecord',
        params
    })
}

export function chatUpdate(params: any) {
    return $request.post({ url: '/chat.chatRecord/update', params })
}

export function getChatCategoryLists(params: any) {
    return $request.get({ url: '/chat.chatCategory/lists', params })
}

export function chatCategoryAdd(params: any) {
    return $request.post({ url: '/chat.chatCategory/add', params })
}

export function chatCategoryEdit(params: any) {
    return $request.post({ url: '/chat.chatCategory/edit', params })
}

export function chatCategoryDelete(params: any) {
    return $request.post({ url: '/chat.chatCategory/del', params })
}

export function chatCategoryClear() {
    return $request.post({ url: '/chat.chatCategory/clear' })
}

export function chatSendText(params: any) {
    return $request.sse({
        url: '/chat.chatDialog/completions',
        params,
        method: 'POST'
    })
}

export function getChatBroadcast(
    params: any,
    headers?: any,
    config?: Partial<RequestOptions>
) {
    return $request.post(
        { url: '/broadcast/generate', params, headers },
        config
    )
}
