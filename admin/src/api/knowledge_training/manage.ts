import request from '@/utils/request'

// 知识库列表
export function knowKnowledgeList(params?: any) {
    return request.get({ url: '/kb.know/lists', params })
}

// 知识库详情
export function knowKnowledgeDetail(params?: any) {
    return request.get({ url: '/kb.know/detail', params })
}

//知识库删除
export function knowKnowledgeDel(params?: any) {
    return request.post({ url: '/kb.know/del', params })
}

//知识库状态修改
export function knowKnowledgeStatus(params?: any) {
    return request.post({ url: '/kb.know/changeStatus', params })
}

//文件列表
export function fileList(params?: any) {
    return request.get({ url: '/kb.know/files', params })
}

//文件数据
export function fileDataList(params?: any) {
    return request.get({ url: '/kb.know/fileDatas', params })
}

//文件删除
export function fileDel(params?: any) {
    return request.post({ url: '/kb.know/fileRemove', params })
}

export function getTeachLists(params?: any) {
    return request.get({ url: '/kb.teach/lists', params })
}

export function delFileData(params?: any) {
    return request.post({ url: '/kb.teach/del', params })
}

export function transferFileData(params?: any) {
    return request.post({ url: '/kb.know/transfer', params })
}
