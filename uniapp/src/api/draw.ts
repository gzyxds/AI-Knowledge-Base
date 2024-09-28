import request from "@/utils/request";

/**
 * 绘图类型选项
 */
export type DrawTypeOptions =
    | {
          label: string;
          value: string | number | boolean;
          disabled?: boolean;
          [key: string]: any;
      }
    | string
    | number
    | boolean
    | undefined;

/**
 * 高级参数
 */
export interface ComplexParams {
    seed: number | string;

    // sd 所需
    step?: number;
    sampler_name?: string;
    cfg_scale?: number;

    // mj 所需
    iw?: number; // 参考图权重
    q?: string; // 图片质量
    s?: number; // 风格化值
    c?: number; // 混乱圈
}

/**
 * 绘图表单数据
 */
export interface DrawFormData {
    draw_api: string;
    draw_type: string;
    draw_model: string;
    draw_loras?: string[] | string;
    denoising_strength?: number;
    prompt: string;
    negative_prompt: string;
    action: string;
    image_mask: string;
    image_id: string;
    size: string;
    complex_params: ComplexParams;

    style?: string;
    quality?: string;

    version?: string;
    origin_task_id?: string | number;
}

/**
 * lora列表项
 */
export interface loraItem {
    id: number;
    cover: string;
    title: string;
    model_name: string;
    sort: number;
    status: number;
    create_time: string;
}

/**
 * 采样器列表项
 */
export interface samplersItem {
    name: string;
    aliases: string[];
    options: {
        scheduler: string;
        discard_next_to_last_sigma: string;
        brownian_noise: string;
    };
}

/**
 * 绘图记录列表项
 */
export interface DrawRecordItem {
    id: number;
    task_id: string;
    complex_params?: string | never;
    prompt: string;
    prompt_en: string;
    prompt_desc: string;
    prompt_other: string;
    status: number;
    image: string | string[];
    image_base: string;
    thumbnail: string;
    model: string;
    image_url?: any;
    image_id?: any;
    scale: string;
    able_actions?: string[];
    able_cut?: boolean | number;
    fail_reason?: any;
    negative_prompt: string;
    version: string;
    style: string;
    engine: string;
    quality: string;
    censor_status: number;
    create_time: string;
    is_share?: number;
    is_collect?: number;
    loras: string[];
    type: 1 | 2 | 3 | 4;
}

/**
 * 模型列表项
 */
export interface ModelItem {
    id: number;
    title: string;
    model_name: string;
    cover: string;
    category_id: number;
    sort: number;
    status: number;
    loras: loraItem[];
    create_time: string;
}

// 生成图片
export function drawing(data: Partial<DrawFormData>) {
    return request.post({ url: "/draw.draw/drawing", data });
}

// 生成图片详情
export function drawingDetail(data: { records_id: number[] }) {
    return request.post({ url: "/draw.draw_records/detail", data });
}

// 绘画图片详情
export function drawRecordDetail(data: { id: number }) {
    return request.get({ url: "/draw.Draw/detail", data });
}

// 生成图片记录
export function drawingRecord(data: any) {
    return request.get({ url: "/draw.draw_records/records", data });
}

// 删除
export function drawingDelete(data: { ids: number[] }) {
    return request.post({ url: "/draw.draw_records/delete", data });
}

// 关键词分类
export function keywordCate(data: { model: string }) {
    return request.get({ url: "/draw.draw_prompt/category", data });
}

// 关键词
export function keywordPrompt(data: any) {
    return request.get({ url: "/draw.draw_prompt/prompt", data });
}

// 绘画示例
export function drawingExample(data: { model: string }) {
    return request.get({ url: "/draw.draw_prompt/example", data });
}

// 关键词翻译
export function translate(data: any) {
    return request.get({ url: "/draw.draw_prompt/translate", data });
}

// 绘画模型
export function drawingModel() {
    return request.get({ url: "/draw.draw/modelConfig" });
}

// SD模型
export function getModelList(data: { category_id: number | string }) {
    return request.get({ url: "/draw.draw/getSdModel", data });
}

// SD模型分类
export function getModelCategoryList() {
    return request.get({ url: "/draw.draw/getSdModelCategory" });
}

// SD采样算法
export function getSdSamplersList() {
    return request.get({ url: "/draw.draw/getSdSamplers" });
}

// SD绘画配置
export function getSdConfig(data: { draw_api: string }) {
    return request.get({ url: "/draw.draw/config", data });
}
