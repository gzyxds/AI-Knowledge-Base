import { ref, reactive } from 'vue'
import {
    DrawFormData,
    loraItem,
    ModelItem,
    DrawTypeOptions
} from '../types/draw'
import { DrawModeEnum, DrawTypeEnum } from '../enums/DrawEnum'
import { useUserStore } from '@/stores/user'

import {
    drawingRecord,
    drawingDetail,
    drawing,
    drawingDelete,
    getDrawConfig,
    getModelCategoryList,
    getModelList
} from '@/api/draw'

export const taskStatusParams = reactive<{ status: number, model: DrawModeEnum }>({
    status: -1,
    model: DrawModeEnum.SD
})
export const taskIds = ref<number[]>([]) // 待完成任务列表
export const scroll = ref<boolean>(false) // 滚动到顶部
export const pageLoading = ref<boolean>(false) // 内容区域加载动画
export const createLoading = ref<boolean>(false) // 生成按钮加载动画
export const config = ref<Record<string, any>>({}) // 绘画配置
export const loraList = ref<loraItem[]>([]) // 微调模型列表
export const modelList = ref<ModelItem[]>([]) // 模型列表
export const modelCategory = ref<number>(0) // 当前选中的模型分类
export const modelCategoryList = ref<DrawTypeOptions[]>([]) // 模型分类列表

export const formData = ref<DrawFormData>({
    draw_api: DrawModeEnum.SD, // 绘图模型通道
    draw_type: 'txt2img', // 绘画类型 txt2img,img2img,scale2d
    draw_model: '', // 绘图主要模型
    draw_loras: [], // 微调模型lora
    denoising_strength: 0.75, // 重绘强度
    size: '512x512', // 图片尺寸
    prompt: '', // 正向提示词
    negative_prompt: '', // 负向提示词
    action: 'generate', // 绘画操作
    image_mask: '', // 图生图初始蒙版
    image_id: '', // 图片放大初始图片id
    complex_params: {
        step: 20, // 采样步数
        sampler_name: 'Euler a', // 采样模式
        seed: -1, // 随机种子
        cfg_scale: 7 // 提示词系数
    },

    quality: '', // dalle3 图片质量
    style: '', // mj / dalle3 风格选择

    version: '', // mj_goapi 版本
    origin_task_id: '' // mj_goapi 绘画任务ID (变图，放大等操作时必传)
})

/**
 * 获取任务分页列表
 */
export const { pager, getLists } = usePaging({
    fetchFun: drawingRecord,
    params: taskStatusParams
})

/**
 * 切换任务状态
 */
export const taskStatusChange = async (e: number) => {
    if (checkUserLogin()) return
    taskStatusParams.status = e
    pageLoading.value = true
    await getLists()
    pageLoading.value = false
}

/**
 * 获取未完成任务id
 */
const refreshTaskIds = () => {
    taskIds.value = getDrawingIds(pager.lists)
    if (taskIds.value.length > 0) {
        start()
    }
}

/**
 * 完成进行中的任务
 */
export const checkOngoingTask = async () => {
    try {
        pageLoading.value = true
        await getLists()
        config.value = await getDrawConfig({
            draw_api: formData.value.draw_api
        })
        pageLoading.value = false
        refreshTaskIds()
    } catch (error) {
        pageLoading.value = false
    }
}

/**
 * 删除绘画记录
 * @param id 绘画记录id
 */
export const deleteHandle = async (id: number) => {
    await drawingDelete({ ids: [id] })
    ElMessage.success('删除成功')
    getLists()
}

/**
 * 绘画任务队列
 */
const check = async () => {
    const route = useRoute()
    try {
        if (!taskIds.value.length) return end()
        const data = await drawingDetail({
            records_id: taskIds.value
        })
        const res = data.filter((item: any) => {
            if (item.status === 3 && !route.fullPath.includes('/draw')) {
                ElNotification({
                    title: '绘画成功',
                    type: 'success',
                    dangerouslyUseHTMLString: true,
                    message:
                        '<div>点击前往<a class="text-primary font-bold" href="/draw/sd">绘画记录</a>查看</div>',
                    duration: 10000
                })
            } else if (
                item.status === 2 &&
                !route.fullPath.includes('/draw/sd')
            ) {
                ElNotification({
                    title: '绘画失败',
                    message: item.fail_reason,
                    type: 'error',
                    duration: 10000
                })
            }
            return item.status === 3 || item.status === 2
        })
        if (res.length || !data.length) {
            endCallback()
        }

        return data
    } catch (error: any) {
        end()
        console.log('获取详情失败=>', error)
    }
}

/**
 * 任务完成回调
 */
const endCallback = async () => {
    end()
    console.log('获取详情结束=>')
    const userStore = useUserStore()
    userStore.getUser()
    await getLists()
    refreshTaskIds()
    config.value = await getDrawConfig({
        draw_api: formData.value.draw_api
    })
}

/**
 * 轮询更新任务状态
 */
const { start, end } = usePolling(check, {
    key: 'draw',
    totalTime: 10 * 60 * 1000,
    time: 2000,
    callback: endCallback
})

/**
 * 创建绘画任务
 * @param formData 绘画参数
 */
export const createTask = async (data: DrawFormData) => {
    try {
        end()
        await validateFormData(data)
        if (data.draw_type !== 'img2img' && data.draw_api === DrawModeEnum.SD) {
            data.image_mask = ''
        }
        createLoading.value = true
        await drawing(data)
        await getLists()
        createLoading.value = false
        scroll.value = !scroll.value
        refreshTaskIds()
    } catch (error) {
        createLoading.value = false
    } finally {
        resetFormData({ action: 'generate' })
    }
}

/**
 *  获取生成中的ids数组
 **/
const getDrawingIds = (arr: any[]) => {
    return arr
        .filter((item: any) => {
            return item.status === 1
        })
        .map((item: any) => item.id)
}

/**
 * 校验参数
 */
const validateFormData = (form: DrawFormData) => {
    return new Promise((resolve, reject) => {
        try {
            const data = form || formData.value
            if (data.draw_type === DrawTypeEnum.img2img) {
                if (data.image_mask === '') {
                    throw new Error('请上传参考图')
                }
            }
            if (data.prompt === '') {
                throw new Error('请输入提示词')
            }
            if (data.draw_api === '') {
                throw new Error('请选择主要模型')
            }
            resolve(true)
        } catch (error: any) {
            ElMessage.error(error.message)
            reject(error)
        }
    })
}

export const getModelCategory = async () => {
    return getModelCategoryList().then(async (res) => {
        modelCategoryList.value = [
            { label: '全部', value: 0 },
            ...res.map((item: any) => {
                return {
                    value: item.id,
                    label: item.name
                }
            })
        ]
        modelCategory.value = 0
        await getModel()
    })
}

export const getModel = () => {
    getModelList({
        category_id: modelCategory.value
    }).then((res) => {
        modelList.value = res
    })
}

/**
 * 重置绘画参数
 */
export const resetFormData = async (params?: Partial<DrawFormData>) => {
    if (params) {
        formData.value = {
            ...formData.value,
            ...params
        }
        const model = modelList.value.find((item) => {
            return item.model_name === params.draw_model
        })
        if (model) {
            modelCategory.value = model?.category_id
            await getModel()
            loraList.value = model.loras
        }
    } else {
        formData.value = {
            ...formData.value,
            ...{
                draw_model: '', // 绘图主要模型
                draw_loras: [], // 微调模型lora
                denoising_strength: 0.75,
                size: '512x512', // 图片尺寸
                prompt: '', // 正向提示词
                negative_prompt: '', // 负向提示词
                action: 'generate', // 绘画操作
                image_mask: '', // 图生图初始蒙版
                image_id: '', // 图片放大初始图片id
                complex_params: {
                    step: 20, // 采样步数
                    sampler_name: 'Euler a', // 采样模式
                    seed: -1, // 随机种子
                    cfg_scale: 7 // 提示词系数
                }
            }
        }
    }
}

/**
 * 验证是否登录
 */
export const checkUserLogin = () => {
    const userStore = useUserStore()
    if (!userStore.isLogin) {
        userStore.toggleShowLogin()
    }
    return !userStore.isLogin
}
