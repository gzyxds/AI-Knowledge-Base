//获取列表数据
export function getSplitList(params: any) {
  return $request.get({ url: '/qa/lists', params })
}

//导入文件
export function addFile(params: any) {
  return $request.post({ url: '/qa/add', params })
}

//删除文件
export function delFile(params: any) {
  return $request.post({ url: '/qa/del', params })
}

//获取拆分数据列表
export function getResultList(params: any) {
  return $request.get({ url: '/qa/results', params })
}

//删除拆分结果
export function delResult(params: any) {
  return $request.post({ url: '/qa/delete', params })
}

//修正结果
export function adjustResult(params: any) {
  return $request.post({ url: '/qa/adjust', params })
}

//修正结果
export function exportQa(params: any) {
  return $request.post({ url: '/qa/export', params })
}
