import { FileParams } from 'ofetch'

export function getRobotLists(params?: any) {
    return $request.get({ url: '/kb.robot/lists', params })
}

export function getRobotDetail(params?: any) {
    return $request.get({ url: '/kb.robot/detail', params })
}

export function postRobot(params?: any) {
    return $request.post({ url: '/kb.robot/add', params })
}

export function putRobot(params: any) {
    return $request.post({ url: '/kb.robot/edit', params })
}

export function delRobot(params: any) {
    return $request.post({ url: '/kb.robot/del', params })
}

export function cancelShare(params: any) {
    return $request.post({ url: '/kb.robot/cancelShare', params })
}

export function getRobotDataRecord(params: any) {
    return $request.get({ url: '/kb.chat/dataRecord', params })
}

export function getRobotChatData(params: any) {
    return $request.get({ url: '/kb.chat/dataCount', params })
}

export function delRobotChatRecord(params: any) {
    return $request.post({ url: '/kb.chat/dataDelete', params })
}

export function getReleaseList(params: any) {
    return $request.get({ url: '/kb.share/lists', params })
}

export function putRelease(params: any) {
    return $request.post({ url: '/kb.share/add', params })
}

export function putReleaseSetBg(params: any) {
    return $request.post({ url: '/kb.share/setBg', params })
}

export function postReleaseInfo(params: any) {
    return $request.post({ url: '/kb.share/update', params })
}

export function getReleaseDetail(params: any) {
    return $request.get({ url: '/kb.share/detail', params })
}

// 分享删除
export function delRelease(params: any) {
    return $request.post({ url: '/kb.share/del', params })
}

// 用量设置
export function postRelease(params: any) {
    return $request.post({ url: '/kb.share/edit', params })
}

export function getRobotCateLists(params: any) {
    return $request.get({ url: '/kb.chat/cateLists', params })
}

export function postRobotCate(params: any) {
    return $request.post({ url: '/kb.chat/cateAdd', params })
}

export function putRobotCate(params: any) {
    return $request.post({ url: '/kb.chat/cateEdit', params })
}

export function delRobotCate(params: any) {
    return $request.post({ url: '/kb.chat/cateDel', params })
}

export function clearRobotCate(params: any) {
    return $request.post({ url: '/kb.chat/cateClear', params })
}

export function robotChat(params: any, headers?: any) {
    return $request.sse({
        url: '/v1/chat/completions',
        method: 'POST',
        params,
        headers
    })
}

export function clearRobotChatRecord(params: any, headers?: any) {
    return $request.post({ url: '/kb.chat/chatClean', params, headers })
}

export function getRobotChatRecord(params: any, headers?: any) {
    return $request.get({ url: '/kb.chat/chatRecord', params, headers })
}

export function robotRecordsCorrect(params: any) {
    return $request.post({ url: '/kb.chat/chatCorrect', params })
}

export function chatFeedBack(params: any, headers?: any) {
    return $request.post({ url: '/kb.chat/feedback', params, headers })
}

export function getRobotCategory(params?: any) {
    return $request.get({ url: '/kb.square/category', params })
}

export function getRobotSquare(params: any) {
    return $request.get({ url: '/kb.square/lists', params })
}

export function getRobotRecord(params?: any) {
    return $request.get({ url: '/kb.square/record', params })
}

export function putRobotRecord(params: any) {
    return $request.post({ url: '/kb.square/add', params })
}

export function delRobotRecord(params: any) {
    return $request.post({ url: '/kb.square/del', params })
}

export function voiceGenerate(params: any, headers?: any) {
    return $request.post({ url: '/voice/generate', params, headers })
}

export function voiceTransfer(params: FileParams) {
    return $request.uploadFile({ url: '/voice/transfer' }, params)
}
