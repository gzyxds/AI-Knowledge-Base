import request from '@/utils/request'

export function getKBList(data: any) {
    return request.get(
        { url: '/kb.know/lists', data },
        { isAuth: true, ignoreCancel: true }
    )
}

export function addKB(data: any) {
    return request.post({ url: '/kb.know/add', data }, { isAuth: true })
}

export function editKB(data: any) {
    return request.post({ url: '/kb.know/edit', data }, { isAuth: true })
}

export function delKB(data: any) {
    return request.post({ url: '/kb.know/del', data }, { isAuth: true })
}

export function getKBDetail(data: any) {
    return request.get({ url: '/kb.know/detail', data }, { isAuth: true })
}

export function fileImport(data: any) {
    return request.post({ url: '/kb.teach/import', data }, { isAuth: true })
}

export function fileDel(data: any) {
    return request.post({ url: '/kb.know/fileRemove', data }, { isAuth: true })
}

export function fileDataList(data: any) {
    return request.get({ url: '/kb.know/files', data }, { isAuth: true })
}

export function itemFileDataList(data: any) {
    return request.get({ url: '/kb.teach/datas', data }, { isAuth: true })
}

export function itemDataDetail(data: any) {
    return request.get({ url: '/kb.teach/detail', data }, { isAuth: true })
}
export function fileDataDel(data: any) {
    return request.post({ url: '/kb.teach/delete', data }, { isAuth: true })
}

//文件重命名
export function fileRename(data?: any) {
    return request.post({ url: '/kb.know/fileRename', data }, { isAuth: true })
}

export function dataImport(data: any) {
    return request.post({ url: '/kb.teach/insert', data }, { isAuth: true })
}

export function dataUpdate(data: any) {
    return request.post({ url: '/kb.teach/update', data }, { isAuth: true })
}

export function dataTest(data: any) {
    return request.post({ url: '/kb.teach/tests', data }, { isAuth: true })
}

export function getSharedUsers(data: any) {
    return request.get({ url: '/kb.know/sharedUsers', data })
}

export function getSharedList(data: any) {
    return request.get({ url: '/kb.know/sharedList', data })
}

export function sharedAdd(data: any) {
    return request.post({ url: '/kb.know/sharedAdd', data }, { isAuth: true })
}

export function sharedDel(data: any) {
    return request.post({ url: '/kb.know/sharedDel', data }, { isAuth: true })
}

export function kbTransfer(data: any) {
    return request.post({ url: '/kb.know/transfer', data })
}

export function webHtmlCapture(data: any) {
    return request.post({ url: '/kb.teach/capture', data })
}


// 团队用户筛选
export function getTeamUser(data: any) {
    return request.get({ url: '/kb.know/teamUsers', data })
}

// 团队成员列表
export function getTeamLists(data: any) {
    return request.get({ url: '/kb.know/teamLists', data })
}

// 团队成员新增
export function postTeam(data: any) {
    return request.post({ url: '/kb.know/teamAdd', data })
}

// 团队成员编辑
export function putTeam(data: any) {
    return request.post({ url: '/kb.know/teamEdit', data })
}

// 团队成员删除
export function deleteTeam(data: any) {
    return request.post({ url: '/kb.know/teamDel', data })
}

// 转移知识库
export function transferTeam(data: any) {
    return request.post({ url: '/kb.know/transfer', data })
}