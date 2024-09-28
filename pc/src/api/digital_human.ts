export function getAvatarList() {
  return $request.get({
    url: '/digital.material/avatarList'
  })
}

export function getMusicCategory(params?: any) {
  return $request.get({
    url: '/digital.material/musicList',
    params
  })
}

export function getMusicList(params?: any) {
  return $request.get({
    url: '/digital.material/musicCategory',
    params
  })
}

export function getBgCategory(params?: any) {
  return $request.get({
    url: '/digital.material/backgroundCategory',
    params
  })
}

export function getBgList(params?: any) {
  return $request.get({
    url: '/digital.material/backgroundList',
    params
  })
}

export function getMapsList(params?: any) {
  return $request.get({
    url: '/digital.material/decalsList',
    params
  })
}

export function getProspectsList(params?: any) {
  return $request.get({
    url: '/digital.material/prepositionList',
    params
  })
}
export function getDubList(params?: any) {
  return $request.get({
    url: '/digital.material/auditList',
    params
  })
}

export function getEffectList(params?: any) {
  return $request.get({
    url: '/digital.material/effectList',
    params
  })
}

export function getFontsList(params?: any) {
  return $request.get({
    url: '/digital.material/fontsList',
    params
  })
}

export function getVideoRecordsList(params?: any) {
  return $request.get({
    url: '/video_records/lists',
    params
  })
}

export function delVideoRecord(params?: any) {
  return $request.post({
    url: '/video_records/del',
    params
  })
}

export function putVideoRename(params?: any) {
  return $request.post({
    url: '/video_records/rename',
    params
  })
}

export function putAvatarSava(params?: any) {
  return $request.post({
    url: '/digital.material/save',
    params
  })
}

export function checkVideoStatus(params?: any) {
  return $request.get({
    url: '/video_records/checkStatus',
    params
  })
}

export function getAvatarDetail(params?: any) {
  return $request.get({
    url: '/digital.material/detail',
    params
  })
}
