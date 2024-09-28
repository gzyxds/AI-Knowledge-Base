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
 * @param { SquareQuery } params
 * @return { Promise }
 */
export function getDrawSquare(params?: SquareQuery) {
    return $request.get({url: '/draw.drawSquare/lists', params})
}

/**
 * @description 绘画收藏/ 取消收藏
 * @param { Object } params
 * @return { Promise }
 */
export function drawSquareCollect(params: { records_id: string, status: number }) {
    return $request.post({url: '/draw.draw/collect', params})
}

/**
 * @description 音乐广场列表
 * @param { SquareQuery } params
 * @return { Promise }
 */
export function getMusicSquare(params?: SquareQuery) {
    return $request.get({url: '/musicSquare/lists', params})
}

//
/**
 * @description 音乐详情
 * @param { SquareQuery } params
 * @return { Promise }
 */
export function getMusicDetail(params: { id: number }) {
    return $request.get({ url: '/MusicSquare/detail', params })
}


/**
 * @description 音乐收藏/ 取消收藏
 * @param { Object } params
 * @return { Promise }
 */
export function musicSquareCollect(params: { records_id: string, status: number }) {
    return $request.post({url: '/music/collect', params})
}

/**
 * @description 视频广场列表
 * @param { SquareQuery } params
 * @return { Promise }
 */
export function getVideoSquare(params?: SquareQuery) {
    return $request.get({url: '/videoSquare/lists', params})
}

/**
 * @description 视频收藏/ 取消收藏
 * @param { Object } params
 * @return { Promise }
 */
export function videoSquareCollect(params: { records_id: string, status: number }) {
    return $request.post({url: '/video/collect', params})
}