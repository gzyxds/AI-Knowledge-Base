import request from '@/utils/request'

//音乐列表
export function getVideoRecord(params?: any) {
    return request.get({ url: '/video.videoRecord/lists', params })
}

//下拉选项
export function getVideoRecordOptions(params?: any) {
    return request.get({ url: '/video.videoRecord/options', params })
}

//音乐删除
export function deleteVideoRecord(params?: any) {
    return request.post({ url: '/video.videoRecord/del', params })
}

//AI配置详情
export function getVideoConfig(params?: any) {
    return request.get({ url: '/video.videoConfig/detail', params })
}

//AI配置保存
export function putVideoConfig(params?: any) {
    return request.post({ url: '/video.videoConfig/save', params })
}

//风格列表
export function getVideoStyle(params?: any) {
    return request.get({ url: '/video.videoStyle/lists', params })
}

//风格详情
export function getVideoStyleDetail(params?: any) {
    return request.get({ url: '/video.videoStyle/detail', params })
}

//风格新增
export function postVideoStyle(params?: any) {
    return request.post({ url: '/video.videoStyle/add', params })
}

//风格编辑
export function putVideoStyle(params?: any) {
    return request.post({ url: '/video.videoStyle/edit', params })
}

//风格状态
export function putVideoStyleStatus(params?: any) {
    return request.post({ url: '/video.videoStyle/status', params })
}

//风格删除
export function deleteVideoStyle(params?: any) {
    return request.post({ url: '/video.videoStyle/del', params })
}
