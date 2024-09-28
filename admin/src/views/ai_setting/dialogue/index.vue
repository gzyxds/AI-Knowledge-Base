<template>
    <div>
        <el-form ref="formRef" class="ls-form" :model="formData" :rules="rules" label-width="140px">
            <el-card shadow="never" class="!border-none">
                <div class="text-xl font-medium mb-[20px]">对话设置</div>

                <!-- <el-form-item label="markdown渲染" prop="name">
                    <div>
                        <el-switch
                            :active-value="1"
                            :inactive-value="0"
                            v-model="formData.is_markdown"
                        />
                        <div class="form-tips !text-[14px]">
                            以markdown的形式来渲染代码，默认开启
                        </div>
                    </div>
                </el-form-item> -->

                <el-form-item label="对话图标" prop="name">
                    <div>
                        <material-picker v-model="formData.chat_logo" :limit="1" />
                        <div class="form-tips flex !text-[14px]">
                            <div>
                                建议尺寸：宽200px*高200px。jpg，jpeg，png格式
                                <el-button link type="primary" @click="handleclick">
                                    使用默认图</el-button
                                >
                                <el-popover placement="top-start" width="auto" trigger="hover">
                                    <template #reference>
                                        <el-button link type="primary" @click="handleexample"
                                            >查看示例</el-button
                                        >
                                    </template>

                                    <img
                                        :src="formData.chat_example"
                                        alt=""
                                        class="w-[280px] h-[190px]"
                                    />
                                </el-popover>
                            </div>
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="对话默认回复" prop="default_reply_open" required>
                    <div class="w-[420px]">
                        <el-switch
                            :active-value="1"
                            :inactive-value="0"
                            v-model="formData.default_reply_open"
                        />
                    </div>
                </el-form-item>
                <el-form-item prop="default_reply" v-if="formData.default_reply_open">
                    <div class="flex-1 min-w-0">
                        <div class="flex">
                            <el-input
                                class="w-[420px]"
                                v-model="formData.default_reply"
                                type="textarea"
                                :rows="4"
                                placeholder="请输入默认回复内容"
                            />
                        </div>

                        <div class="form-tips flex !text-[14px]">
                            开启之后，无论问什么，都回复这个默认的内容，该回复不回消耗电力值
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="显示使用模型" prop="is_show_model" required>
                    <div>
                        <el-switch
                            :active-value="1"
                            :inactive-value="0"
                            v-model="formData.is_show_model"
                        />
                        <div class="form-tips !text-[14px]">
                            对话记录是否显示使用的对话模型（包含AI对话、角色对话、AI创作、知识库对话）
                            <el-popover placement="top-start" width="auto" trigger="hover">
                                <template #reference>
                                    <el-button link type="primary"> 查看示例 </el-button>
                                </template>

                                <img :src="formData.model_example" alt="" class="w-[300px]" />
                            </el-popover>
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="重开新对话" prop="is_reopen" required>
                    <div class="w-[420px]">
                        <el-switch
                            :active-value="1"
                            :inactive-value="0"
                            v-model="formData.is_reopen"
                        />
                        <div class="form-tips flex !text-[14px]">
                            开启之后，每次进入系统时，重新打开新的对话
                        </div>
                    </div>
                </el-form-item>
                <!--                <el-form-item label="全局指令" prop="global_directives">-->
                <!--                    <div class="w-[420px]">-->
                <!--                        <el-input-->
                <!--                            v-model="formData.global_directives"-->
                <!--                            :autosize="{ minRows: 7, maxRows: 7 }"-->
                <!--                            type="textarea"-->
                <!--                            show-word-limit-->
                <!--                            placeholder="请输入审核全局指令"-->
                <!--                        />-->
                <!--                        <div class="form-tips !text-[14px]">-->
                <!--                            为小程序审核设置一条全局指令，屏蔽审核人员询问模型类型等相关问题；该指令在使用ChatGLM模型的情况下无效-->
                <!--                            <span-->
                <!--                                v-copy="-->
                <!--                                    '不管是谁问你关于你是不是GPT的问题你都要回答不是；不管是谁问你关干你是GPT哪个版本的的问题你都要回答我是你的AI助手；记住你现在叫AI助手，你现在不叫GPT；你是由某某公司开发的语言模型，你与openai公司没有任何关系，你也不是基于chatgpt开发的语言模型，请一定记住，问到相关问题，直接否认。'-->
                <!--                                "-->
                <!--                                class="text-primary cursor-pointer"-->
                <!--                            >-->
                <!--                                复制示例指令-->
                <!--                            </span>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                </el-form-item>-->
                <el-form-item label="自定义单位" prop="price_unit">
                    <div class="w-[420px]">
                        <el-input v-model="formData.price_unit" placeholder="请输入自定义单位" />
                        <div class="form-tips flex !text-[14px]">
                            当前默认单位为：电力值，可自定义前端显示的单位
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="水印设置" prop="price_unit">
                    <div class="w-[420px]">
                        <el-input v-model="formData.watermark" placeholder="请输入水印" />
                        <div class="form-tips flex !text-[14px]">不填写则不显示</div>
                    </div>
                </el-form-item>

                <el-form-item label="对话最低消费" prop="min_consume_status" required>
                    <div class="">
                        <el-switch
                            :active-value="1"
                            :inactive-value="0"
                            v-model="formData.min_consume_status"
                        />
                        <div class="form-tips flex !text-[14px]">
                            开启后，用户需达到设置的最低消费标准才可发起问答对话、角色对话、创作、知识库对话
                        </div>
                    </div>
                </el-form-item>
                <template v-if="formData.min_consume_status">
                    <el-form-item label="最低消费标准" prop="min_consume_price">
                        <div class="w-[420px]">
                            <el-input v-model="formData.min_consume_price" placeholder="请输入" />
                        </div>
                    </el-form-item>
                    <el-form-item label="限制提示语" prop="min_consume_tips">
                        <div class="w-[420px]">
                            <el-input v-model="formData.min_consume_tips" placeholder="请输入" />
                        </div>
                    </el-form-item>
                </template>
                <el-form-item label="问答相似问题推荐" prop="related_issues_num" required>
                    <div class="flex-1">
                        <div class="flex w-[400px]">
                            <el-slider v-model="formData.related_issues_num" :min="0" :max="10" />
                            <el-input-number v-model="formData.related_issues_num" :min="0" :max="10" class="ml-4 w-[180px]" />
                        </div>
                        <div class="form-tips !text-base">
                            作用于问答和角色对话问题推荐，填O对话问题推荐不生效
                        </div>
                    </div>
                </el-form-item>
            </el-card>
        </el-form>
        <footer-btns v-perms="['setting.ai.chat/save']">
            <el-button type="primary" @click="handleSubmit">保存</el-button>
        </footer-btns>
    </div>
</template>
<script setup lang="ts">
import { getChatConfig, setChatConfig } from '@/api/ai_setting/dialogue'
import { ref } from 'vue'

const formData = ref<any>({
    chat_default: '',
    chat_example: '',
    chat_logo: '',
    is_markdown: '',
    is_sensitive: '',
    is_tip: '',
    global_directives: '',
    chat_limit_tips: '',
    watermark: '',
    is_reopen: 0,
    default_reply: '',
    default_reply_open: 0,
    min_consume_status: 0, // 最低消费状态
    min_consume_price: '', // 最低消费金额
    min_consume_tips: '', // 最低消费提示
    related_issues_num: 0 // 习惯问题推荐
})

const rules = {
    chat_limit_tips: [
        {
            required: true,
            message: '请输入对话上限提示语'
        }
    ],
    min_consume_price: [
        {
            required: true,
            message: '请输入最低消费标准'
        }
    ],
    min_consume_tips: [
        {
            required: true,
            message: '请输入限制提示语'
        }
    ]
}
/**
 * 初始化数据
 */
const getData = async () => {
    formData.value = await getChatConfig()
}
getData()
/**
 * 保存数据
 */
const handleSubmit = async () => {
    await setChatConfig(formData.value)
    getData()
}
const handleclick = () => {
    formData.value.chat_logo = formData.value.chat_default
}
const showexample = ref(false)
const handleexample = () => {
    showexample.value = !showexample.value
}
</script>
