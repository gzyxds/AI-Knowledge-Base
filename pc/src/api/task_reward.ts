/**
 * @description 任务奖励列表
 * @return { Promise }
 */
export function getShareTaskList() {
    return $request.get({ url: '/share/task' })
}

/**
 * @description 获取分享ID
 * @return { Promise }
 */
export function getShareId() {
    return $request.get({ url: '/share/share' })
}

/**
 * @description 分享链接点击
 * @param { Object } params
 * @return { Promise }
 */
export function shareClick(params: { share_id: number }) {
    return $request.post({ url: '/share/click', params })
}

/**
 * @description 邀请链接点击
 * @param { Object } params
 * @return { Promise }
 */
export function shareInvite(params: { share_id: number }) {
    return $request.post({ url: '/share/invite', params })
}

/**
 * @description 签到接口
 * @return { Promise }
 */
export function signClick() {
    return $request.post({ url: '/share/sign' })
}

/**
 * @description 获取广场分类列表
 * @return { Promise }
 */
export function getSquareCategory(params: { type: number; share?: number }) {
    return $request.get({ url: '/squareCategory/lists', params })
}

/**
 * @description 分享到绘画广场
 * @param { Object } params
 * @return { Promise }
 */
export function shareDraw(params: {
    category_id: string
    prompts: string
    image: string
    records_id: string
}) {
    return $request.post({ url: '/draw.DrawSquare/add', params })
}

/**
 * @description 分享到音乐广场
 * @param { Object } params
 * @return { Promise }
 */
export function shareMusic(params: {
    records_id: string
    category_id: string
}) {
    return $request.post({ url: '/MusicSquare/add', params })
}

/**
 * @description 分享到视频广场
 * @param { Object } params
 * @return { Promise }
 */
export function shareVideo(params: {
    records_id: string
    category_id: string
}) {
    return $request.post({ url: '/VideoSquare/add', params })
}

/**
 * @description 获取智能体分类列表
 * @return { Promise }
 */
export function getAgentCategoryList() {
    return $request.get({ url: '/kb.robot/categoryLists' })
}

/**
 * @description 分享到智能体广场
 * @param { Object } params
 * @return { Promise }
 */
export function shareAgent(params: { cate_id: string; id: string }) {
    return $request.post({ url: '/kb.robot/share', params })
}
