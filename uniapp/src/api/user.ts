import request from '@/utils/request'

export function getUserCenter(header?: any) {
    return request.get({ url: '/user/center', header })
}

// 个人信息
export function getUserInfo() {
    return request.get({ url: '/user/info' }, { isAuth: true })
}

// 个人编辑
export function userEdit(data: any) {
    return request.post({ url: '/user/setInfo', data }, { isAuth: true })
}

// 绑定手机
export function userBindMobile(data: any, header?: any) {
    return request.post(
        { url: '/user/bindMobile', data, header },
        { isAuth: true }
    )
}

// 微信电话
export function userMnpMobile(data: any, header?: any) {
    return request.post(
        { url: '/user/getMobileByMnp', data, header },
        { isAuth: true }
    )
}

// 更改手机号
export function userChangePwd(data: any) {
    return request.post({ url: '/user/changePassword', data }, { isAuth: true })
}

//忘记密码
export function forgotPassword(data: Record<string, any>) {
    return request.post({ url: '/user/resetPassword', data })
}

//余额明细
export function accountLog(data: any) {
    return request.get({ url: '/account/lists', data })
}

//明细详情
export function accountDetail(data: any) {
    return request.get({ url: '/account/detail', data })
}

export function feedbackPost(data: any) {
    return request.post({ url: '/feedback/add', data })
}
//注销账号
export function cancelled(data?: any) {
    return request.post({ url: '/user/cancelled', data })
}
// 小程序绑定微信
export const apiBindwx = (params: any, header?: any) =>
    request.post(
        { url: '/login/mnpAuthBind', data: params, header },
        { isAuth: true }
    )

// 公众号绑定
export function OaAuthBind(data: Record<string, any>) {
    return request.post({ url: '/login/oaAuthBind', data })
}

// 绑定邀请
export function bindInvite(data: any, token: string) {
    return request.post({ url: '/share/invite', data, header: { token } })
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
 * @param data
 */
export function checkRedeemCode(data: {
    sn: number | string
}): Promise<RedeemCodeResponse> {
    return request.get({ url: '/cardCode/checkCard', data: data })
}

/**
 * @description 卡密兑换
 * @param data
 */
export function useRedeemCode(data: { sn: number | string }) {
    return request.post({ url: '/cardCode/useCard', data: data })
}
