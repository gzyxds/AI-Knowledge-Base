import request, { RequestEventStreamConfig } from '@/utils/request'

export function getRobotLists(data?: any) {
    return request.get({ url: '/kb.robot/lists', data })
}

export function getRobotDetail(data?: any) {
    return request.get({ url: '/kb.robot/detail', data })
}

export function postRobot(data?: any) {
    return request.post({ url: '/kb.robot/add', data })
}

export function putRobot(data: any) {
    return request.post({ url: '/kb.robot/edit', data })
}

export function delRobot(data: any) {
    return request.post({ url: '/kb.robot/del', data })
}

export function cancelShare(data: any) {
    return request.post({ url: '/kb.robot/cancelShare', data })
}

export function getRobotDataRecord(data: any) {
    return request.get({ url: '/kb.chat/dataRecord', data })
}

export function getRobotChatData(data: any) {
    return request.get({ url: '/kb.chat/dataCount', data })
}

export function delRobotChatRecord(data: any) {
    return request.post({ url: '/kb.chat/dataDelete', data })
}

export function getReleaseList(data: any) {
    return request.get({ url: '/kb.share/lists', data })
}

export function putRelease(data: any) {
    return request.post({ url: '/kb.share/add', data })
}

export function getReleaseDetail(data: any) {
    return request.get({ url: '/kb.share/detail', data })
}

// 分享删除
export function delRelease(data: any) {
    return request.post({ url: '/kb.share/del', data })
}

// 用量设置
export function postRelease(data: any) {
    return request.post({ url: '/kb.share/edit', data })
}

export function postReleaseInfo(data: any) {
    return request.post({ url: '/kb.share/update', data })
}

export function getRobotCateLists(data: any) {
    return request.get({ url: '/kb.chat/cateLists', data })
}

export function postRobotCate(data: any) {
    return request.post({ url: '/kb.chat/cateAdd', data })
}

export function putRobotCate(data: any) {
    return request.post({ url: '/kb.chat/cateEdit', data })
}

export function delRobotCate(data: any) {
    return request.post({ url: '/kb.chat/cateDel', data })
}

export function clearRobotCate(data: any) {
    return request.post({ url: '/kb.chat/cateClear', data })
}

export function chatFeedBack(data: any) {
    return request.post({ url: '/kb.chat/feedback', data })
}

export function robotChat(
    data: any,
    config: RequestEventStreamConfig,
    header?: any
) {
    return request.eventStream(
        {
            url: '/v1/chat/completions',
            method: 'POST',
            data,
            header
        },
        config
    )
}

export function clearRobotChatRecord(data: any, header?: any) {
    return request.post({ url: '/kb.chat/chatClean', data, header })
}

export function getRobotChatRecord(data: any, header?: any) {
    return request.get({ url: '/kb.chat/chatRecord', data, header })
}

export function robotRecordsCorrect(data: any) {
    return request.post({ url: '/kb.chat/chatCorrect', data })
}

export function getRobotCategory(data?: any) {
    return request.get({ url: '/kb.square/category', data })
}

export function getRobotSquare(data: any) {
    return request.get({ url: '/kb.square/lists', data })
}

export function getRobotRecord(data?: any) {
    return request.get({ url: '/kb.square/record', data })
}

export function putRobotRecord(data: any) {
    return request.post({ url: '/kb.square/add', data })
}

export function delRobotRecord(data: any) {
    return request.post({ url: '/kb.square/del', data })
}

export function voiceGenerate(data: any, header?: any) {
    return request.post({ url: '/voice/generate', data, header })
}

export function voiceTransfer(file: string) {
    return request.uploadFile({
        url: '/voice/transfer',
        filePath: file,
        name: 'file'
    })
}

export function putReleaseSetBg(data: any) {
    return request.post({ url: '/kb.share/setBg', data })
}
