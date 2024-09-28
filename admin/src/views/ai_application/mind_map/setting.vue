<!-- 网站信息 -->
<template>
    <div class="website-information">
        <el-form ref="formRef" :rules="rules" class="ls-form" :model="formData" label-width="120px">
            <el-card shadow="never" class="!border-none">
                <div class="text-xl font-medium mb-[20px]">应用设置</div>
                <el-form-item label="AI接口" prop="channel_id">
                    <div>
                        <el-radio-group v-model="formData.channel_id">
                            <el-radio
                                v-for="(item, index) in chatList"
                                :label="item.id"
                                :key="index"
                                @change="modelChange"
                            >
                                {{ item.name }}
                            </el-radio>
                        </el-radio-group>
                    </div>
                </el-form-item>
                <el-form-item label="选择模型" prop="model_id">
                    <div class="w-[460px]">
                        <el-select
                            class="w-full"
                            v-model="formData.model_id"
                            placeholder=""
                            clearable
                        >
                            <el-option
                                v-for="item in currentModel"
                                :value="item.id"
                                :label="item.alias"
                                :key="item.id"
                            />
                        </el-select>
                        <div class="form-tips">选择生成思维导图的模型</div>
                    </div>
                </el-form-item>
                <el-form-item label="消耗电力值" prop="balance">
                    <div class="w-[460px]">
                        <el-input
                            v-model="formData.balance"
                            placeholder="请输入额外消耗电力值"
                            type="number"
                        />
                        <div class="form-tips">不能为空，填写0则表示不额外扣费</div>
                    </div>
                </el-form-item>
            </el-card>
            <el-card shadow="never" class="!border-none mt-4">
                <div class="text-xl font-medium mb-[20px]">内置提示词</div>
                <el-form-item label="提示词设置" prop="cue_word">
                    <div class="w-[460px]">
                        <el-input
                            v-model="formData.cue_word"
                            placeholder="请输入"
                            type="textarea"
                            :rows="6"
                            resize="none"
                        />
                        <div class="form-tips whitespace-pre-wrap">
                            <el-button
                                type="primary"
                                link
                                v-copy="
                                    `请按我接下来说的主题帮我制作一份思维导图，列出主分支内容和子分支内容，你需按以下格式返回数据：&quot;
# {标题}
## {子标题}
- {内容} 
...&quot;，不要返回其他提示信息或解释，我的主题是：{prompt}`
                                "
                            >
                                复制示例指令
                            </el-button>
                            如果示例指令效果不明显，或者效果不好，可自行调整提示词
                        </div>
                    </div>
                </el-form-item>
            </el-card>
        </el-form>
        <footer-btns v-perms="['setting.Mindmap/setConfig']">
            <el-button type="primary" @click="handleSubmit">保存</el-button>
        </footer-btns>
    </div>
</template>

<script lang="ts" setup name="mindMapSetting">
import { getMindMapConfig, setMindMapConfig } from '@/api/ai_application/mind_map'

import type { FormInstance } from 'element-plus'
const formRef = ref<FormInstance>()

// 表单数据
const formData = reactive({
    channel_id: '',
    model_id: '',
    cue_word: '',
    balance: ''
})
const chatList = ref<any[]>([])

// 表单验证
const rules = {
    balance: [
        {
            required: true,
            message: '请输入消耗电力值'
        }
    ],
    channel_id: [
        {
            required: true,
            message: '请选择AI接口'
        }
    ],
    model_id: [
        {
            required: true,
            message: '请选择模型'
        }
    ]
}
const modelChange = () => {
    const [item] = currentModel.value
    if (item) {
        formData.model_id = item.id
    }
}
const currentModel = computed(() => {
    const current = chatList.value.find((item) => item.id === formData.channel_id)
    return current?.models_lists || []
})
// 获取备案信息
const getData = async () => {
    const data = await getMindMapConfig()
    for (const key in formData) {
        //@ts-ignore
        formData[key] = data[key]
    }
    chatList.value = data.chat_list
}

// 设置备案信息
const handleSubmit = async () => {
    await formRef.value?.validate()
    await setMindMapConfig(formData)
    getData()
}

getData()
</script>

<style lang="scss" scoped></style>
