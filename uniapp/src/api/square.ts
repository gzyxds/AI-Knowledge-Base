import request from '@/utils/request'

interface PageType {
    page_no: number
    page_size: number
}

export interface SquareQuery extends PageType {
    category_id?: number |string
    style_id?: number |string
    keyword: string
}

/**
 * @description 绘画广场列表
 * @param { SquareQuery } data
 * @return { Promise }
 */
export function getDrawSquare(data?: SquareQuery) {
    return request.get({url: '/draw.drawSquare/lists', data})
}

/**
 * @description 绘画广场详情
 * @param { Object } data
 * @return { Promise }
 */
export function getDrawSquareDetail(data?: { id: number }) {
    return request.get({url: '/draw.drawSquare/detail', data})
}

/**
 * @description 绘画收藏/ 取消收藏
 * @param { Object } data
 * @return { Promise }
 */
export function drawSquareCollect(data: { records_id: string | number, status: number }) {
    return request.post({url: '/draw.draw/collect', data})
}

/**
 * @description 音乐广场列表
 * @param { SquareQuery } data
 * @return { Promise }
 */
export function getMusicSquare(data?: SquareQuery) {
    return request.get({url: '/musicSquare/lists', data})
}

/**
 * @description 音乐广场详情
 * @param { SquareQuery } data
 * @return { Promise }
 */
export function getMusicDetail(data?: { id: number }) {
    return request.get({url: '/musicSquare/detail', data})
}

/**
 * @description 音乐广场推荐列表
 * @return { Promise }
 */
export function getMusicRecommendLists() {
    return request.get({url: '/musicSquare/recommendLits'})
}

/**
 * @description 音乐收藏/ 取消收藏
 * @param { Object } data
 * @return { Promise }
 */
export function musicSquareCollect(data: { records_id: string, status: number }) {
    return request.post({url: '/music/collect', data})
}

/**
 * @description 视频广场列表
 * @param { SquareQuery } data
 * @return { Promise }
 */
export function getVideoSquare(data?: SquareQuery) {
    return request.get({url: '/videoSquare/lists', data})
}

/**
 * @description 视频收藏/ 取消收藏
 * @param { Object } data
 * @return { Promise }
 */
export function videoSquareCollect(data: { records_id: string, status: number }) {
    return request.post({url: '/video/collect', data})
}


/**
 * @description 视频广场详情
 * @param { Object } data
 * @return { Promise }
 */
export function getSquareVideoDetail(data?: { id: number }) {
    return request.get({url: '/VideoSquare/detail', data})
}