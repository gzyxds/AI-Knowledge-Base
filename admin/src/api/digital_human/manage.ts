import request from '@/utils/request'

// 获取形象配置
export function getAvatarConfig(params?: any) {
    return request.get({ url: '/setting.digital/detail', params })
}

// 设置形象配置
export function setAvatarConfig(params?: any) {
    return request.post({ url: '/setting.digital/save', params })
}

//获取合成记录
export function getRecordList(params?: any) {
    return request.get({ url: '/digital.videoRecords/lists', params })
}

//删除合成记录
export function delRecord(params?: any) {
    return request.post({ url: '/digital.videoRecords/del', params })
}
