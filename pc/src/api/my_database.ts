// 知识库列表
export function knowKnowledgeList(params?: any) {
  return $request.get({ url: '/kb.know/lists', params })
}
// 知识库列表
export function knowKnowledgeAllList(params?: any) {
  return $request.get({ url: '/kb.know/all', params })
}

// 知识库详情
export function knowKnowledgeDetail(params?: any) {
  return $request.get({ url: '/kb.know/detail', params })
}

//知识库新增
export function knowKnowledgeAdd(params?: any) {
  return $request.post({ url: '/kb.know/add', params })
}

//知识库编辑
export function knowKnowledgeEdit(params?: any) {
  return $request.post({ url: '/kb.know/edit', params })
}

//知识库删除
export function knowKnowledgeDel(params?: any) {
  return $request.post({ url: '/kb.know/del', params })
}

//数据列表
export function fileDataList(params?: any) {
  return $request.get({ url: '/kb.know/files', params })
}
//数据导入
export function fileDataimport(params?: any) {
  return $request.post({ url: '/kb.teach/import', params })
}

//单个文件数据列表
export function itemFileDataList(params?: any) {
  return $request.get({ url: '/kb.teach/datas', params })
}

//文件重命名
export function fileRename(params?: any) {
  return $request.post({ url: '/kb.know/fileRename', params })
}

//文件删除
export function fileDelete(params?: any) {
  return $request.post({ url: '/kb.know/fileRemove', params })
}

//拆分重试
export function fileRetry(params?: any) {
  return $request.post({ url: '/kb.teach/qaRetry', params })
}

//数据测试
export function itemDatatest(params?: any) {
  return $request.post({ url: '/kb.teach/tests', params })
}

//数据录入
export function itemDataImport(params?: any) {
  return $request.post({ url: '/kb.teach/insert', params })
}

//数据修正
export function itemDataEdit(params?: any) {
  return $request.post({ url: '/kb.teach/update', params })
}

//数据重试
export function itemDataRetry(params?: any) {
  return $request.post({ url: '/kb.teach/reset', params })
}

//文件数据删除
export function itemDataDel(params?: any) {
  return $request.post({ url: '/kb.teach/delete', params })
}

//数据详情
export function itemDataDetail(params?: any) {
  return $request.get({ url: '/kb.teach/detail', params })
}

export function webHtmlCapture(params: any) {
  return $request.post({ url: '/kb.teach/capture', params })
}

//计费模型
export function getmodels(params?: any) {
  return $request.get({ url: '/index/models', params })
}

//数据检测
export function checkData(params?: any) {
  return $request.post({ url: '/kb.teach/detection', params })
}
