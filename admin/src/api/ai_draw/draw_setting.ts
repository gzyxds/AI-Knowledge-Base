import request from "@/utils/request";

// SD绘画配置详情
export function getDeawConfig(params: any) {
    return request.get({ url: "/setting.ai.draw/detail", params });
}

// SD绘画配置保存
export function setDeawConfig(params: any) {
    return request.post({ url: "/setting.ai.draw/save", params });
}
