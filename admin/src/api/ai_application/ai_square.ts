import request from "@/utils/request";

// --------------绘画列表设置

//列表
export function getDrawSquareList(params: any) {
    return request.get(
        { url: '/draw.draw_square/lists', params },
        {
            ignoreCancelToken: true
        }
    )
}

//详情
export function getDrawSquareDetail(params: { id?: number }) {
    return request.get({ url: '/draw.draw_square/detail', params })
}

//新增
export function addDrawSquare(params: any) {
    return request.post({ url: '/draw.draw_square/add', params })
}

//编辑
export function editDrawSquare(params: any) {
    return request.post({ url: '/draw.draw_square/edit', params })
}

//审核
export function auditDrawSquare(params: any) {
    return request.post({ url: '/draw.draw_square/verifyStatus', params })
}

//删除
export function delDrawSquare(params: any) {
    return request.post({ url: '/draw.draw_square/del', params })
}

//修改状态
export function editDrawSquareStatus(params: any) {
    return request.post({ url: '/draw.draw_square/isShow', params })
}

//迁移分类
export function putDrawSquareTransferCategory(params: any) {
    return request.post({ url: '/draw.draw_square/removeCategory', params })
}


// --------------音乐列表设置
//列表
export function getMusicSquareList(params: any) {
    return request.get(
        { url: '/music.musicSquare/lists', params },
        {
            ignoreCancelToken: true
        }
    )
}

//详情
export function getMusicSquareDetail(params: { id?: number }) {
    return request.get({ url: '/music.musicSquare/detail', params })
}

//新增
export function addMusicSquare(params: any) {
    return request.post({ url: '/music.musicSquare/add', params })
}

//编辑
export function editMusicSquare(params: any) {
    return request.post({ url: '/music.musicSquare/edit', params })
}

//审核
export function auditMusicSquare(params: any) {
    return request.post({ url: '/music.musicSquare/verifyStatus', params })
}

//删除
export function delMusicSquare(params: any) {
    return request.post({ url: '/music.musicSquare/del', params })
}

//修改状态
export function editMusicSquareStatus(params: any) {
    return request.post({ url: '/music.musicSquare/isShow', params })
}

//迁移分类
export function putMusicSquareTransferCategory(params: any) {
    return request.post({ url: '/music.musicSquare/removeCategory', params })
}


// --------------视频广场设置

//列表
export function getVideoSquareList(params: any) {
    return request.get(
        { url: '/video.videoSquare/lists', params },
        {
            ignoreCancelToken: true
        }
    )
}

//详情
export function getVideoSquareDetail(params: { id?: number }) {
    return request.get({ url: '/video.videoSquare/detail', params })
}

//新增
export function addVideoSquare(params: any) {
    return request.post({ url: '/video.videoSquare/add', params })
}

//编辑
export function editVideoSquare(params: any) {
    return request.post({ url: '/video.videoSquare/edit', params })
}

//审核
export function auditVideoSquare(params: any) {
    return request.post({ url: '/video.videoSquare/verifyStatus', params })
}

//删除
export function delVideoSquare(params: any) {
    return request.post({ url: '/video.videoSquare/del', params })
}

//修改状态
export function editVideoSquareStatus(params: any) {
    return request.post({ url: '/video.videoSquare/isShow', params })
}

//迁移分类
export function putVideoSquareTransferCategory(params: any) {
    return request.post({ url: '/video.videoSquare/removeCategory', params })
}


// --------------AI广场设置

export type AiSquareSetFormType = {
    draw_award: {
        is_show_user: number // 是  显示用户信息：1-开启；0-关闭；
        auto_audit: number // 是  自动通过审核：1-开启；0-关闭；
    }
    music_award: {
        is_show_user: number // 是  显示用户信息：1-开启；0-关闭；
        auto_audit: number // 是  自动通过审核：1-开启；0-关闭；
    }
    video_award: {
        is_show_user: number // 是  显示用户信息：1-开启；0-关闭；
        auto_audit: number // 是  自动通过审核：1-开启；0-关闭；
    }
}

export function aiSquareGetConfig(): Promise<AiSquareSetFormType> {
    return request.get({ url: '/market.activityReward/getSquareSetting' })
}

//获取卡密设置
export function aiSquareSetConfig(params: AiSquareSetFormType) {
    return request.post({ url: '/market.activityReward/setSquareSetting', params })
}


// --------------AI广场分类
export function getSquareCategory(params: any): Promise<any> {
    return request.get({ url: '/square.squareCategory/lists', params })
}

export function getSquareCategoryAll(params: any): Promise<any> {
    return request.get({ url: '/square.squareCategory/categoryLists', params })
}




// 新增分类
export function postSquareCategory(params: any) {
    return request.post({ url: '/square.squareCategory/add', params })
}

// 编辑分类
export function putSquareCategory(params: any) {
    return request.post({ url: '/square.squareCategory/edit', params })
}

// 删除分类
export function deleteSquareCategory(params: { id: number[] | number }) {
    return request.post({ url: '/square.squareCategory/del', params })
}

// 设置分类状态
export function putSquareCategoryStatus(params: { id: number }) {
    return request.post({ url: '/square.squareCategory/status', params })
}