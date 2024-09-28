import request from '@/utils/request'

//充值
export function recharge(data: any) {
    return request.post({ url: '/recharge/place', data }, { isAuth: true })
}

//充值记录
export function rechargeRecord(data: any) {
    return request.get({ url: '/recharge/record', data }, { isAuth: true })
}

// 充值配置
export function getRechargeConfig(data: any) {
    return request.get({ url: '/recharge/lists', data }, { isAuth: true })
}
