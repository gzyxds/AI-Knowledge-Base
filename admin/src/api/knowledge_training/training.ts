import request from '@/utils/request'

// 数据训练
export function dataTrain(params?: any) {
    return request.post({ url: '/know.Embedding/import', params, timeout: 100000 })
}

//训练数据列表
export function trainDataList(params?: any) {
    return request.get({ url: '/know.Embedding/lists', params })
}

//重试
export function trainingRetry(params?: any) {
    return request.post({ url: '/know.Embedding/retry', params })
}

//训练数据删除
export function trainingDataDel(params?: any) {
    return request.post({ url: '/know.Embedding/delete', params })
}

export function trainingDataEdit(params: any) {
    return request.post({ url: '/know.Embedding/edit', params })
}

export function webHtmlCapture(params: any) {
    return request.post({ url: '/know.embedding/capture', params })
}

export function trainingTasks(params?: any) {
    return request.post(
        { url: '/know.Embedding/tasks', params },
        {
            showProgress: false
        }
    )
}
