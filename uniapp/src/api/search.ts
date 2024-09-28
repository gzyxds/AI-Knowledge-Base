import request, { RequestEventStreamConfig } from '@/utils/request'

export function getSearchExample() {
    return request.get({ url: '/search/example' })
}

export function postSearch(data: any, config: RequestEventStreamConfig) {
    return request.eventStream(
        { url: '/search/query', data, method: 'POST' },
        config
    )
}

export function getSearchConfig() {
    return request.get({ url: '/search/config' })
}
