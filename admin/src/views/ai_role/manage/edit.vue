<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-page-header :content="title" @back="$router.back()"/>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <el-form ref="formRef" :model="formData" label-width="84px" :rules="rules">
                <!-- 图标 -->
                <el-form-item label="图标" prop="image">
                    <material-picker v-model="formData.image" :limit="1"/>
                </el-form-item>
                <!-- 角色名称 -->
                <el-form-item label="角色名称" prop="name">
                    <div class="w-[380px]">
                        <el-input placeholder="请输入角色名称" v-model="formData.name"></el-input>
                    </div>
                </el-form-item>
                <el-form-item label="角色描述" prop="describe">
                    <div class="w-[380px]">
                        <el-input
                            v-model="formData.describe"
                            placeholder="请输入角色描述"
                            :rows="3"
                            type="textarea"
                        ></el-input>
                    </div>
                </el-form-item>
                <!-- 所属类别 -->
                <el-form-item label="所属类别" prop="category_id">
                    <div class="w-[380px]">
                        <el-select placeholder="请选择" class="w-full" v-model="formData.category_id">
                            <el-option
                                v-for="(item, index) in categoryList"
                                :key="index"
                                :label="item.name"
                                :value="item.id"
                            />
                        </el-select>
                    </div>
                </el-form-item>
                <!-- 角色描述 -->
                <el-form-item label="调教文案" prop="content">
                    <div class="w-[380px]">
                        <el-input
                            v-model="formData.content"
                            placeholder="请输入调教文案"
                            :rows="3"
                            type="textarea"
                        ></el-input>
                    </div>
                </el-form-item>
                <!-- 模型内容 -->

                <!-- 提示文字 -->
                <el-form-item label="欢迎语" prop="tips">
                    <div class="w-[380px]">
                        <el-input
                            placeholder="打开聊天窗口后会主动发送，前后添加井号可添加问题示例"
                            :rows="6"
                            type="textarea"
                            v-model="formData.tips"
                        ></el-input>
                        <div class="form-tips !text-base">
                            打开聊天窗口后会主动发送，前后添加井号可添加提问示例，例如：#帮我写一则关于xxx的文案#
                            多个问题请用回车换行
                            <el-button type="primary" link @click="formData.tips = `你好，我是你的AI好友写作助手，我可以为你提供写作方面的帮助。只需要将您的文章提交给我，我就可以修改和优化。

你可以试试这样提问：
#我想让文章的语法更加准确。#
#我想让文章更加流畅易读。#
#请给我一些关于如何提高文本连贯性和可读性的建议。#
                            `">
                                使用系统示例
                            </el-button>
                        </div>
                    </div>
                </el-form-item>

                <!-- 全局指令 -->
                <el-form-item label="全局指令" prop="system">
                    <div class="w-[380px]">
                        <el-input
                            placeholder="如果填写了该全局指令则会替换对话设置的全局指令"
                            :rows="3"
                            type="textarea"
                            v-model="formData.system"
                        ></el-input>
                    </div>
                </el-form-item>

                <!--                <el-form-item label="模型" prop="category_id">-->
                <!--                    <div class="flex-1">-->
                <!--                        <el-select class="w-[280px]" v-model="formData.model">-->
                <!--                            <el-option-->
                <!--                                v-for="item in modeList"-->
                <!--                                :value="item"-->
                <!--                                :label="item"-->
                <!--                                :key="item"-->
                <!--                            ></el-option>-->
                <!--                        </el-select>-->
                <!--                        <div class="form-tips">默认：gpt-3.5-turbo</div>-->
                <!--                    </div>-->
                <!--                </el-form-item>-->

                <!--                <el-form-item label="最大长度" prop="name">-->
                <!--                    <div>-->
                <!--                        <el-input-->
                <!--                            placeholder="请输入"-->
                <!--                            class="w-[280px]"-->
                <!--                            v-model="formData.max_tokens"-->
                <!--                        ></el-input>-->
                <!--                        <div class="form-tips">-->
                <!--                            每次回答的最大字符长度。如果设置过小，回答中间可能被切断。如果设置过大，则可能产生更多的计费。-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                </el-form-item>-->
                <el-form-item label="模型参数" prop="context_num">
                    <div class="flex flex-wrap">
                        <div class="w-[190px] mr-[20px] mb-[20px]">
                            <div class="flex items-center text-tx-regular text-xs">
                                <span class="mr-[4px] mt-[2px]">上下文总数</span>
                                <el-tooltip
                                    class="box-item"
                                    effect="dark"
                                    content="生成文本的最大长度，取值范围为1~5之间的整数"
                                    placement="top"
                                >
                                    <el-icon size="16px">
                                        <QuestionFilled/>
                                    </el-icon>
                                </el-tooltip>
                            </div>

                            <el-slider v-model="formData.context_num" :min="1" :max="5"/>
                        </div>
                        <div class="w-[190px] mr-[20px] mb-[20px]">
                            <div class="flex items-center text-tx-regular text-xs">
                                <span class="mr-[4px] mt-[2px]">回复条数</span>
                                <el-tooltip
                                    class="box-item"
                                    effect="dark"
                                    content="为每个输入消息生成多个回复，取值范围为1~5之间的整数。"
                                    placement="top"
                                >
                                    <el-icon size="16px">
                                        <QuestionFilled/>
                                    </el-icon>
                                </el-tooltip>
                            </div>

                            <el-slider v-model="formData.n" :min="1" :max="5"/>
                        </div>
                        <div class="w-[190px] mr-[20px] mb-[20px]">
                            <div class="flex items-center text-tx-regular text-xs">
                                <span class="mr-[4px] mt-[2px]">词汇属性</span>
                                <el-tooltip
                                    class="box-item"
                                    effect="dark"
                                    content="用于控制生成文本的随机性，取值范围为0~1之间的浮点数，建议取值0.7左右。"
                                    placement="top"
                                >
                                    <el-icon size="16px">
                                        <QuestionFilled/>
                                    </el-icon>
                                </el-tooltip>
                            </div>

                            <el-slider
                                v-model="formData.temperature"
                                :min="0"
                                :max="1"
                                :step="0.1"
                            />
                        </div>

                        <div class="w-[190px] mr-[20px] mb-[20px]">
                            <div class="flex items-center text-tx-regular text-xs">
                                <span class="mr-[4px] mt-[2px]">随机属性</span>
                                <el-tooltip
                                    class="box-item"
                                    effect="dark"
                                    content="用于控制生成文本的多样性，取值范围为0~1之间的浮点数，建议取值0.9左右。"
                                    placement="top"
                                >
                                    <el-icon size="16px">
                                        <QuestionFilled/>
                                    </el-icon>
                                </el-tooltip>
                            </div>
                            <el-slider v-model="formData.top_p" :min="0" :max="1" :step="0.1"/>
                        </div>
                        <div class="w-[190px] mr-[20px] mb-[20px]">
                            <div class="flex items-center text-tx-regular text-xs">
                                <span class="mr-[4px] mt-[2px]">话题属性</span>
                                <el-tooltip
                                    class="box-item"
                                    effect="dark"
                                    content="用于控制生成文本中是否出现给定的关键词，取值范围为0~1之间的浮点数，建议取值0.5左右。"
                                    placement="top"
                                >
                                    <el-icon size="16px">
                                        <QuestionFilled/>
                                    </el-icon>
                                </el-tooltip>
                            </div>

                            <el-slider
                                v-model="formData.presence_penalty"
                                :step="0.1"
                                :min="0"
                                :max="1"
                            />
                        </div>
                        <!-- <div class="w-[190px] mr-[20px] mb-[20px]">
                            <div class="flex items-center text-tx-regular text-xs">
                                <span class="mr-[4px] mt-[2px]">重复属性</span>
                                <el-tooltip
                                    class="box-item"
                                    effect="dark"
                                    content="用于控制生成文本中重复的程度，取值范围为0~1之间的浮点数，建议取值0.5左右"
                                    placement="top"
                                >
                                    <el-icon size="16px"><QuestionFilled /></el-icon>
                                </el-tooltip>
                            </div>
                            <el-slider
                                v-model="formData.frequency_penalty"
                                :step="0.1"
                                :min="0"
                                :max="1"
                            />
                        </div> -->
                    </div>
                </el-form-item>
                <!-- 排序 -->
                <el-form-item label="排序">
                    <div class="w-[380px]">
                        <el-input v-model="formData.sort" placeholder=""></el-input>
                    </div>
                </el-form-item>
                <!-- 状态 -->
                <el-form-item label="状态">
                    <el-switch v-model="formData.status" :active-value="1" :inactive-value="0"/>
                </el-form-item>
            </el-form>
        </el-card>
        <footer-btns>
            <el-button type="primary" @click="handleSave">保存</el-button>
        </footer-btns>
    </div>
</template>
<script lang="ts" setup>
import type {FormInstance} from 'element-plus'
import Popup from '@/components/popup/index.vue'
import {skillCategoryLists} from '@/api/ai_role/type'
import {addSkillModel, editkillModel, skillModelDetail, modelList} from '@/api/ai_role/manage'

const emit = defineEmits(['success', 'close'])
//表单ref
const formRef = shallowRef<FormInstance>()
//弹框ref
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const {query} = useRoute()
const router = useRouter()
const title = computed(() => {
    return query.mode == 'edit' ? '编辑角色管理' : '新增角色管理'
})
//表单数据
const formData = reactive({
    id: '',
    name: '', //名称
    category_id: '', //分类
    content: '', //内容
    sort: 0, //排序
    status: 1, //状态
    image: '', //图标
    tips: '', //提示内容
    system: '', // 全局指令
    describe: '', //描述

    model: 'gpt-3.5-turbo',
    // max_tokens: 150,

    n: 0,
    temperature: 0.7,
    context_num: 2,
    top_p: 0.9,
    presence_penalty: 0.5,
    frequency_penalty: 0.5
})

//分类列表
const categoryList: any = ref([])
// 模型略表
const modeList = ref<any>([])

//表单校验规则
const rules = {
    name: [
        {
            required: true,
            message: '请输入名称',
            trigger: ['blur']
        }
    ],
    image: [
        {
            required: true,
            message: '请选择图片',
            trigger: ['blur']
        }
    ],
    category_id: [
        {
            required: true,
            message: '请选择类别',
            trigger: ['blur']
        }
    ],
    content: [
        {
            required: true,
            message: '请输入调教文案',
            trigger: ['blur']
        }
    ],
    tips: [
        {
            required: true,
            message: '请输入提示内容',
            trigger: ['blur']
        }
    ],
    model: [
        {
            required: true,
            message: '请选择模型',
            trigger: ['blur']
        }
    ],
    context_num: [
        {
            required: true,
            message: '请选择模型参数',
            trigger: ['blur']
        }
    ]
}

//保存
const handleSave = async () => {
    await formRef.value?.validate()
    query.mode == 'edit' ? await editkillModel(formData) : await addSkillModel(formData)
    router.back()
}

const getDetail = async () => {
    try {
        const data = await skillModelDetail({
            id: query.id
        })
        setFormData(data)
    } catch (e) {
        console.log(e)
    }
}

//获取分类列表
const getCategoryList = async () => {
    const {lists} = await skillCategoryLists()
    categoryList.value = lists
}

const setFormData = async (row: any) => {
    for (const key in formData) {
        if (row[key] != null && row[key] != undefined) {
            //@ts-ignore
            formData[key] = row[key]
        }
    }
}

query.id && getDetail()
getCategoryList()
</script>
