import request from '@/utils/request'

// key列表
export function getKeyDownRuleLists(params?: any) {
    return request.get(
        { url: '/setting.KeyRule/lists', params },
        {
            ignoreCancelToken: true
        }
    )
}
export function getKeyDownRuleDetail(params?: any) {
    return request.get({ url: '/setting.KeyRule/detail', params })
}

// ai模型
export function getKeyDownRuleAiModel(params?: any) {
    return request.get({ url: '/setting.KeyRule/getAimodel', params })
}

// 新增
export function addKeyDownRule(data?: any) {
    return request.post({ url: '/setting.KeyRule/add', data })
}

// 编辑
export function editKeyDownRule(data?: any) {
    return request.post({ url: '/setting.KeyRule/edit', data })
}

// 删除
export function delKeyDownRule(data?: any) {
    return request.post({ url: '/setting.KeyRule/del', data })
}

// 修改状态
export function statusKeyDownRule(data?: any) {
    return request.post({ url: '/setting.KeyRule/status', data })
}

export function setKeyPoolConfig(data: any) {
    return request.post({ url: '/setting.KeyRule/setConfig', data })
}

export function getKeyPoolConfig() {
    return request.get({ url: '/setting.KeyRule/getConfig' })
}
