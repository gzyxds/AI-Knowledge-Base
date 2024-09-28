import request from "@/utils/request";

// 绘画模型列表
export function getModelList(params: any) {
    return request.get(
        { url: "/draw.draw_model/lists", params },
        {
            ignoreCancelToken: true,
        }
    );
}

// 绘画微调模型列表
export function getLoraList(params: any) {
    return request.get(
        { url: "/draw.draw_model/getLoraList", params },
        {
            ignoreCancelToken: true,
        }
    );
}

// 获取绘画模型详情
export function getModelDetail(params: any) {
    return request.get({ url: '/draw.draw_model/detail', params })
}


// 获取系统绘画模型
export function getSdModel(params: any) {
    return request.get({ url: "/draw.draw_model/getSdModel", params });
}

// 获取系统绘画微调模型
export function getSdLora(params: any) {
    return request.get({ url: "/draw.draw_model/getSdLora", params });
}

// 新增绘画模型
export function addModel(params: any) {
    return request.post({ url: "/draw.draw_model/add", params });
}

// 编辑绘画模型
export function editModel(params: any) {
    return request.post({ url: "/draw.draw_model/edit", params });
}

// 删除绘画模型
export function delModel(params: any) {
    return request.post({ url: "/draw.draw_model/delete", params });
}
// 修改绘画模型状态
export function editModelStatus(params: any) {
    return request.post({ url: "/draw.draw_model/status", params });
}
