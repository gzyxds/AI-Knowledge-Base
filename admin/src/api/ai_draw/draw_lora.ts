import request from "@/utils/request";

// 微调模型列表
export function getLoraList(params: any) {
    return request.get(
        { url: "/draw.draw_lora/lists", params },
        {
            ignoreCancelToken: true,
        }
    );
}

// 获取全部微调模型
export function getLoraListAll(params: any) {
    return request.get(
        { url: "/draw.draw_lora/all", params }
    );
}

// 获取系统微调模型
export function getSdLora(params: any) {
    return request.get({ url: "/draw.draw_lora/getSdLora", params });
}

// 新增微调模型
export function addLora(params: any) {
    return request.post({ url: "/draw.draw_lora/add", params });
}

// 编辑微调模型
export function editLora(params: any) {
    return request.post({ url: "/draw.draw_lora/edit", params });
}

// 删除微调模型
export function delLora(params: any) {
    return request.post({ url: "/draw.draw_lora/delete", params });
}
// 修改微调模型状态
export function editLoraStatus(params: any) {
    return request.post({ url: "/draw.draw_lora/status", params });
}
