export interface MemberRequest {
    apply_list: ApplyList[];
    /**
     * 会员权益
     */
    benefits_list: BenefitsList[];
    /**
     * 描述
     */
    describe: string;
    /**
     * 是否已有人使用
     */
    is_use?: string | number
    /**
     * 是否推荐
     */
    is_recommend: number;
    /**
     * 对话模型
     */
    model_list: ModelList;
    /**
     * 套餐名称
     */
    name: string;
    /**
     * 价格列表
     */
    price_list: PriceList[];
    /**
     * 排序
     */
    sort: number | string;
    /**
     * 状态
     */
    status: number;
    [property: string]: any;
}

export interface ApplyList {
    /**
     * 渠道id
     */
    channel: string;
    /**
     * 每日限额
     */
    day_limit: string;
    /**
     * 渠道名称
     */
    name: string;
    /**
     * 状态
     */
    status: number;
    [property: string]: any;
}

export interface BenefitsList {
    /**
     * 描述
     */
    describe?: string;
    /**
     * 图标
     */
    image?: string;
    /**
     * 名称
     */
    name?: string;
    /**
     * 状态
     */
    status?: number;
    [property: string]: any;
}

export interface ModelList {
    /**
     * 对话模型
     */
    chat_model: ChatModel[];
    /**
     * 矢量模型
     */
    vector_model: string[];
    [property: string]: any;
}

export interface ChatModel {
    /**
     * 渠道id
     */
    channel: string;
    /**
     * 每日限额
     */
    day_limit: number;
    /**
     * 状态
     */
    status: number;
    [property: string]: any;
}

export interface PriceList {
    /**
     * 时长（时长类型为3时可以为空）
     */
    duration?: string;
    /**
     * 时长类型：1-天；2-月；3-永久
     */
    duration_type: number;
    /**
     * 赠送电力值
     */
    give_balance: number | string;
    /**
     * 赠送智能体
     */
    give_robot: number | string;
    /**
     * 是否赠送
     */
    is_give: number;
    /**
     * 划线价
     */
    lineation_price: number | string;
    /**
     * 售价
     */
    sell_price: number | string;
    /**
     * 会员标签
     */
    tags: string;
    /**
     * 是否推荐
     */
    is_recommend?: number;
    /**
     * 状态
     */
    status: number;
    /**
     * id
     */
    id?: number;
    [property: string]: any;
}