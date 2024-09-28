/**
 * 绘图类型选项
 */
export type DrawTypeOptions =
    | {
          label: string
          value: string | number | boolean
          disabled?: boolean
          [key: string]: any
      }
    | string
    | number
    | boolean
    | undefined

/**
 * 高级参数
 */
export interface ComplexParams {
    seed: number

    // sd 所需
    step?: number
    sampler_name?: string
    cfg_scale?: number

    // mj 所需
    iw?: number // 参考图权重
    q?: number  // 图片质量
    s?: number  // 风格化值
    c?: number  // 混乱圈
}

/**
 * 绘图表单数据
 */
export interface DrawFormData {
    draw_api: string
    draw_type: string
    draw_model: string
    draw_loras: string[]
    denoising_strength: number
    prompt: string
    negative_prompt: string
    action: string
    image_mask: string
    image_id: string
    size: string
    complex_params: ComplexParams
    style?: string
    quality?: string

    version: string // mj-模型版本
    origin_task_id: string // mj_goapi 绘画任务ID (变图，放大等操作时必传)
}

/**
 * lora列表项
 */
export interface loraItem {
    id: number
    cover: string
    title: string
    model_name: string
    sort: number
    status: number
    create_time: string
}

/**
 * 采样器列表项
 */
export interface samplersItem {
    name: string
    aliases: string[]
    options: {
        scheduler: string
        discard_next_to_last_sigma: string
        brownian_noise: string
    }
}

/**
 * 绘图记录列表项
 */
export interface DrawRecordItem {
    id: number
    task_id: string
    prompt: string
    prompt_en: string
    prompt_desc: string
    prompt_other: string
    status: number
    image: string | string[]
    image_base: string
    image_mask?: string
    thumbnail: string
    model: string
    image_url?: any
    image_id?: any
    is_share?: number
    scale: string
    able_actions?: string[]
    able_cut?: boolean | number
    fail_reason?: any
    negative_prompt: string
    version: string
    style: string
    engine: string
    quality: string
    censor_status: number
    create_time: string
    loras: string[]
    loading?: boolean
    type: 1 | 2 | 3 | 4
}

/**
 * 模型列表项
 */
export interface ModelItem {
    id: number
    title: string
    model_name: string
    cover: string
    category_id: number
    sort: number
    status: number
    loras: loraItem[]
    create_time: string
}
