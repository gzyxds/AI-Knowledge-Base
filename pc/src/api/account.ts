import { terminal } from '@/config'

// 登录
export function login(params: any) {
  return $request.post({
    url: '/login/account',
    params: { ...params, terminal }
  })
}

//游客登录
export function visitorLogin() {
  return $request.post({
    url: '/login/tourist',
    params: {
      terminal
    }
  })
}
export function bindAccount(params: any) {
  return $request.post({
    url: '/login/bindAccount',
    params
  })
}

// 登出
export function logout() {
  return $request.post({ url: '/login/logout' })
}

//注册
export function register(params: any) {
  return $request.post({
    url: '/login/register',
    params: { ...params, channel: terminal }
  })
}

//向微信请求code的链接
export function getWxCode(params?: any) {
  return $request.get({
    url: '/login/qrcode',
    params
  })
}

export function checkTicket(params?: any) {
  return $request.post({
    url: '/login/ticket',
    params
  })
}

export function wxLogin(params: any) {
  return $request.post({ url: '/login/scanLogin', params })
}

//发送短信
export function sendEmailCode(params: any) {
  return $request.post({ url: '/email/sendCode', params })
}

//余额明细列表
export function accountList(params: any) {
    return $request.get({ url: '/account/lists', params })
}

//余额详情
export function accountDetail(params: any) {
    return $request.get({ url: '/account/detail', params })
}

//修改用户信息
export function editUserInfo(params: any) {
    return $request.post({ url: '/user/setInfo', params })
}

//向微信请求code的链接
export function getWxCodeUrl() {
    return $request.get({
        url: '/login/codeUrl',
        params: {
            url: location.href
        }
    })
}


export function wxJsConfig(params: any) {
    return $request.get({ url: '/wechat/jsConfig', params })
}

export function cancelled(params?: any) {
    return $request.get({ url: '/user/cancelled', params })
}

//获取图形验证码
export function captcha() {
    return $request.get({ url: '/login/captcha' })
}