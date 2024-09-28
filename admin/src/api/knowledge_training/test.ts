import request from '@/utils/request'

//数据训练
export function konwledgeTest(params?: any) {
    return request.post({ url: '/know.Embedding/test', params })
}
