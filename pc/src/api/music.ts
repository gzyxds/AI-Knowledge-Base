// 音乐风格
export function getMusicConfig() {
  return $request.get({ url: '/music/config' })
}

// 音乐列表
export function getMusicLists(params: any) {
  return $request.get({ url: '/music/lists', params })
}

// 歌词联想
export function postMusicImagine(params: any) {
  return $request.post({ url: '/music/imagine', params })
}

// 歌词联想
export function postMusicGenerate(params: any) {
  return $request.post({ url: '/music/generate', params })
}

// 音乐详情
export function getMusicDetail(params: { id: number }) {
  return $request.get({ url: '/music/detail', params })
}

// 音乐推荐列表
export function getMusicRecommendList() {
  return $request.get({ url: '/musicSquare/recommendLits' })
}

// 删除音乐
export function deleteMusic(params: any) {
  return $request.post({ url: '/music/del', params })
}
