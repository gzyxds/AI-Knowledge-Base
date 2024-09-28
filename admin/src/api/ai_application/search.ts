import request from '@/utils/request'

export function getSearchRecord(params?: any) {
    return request.get({ url: '/search.record/lists', params })
}

export function delSearchRecord(params?: any) {
    return request.post({ url: '/search.record/del', params })
}

export function getSearchConfig() {
    return request.get({ url: '/search.setting/basisConfig' })
}

export function setSearchConfig(params: any) {
    return request.post({ url: '/search.setting/basisSave', params })
}

export function getSearchExample() {
    return request.get({ url: '/search.setting/exampleConfig' })
}

export function setSearchExample(params: any) {
    return request.post({ url: '/search.setting/exampleSave', params })
}
