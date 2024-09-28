/**
 * @description 配音角色
 */
export function getDubbingList() {
  return $request.get({
    url: '/kb.digital/dubbing'
  })
}

/**
 * @description 形象列表
 */
export function getDigitalList(params: any) {
  return $request.get({
    url: '/kb.digital/lists',
    params
  })
}

/**
 * @description 形象详情
 */
export function getDigitalDetail(params: any) {
  return $request.get({
    url: '/kb.digital/detail',
    params
  })
}

/**
 * @description 形象添加
 */
export function postDigital(params: any) {
  return $request.post({
    url: '/kb.digital/add',
    params
  })
}

/**
 * @description 形象编辑
 */
export function putDigital(params: any) {
  return $request.post({
    url: '/kb.digital/edit',
    params
  })
}

/**
 * @description 形象删除
 */
export function delDigital(params: any) {
  return $request.post({
    url: '/kb.digital/del',
    params
  })
}
