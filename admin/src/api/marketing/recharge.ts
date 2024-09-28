import request from '@/utils/request'
//获取充值套餐配置
export function getRechargeConfig() {
    return request.get({ url: '/recharge.package/getConfig' })
}
//设置充值套餐配置
export function setRechargeConfig(params: any) {
    return request.post({ url: '/recharge.package/setConfig', params })
}

// 充值套餐列表
export function getRechargeLists(params: any) {
    return request.get({ url: '/recharge.package/lists', params })
}

// 添加充值套餐
export function rechargeAdd(params: any) {
    return request.post({ url: '/recharge.package/add', params })
}
// 编辑充值套餐
export function rechargeEdit(params: any) {
    return request.post({ url: '/recharge.package/edit', params })
}

// 充值套餐排序
export function rechargeSort(params: any) {
    return request.post({ url: '/recharge.package/sort', params })
}

// 删除充值套餐
export function rechargeDelete(params: any) {
    return request.post({ url: '/recharge.package/del', params })
}

// 充值套餐详情
export function getRechargeDetail(params: any) {
    return request.get({ url: '/recharge.package/detail', params })
}

// 修改套餐状态
export function rechargeStatus(params: any) {
    return request.post({ url: '/recharge.package/status', params })
}
