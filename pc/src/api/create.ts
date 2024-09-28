export function getCreationLists(params?: any) {
  return $request.get({ url: '/chat.creation/lists', params })
}
export function getCategoryList(params?: any) {
  return $request.get({ url: '/chat.creation/category', params })
}

export function getCreantionList(params?: any) {
  return $request.get({ url: '/chat.creation/lists', params })
}

export function getCreationDetail(params: any) {
  return $request.get({ url: '/chat.creation/detail', params })
}

//收藏列表
export function collection(params: any) {
  return $request.post({ url: '/chat.creation/collect', params })
}
