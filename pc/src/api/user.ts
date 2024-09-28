export function getUserCenter(headers?: any) {
  return $request.get({ url: '/user/center', headers })
}

// 个人信息
export function getUserInfo() {
  return $request.get({ url: '/user/info' })
}

// 个人编辑
export function userEdit(params: any) {
  return $request.post({ url: '/user/setInfo', params })
}

// 绑定手机
export function userBindMobile(params: any, headers?: any) {
  return $request.post(
    { url: '/user/bindMobile', params, headers },
    { withToken: !headers?.token }
  )
}

// 微信电话
export function userMnpMobile(params: any) {
  return $request.post({ url: '/user/getMobileByMnp', params })
}

// 更改密码
export function userChangePwd(params: any) {
  return $request.post({ url: '/user/changePassword', params })
}

//忘记密码
export function forgotPassword(params: Record<string, any>) {
  return $request.post({ url: '/user/resetPassword', params })
}


// 分享/邀请新用户
export function shareInvite(params: Record<string, any>) {
  return $request.post({ url: '/share/invite', params })
}

// 点击分享链接
export function shareClick(params: Record<string, any>) {
  return $request.post({ url: '/share/click', params })
}




export type RedeemCodeResponse = {
  content: string         // 卡密内容
  failure_time: string    // 失效时间
  id: string | number     // 卡密ID
  sn: string | number     // 卡密编号
  type: string            // 卡密类型
  type_desc: string       // 卡密类型说明
  valid_time: string      // 卡密有效期
}

/**
 * @description 卡密查询
 * @return { RedeemCodeResponse }
 * @param params
 */
export function checkRedeemCode(params: {
  sn: number | string
}): Promise<RedeemCodeResponse> {
  return $request.get({ url: '/cardCode/checkCard', params: params })
}

/**
 * @description 卡密兑换
 * @param params
 */
export function useRedeemCode(params: { sn: number | string }) {
  return $request.post({ url: '/cardCode/useCard', params: params })
}
