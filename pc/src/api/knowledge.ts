export function getKnowCategoryLists(params: any) {
  return $request.get({ url: '/knowCategory/lists', params })
}

export function getKnowCategoryDetail(params: any) {
  return $request.get({ url: '/KnowApply/detail', params })
}

export function knowCategoryAdd(params: any) {
  return $request.post({ url: '/knowCategory/add', params })
}

export function knowCategoryEdit(params: any) {
  return $request.post({ url: '/knowCategory/edit', params })
}

export function knowCategoryDelete(params: any) {
  return $request.post({ url: '/knowCategory/del', params })
}

export function knowCategoryClear(params: any) {
  return $request.post({ url: '/knowCategory/clear', params })
}

export function postApp(params: any) {
  return $request.post({ url: '/KnowApply/add', params })
}

export function putApp(params: any) {
  return $request.post({ url: '/KnowApply/edit', params })
}

export function delApp(params: any) {
  return $request.post({ url: '/KnowApply/del', params })
}
export function collectApp(params: any) {
  return $request.post({ url: '/KnowApply/collect', params })
}

export function knowDialogueChat(params: any) {
  return $request.sse({ url: '/knowDialogue/chat', method: 'POST', params })
}
export function getAppChatRecord(params: any) {
  return $request.get({ url: '/KnowApply/chatRecord', params })
}

export function getKnowRecordsLists(params: any) {
  return $request.get({ url: '/KnowRecords/lists', params })
}

export function knowRecordsClean(params: any) {
  return $request.post({ url: '/KnowRecords/clean', params })
}

export function getChatChannelsRecord(params: any, headers: any) {
  return $request.get(
    { url: '/KnowDialogue/shareChatRecord', params, headers },
    { withToken: false }
  )
}

export function chatChannelsRecordClean(params: any, headers: any) {
  return $request.post(
    { url: '/KnowDialogue/shareChatClean', params, headers },
    { withToken: false }
  )
}

export function chatChannelsInfo(params: any) {
  return $request.get(
    { url: '/KnowDialogue/shareDetail', params },
    { withToken: false }
  )
}

export function chatChannelsAuth(params: any) {
  return $request.post(
    { url: '/KnowDialogue/shareAuth', params },
    { withToken: false }
  )
}

export function chatChannelsSend(params: any, headers: any) {
  return $request.sse({
    url: '/KnowDialogue/shareChat',
    params,
    method: 'POST',
    headers
  })
}

//发布网页分享
export function applyReleaseAddWeb(params: any) {
  return $request.post({ url: '/knowRelease/addWeb', params })
}

//发布api
export function applyReleaseAddApi(params: any) {
  return $request.post({ url: '/knowRelease/addApi', params })
}

export function getApplyReleaseLists(params: any) {
  return $request.get({ url: '/knowRelease/lists', params })
}
// 分享删除
export function applyReleaseDelete(params: any) {
  return $request.post({ url: '/knowRelease/delete', params })
}

// 用量设置
export function applyReleaseSetDosage(params: any) {
  return $request.post({ url: '/knowRelease/setDosage', params })
}

//重置密钥
export function applyReleaseResetSecret(params: any) {
  return $request.post({ url: '/knowRelease/resetSecret', params })
}

//问答记录删除
export function knowRecordsDelete(params: any) {
  return $request.post({ url: '/knowApply/chatRecordDelete', params })
}
//问答记录修正

export function getSharedUsers(params: any) {
  return $request.get({ url: '/kb.know/sharedUsers', params })
}

export function getSharedList(params: any) {
  return $request.get({ url: '/kb.know/sharedList', params })
}

export function sharedAdd(params: any) {
  return $request.post({ url: '/kb.know/sharedAdd', params })
}

export function sharedDel(params: any) {
  return $request.post({ url: '/kb.know/sharedDel', params })
}

export function kbTransfer(params: any) {
  return $request.post({ url: '/kb.know/transfer', params })
}

// 团队用户筛选
export function getTeamUser(params: any) {
  return $request.get({ url: '/kb.know/teamUsers', params })
}

// 团队成员列表
export function getTeamLists(params: any) {
  return $request.get({ url: '/kb.know/teamLists', params })
}

// 团队成员新增
export function postTeam(params: any) {
  return $request.post({ url: '/kb.know/teamAdd', params })
}

// 团队成员编辑
export function putTeam(params: any) {
  return $request.post({ url: '/kb.know/teamEdit', params })
}

// 团队成员删除
export function deleteTeam(params: any) {
  return $request.post({ url: '/kb.know/teamDel', params })
}

// 转移知识库
export function transferTeam(params: any) {
  return $request.post({ url: '/kb.know/transfer', params })
}