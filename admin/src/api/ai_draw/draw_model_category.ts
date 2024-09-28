import request from "@/utils/request";

//问题分类列表
export function getModelCategoryList(params: any) {
    return request.get(
        { url: "/draw.draw_model_category/lists", params },
        {
            ignoreCancelToken: true,
        }
    );
}

//新增问题示例
export function addModelCategory(params: any) {
    return request.post({ url: "/draw.draw_model_category/add", params });
}

//编辑问题示例
export function editModelCategory(params: any) {
    return request.post({ url: "/draw.draw_model_category/edit", params });
}

//删除问题示例
export function delModelCategory(params: any) {
    return request.post({ url: "/draw.draw_model_category/delete", params });
}
//修改状态
export function editModelCategoryStatus(params: any) {
    return request.post({ url: "/draw.draw_model_category/status", params });
}
