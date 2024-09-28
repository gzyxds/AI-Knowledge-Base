import type { SdDrawFormData, loraItem } from '../pages/draw/types/sd'

export type DrawingFormType = {
    prompt: string // 是 关键词
    action: string // 是 操作 generate=生成图片 upsample{index}=放大 variation{index}=变换
    image_base: string // 否 图片地址 图生成图时必填
    image_id: string // 否 图片id 图片放大或变换时必填
    model: string // 是 绘画的模型
    scale: string // 否 图片比例
    other: string // 否 其它参数
    no_content: string // 否 不想出现的内容
    version: string // 否 绘画版本
    style: string // 否 风格 动漫-default 可爱-cute 丰富-expressive 风景-scenic
    engine: string // 否 意间sd-绘画引擎
    quality: string // 否 DALLE-3 画质
}

// 生成图片
export function drawing(params: SdDrawFormData) {
    return $request.post({ url: '/draw.draw/drawing', params })
}

// 生成图片详情
export function drawingDetail(params: { records_id: number[] }) {
    return $request.post({ url: '/draw.draw_records/detail', params })
}

// 生成图片记录
export function drawingRecord(params: any) {
    return $request.get({ url: '/draw.draw_records/records', params })
}

// 删除
export function drawingDelete(params: { ids: number[] }) {
    return $request.post({ url: '/draw.draw_records/delete', params })
}

// 关键词分类
export function keywordCate(params: { model: string }) {
    return $request.get({ url: '/draw.draw_prompt/category', params })
}

// 关键词
export function keywordPrompt(params: any) {
    return $request.get({ url: '/draw.draw_prompt/prompt', params })
}

// 绘画示例
export function drawingExample(params: { model: string }) {
    return $request.get({ url: '/draw.draw_prompt/example', params })
}

// 关键词翻译
export function translate(params: any) {
    return $request.get({ url: '/draw.draw_prompt/translate', params })
}

// 绘画模型
export function drawingModel() {
    return $request.get({ url: '/draw.draw/modelConfig' })
}

// SD模型
export function getModelList(params: { category_id: number | string }) {
    return $request.get({ url: '/draw.draw/getSdModel', params })
}

// SD模型分类
export function getModelCategoryList() {
    return $request.get({ url: '/draw.draw/getSdModelCategory' })
}

// SD采样算法
export function getSdSamplersList() {
    return $request.get({ url: '/draw.draw/getSdSamplers' })
}

// SD绘画配置
export function getDrawConfig(params: { draw_api: string }) {
    return $request.get({ url: '/draw.draw/config', params })
}
