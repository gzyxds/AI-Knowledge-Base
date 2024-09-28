import request from '@/utils/request'

// AI模型配置详情
export function getAiModel() {
    return request.get({ url: '/setting.ai.models/lists' })
}

//模型详情
export function getAiModelDetail(params: any) {
    return request.get({ url: '/setting.ai.models/detail', params })
}

//模型通道
export function getAiModelChannels(params?: any) {
    return request.get({ url: '/setting.ai.models/channels', params })
}

//模型创建
export function postModel(params?: any) {
    return request.post({ url: '/setting.ai.models/add', params })
}

//模型编辑
export function putModel(params?: any) {
    return request.post({ url: '/setting.ai.models/edit', params })
}

//模型删除
export function deleteModel(params?: any) {
    return request.post({ url: '/setting.ai.models/del', params })
}

// AI模型配置保存
export function setAiModel(params: any) {
    return request.post({ url: '/setting.ai.models/save', params })
}

// AI模型配置保存
export function putAiModelSort(params: any) {
    return request.post({ url: '/setting.ai.models/sort', params })
}

// 模型列表
export function getAiModelAll() {
    return request.get({ url: '/setting.ai.cost/models' })
}

// 计费模型
export function getModelBilling(params: any) {
    return request.get({ url: '/setting.ai.models/lists', params })
}

// 计费模型保存
export function setModelBilling(params: any) {
    return request.post({ url: '/setting.ai.cost/save', params })
}

export function checkModelCanDel(params: any) {
    return request.post({ url: '/setting.ai.cost/checkDel', params })
}
