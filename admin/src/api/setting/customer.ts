import request from '@/utils/request'

//获取客服设置
export function getCustomerConfig(params?: any) {
    return request.get({ url: '/setting.customer/detail', params })
}

//保存客服设置
export function setCustomerConfig(params?: any) {
    return request.post({ url: '/setting.customer/save', params })
}
