import { ContentTypeEnum } from '@/enums/requestEnums'
import request, {
    RequestConfig,
    RequestEventStreamConfig
} from '@/utils/request'

export function getSkillLists(data: any) {
    return request.get({ url: '/skill/lists', data })
}

export function getCreationLists(data: any) {
    return request.get({ url: '/creation/lists', data })
}

export function getSamplesLists() {
    return request.get({ url: '/chat.chatSample/samplesLists' })
}

export function questionChat(data: any) {
    return request.post({ url: '/chat_records/chat', data }, { isAuth: true })
}

// 对话记录
export function getChatRecord(data: any) {
    return request.get(
        {
            url: '/chat.chatRecord/chatRecord',
            data
        },
        {
            ignoreCancel: true
        }
    )
}

//清空会话
export function cleanChatRecord(data: any) {
    return request.post(
        {
            url: '/chat.chatRecord/chatClean',
            data
        },
        { isAuth: true }
    )
}

//删除会话
export function deleteChatRecord(data: { id: number | string; content?: string }) {
    return request.post({
        url: '/chat.chatRecord/del',
        data
    })
}

export function collectChatRecord(data: any) {
    return request.post(
        { url: '/chat_records/collect', data },
        { isAuth: true }
    )
}

export function cancelCollectChatRecord(data: any) {
    return request.post(
        { url: '/chat_records/cancelCollect', data },
        { isAuth: true }
    )
}

export function getCollectChatRecordLists(data: any) {
    return request.get({ url: '/chat_records/collectLists', data })
}

export function getCreationDetail(data: any) {
    return request.get({ url: '/creation/detail', data })
}

//创作收藏
export function creationCollection(data: any) {
    return request.post({ url: '/creation/collect', data })
}

export function chatSendText(data: any, config: RequestEventStreamConfig) {
    return request.eventStream(
        { url: '/chat.chatDialog/completions', data, method: 'POST' },
        config
    )
}

export function getChatCategoryLists(data: any) {
    return request.get({ url: '/chat.chatCategory/lists', data }, { ignoreCancel: true })
}

export function chatCategoryAdd(data: any) {
    return request.post(
        { url: '/chat.chatCategory/add', data },
        { isAuth: true }
    )
}

export function chatCategoryEdit(data: any) {
    return request.post(
        { url: '/chat.chatCategory/edit', data },
        { isAuth: true }
    )
}

export function chatCategoryDelete(data: any) {
    return request.post(
        { url: '/chat.chatCategory/del', data },
        { isAuth: true }
    )
}

export function chatCategoryClear() {
    return request.post({ url: '/chat.chatCategory/clear' }, { isAuth: true })
}

export function creationChat(data: any, config: RequestEventStreamConfig) {
    return request.eventStream(
        { url: '/chat/creationChat', data, method: 'POST' },
        config
    )
}

export function getChatBroadcast(
    data: any,
    header?: any,
    config?: Partial<RequestConfig>
) {
    return request.post({ url: '/broadcast/generate', data, header }, config)
}

export function audioTransfer(filePath: string, formData: Record<string, any>) {
    return request.uploadFile({
        url: '/chat/voiceTransfer',
        filePath,
        formData,
        name: 'file'
    })
}
