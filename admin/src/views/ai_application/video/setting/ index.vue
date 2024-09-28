<template>
    <div>
        <el-form
            class="mt-4"
            ref="formRef"
            :model="formData"
            label-width="120px"
            :rules="formRules"
        >
            <el-card shadow="never" class="!border-none">
                <div class="text-xl font-medium mb-[20px]">AI设置</div>
                <el-form-item label="AI接口" required :show-message="false">
                    <div>
                        <div>
                            <el-radio-group v-model="currentKey">
                                <el-radio
                                    :label="item.channel"
                                    v-for="(item, key) in formData.video_models"
                                    :key="key"
                                >
                                    {{ item.name }}
                                </el-radio>
                            </el-radio-group>
                        </div>
                        <div
                            class="form-tips"
                            v-if="
                                formData.video_models[currentKey]?.tips ||
                                formData.video_models[currentKey]?.website
                            "
                        >
                            <template v-if="formData.video_models[currentKey].tips">
                                {{ formData.video_models[currentKey].tips }}
                            </template>
                            <a
                                v-if="formData.video_models[currentKey].website"
                                class="text-primary"
                                target="_blank"
                                :href="formData.video_models[currentKey].website"
                            >
                                前往开通
                            </a>
                        </div>
                    </div>
                </el-form-item>
                <template v-if="currentKey">
                    <el-form-item
                        v-if="currentKey === 'openai_hk'"
                        label="选择版本"
                        :prop="`video_models.${currentKey}.version`"
                    >
                        <div>
                            <el-radio-group v-model="formData.video_models[currentKey].version">
                                <!-- <el-radio label="relax"> relax版本 </el-radio> -->
                                <el-radio label="pro"> pro版本 </el-radio>
                            </el-radio-group>
                            <div class="form-tips">
                                relax版本费用较低，视频带水印，需要进入队列等候生成；pro版本则费用较高，视频上可去除水印，无需队列排队
                            </div>
                        </div>
                    </el-form-item>

                    <el-form-item label="消耗电力值" :prop="`video_models.${currentKey}.price`">
                        <div class="w-[460px]">
                            <el-input
                                v-model="formData.video_models[currentKey].price"
                                placeholder="请输入消耗电力值数量"
                                type="number"
                            />
                            <div class="form-tips">填写0则表示不消耗电力值</div>
                        </div>
                    </el-form-item>
                </template>
            </el-card>
            <el-card shadow="never" class="!border-none mt-4">
                <div class="text-xl font-medium mb-[20px]">翻译配置</div>
                <el-form-item label="翻译功能">
                    <div>
                        <el-switch
                            v-model="formData.translate.status"
                            :active-value="'1'"
                            :inactive-value="'0'"
                        />
                        <div class="form-tips !text-[14px]">
                            默认关闭；功能关闭后，前台提示词输入框将不显示翻译按钮，也不会自动翻译提示词内容
                        </div>
                    </div>
                </el-form-item>

                <el-form-item label="翻译接口">
                    <div>
                        <el-select
                            v-model="formData.translate.api"
                            class="w-[400px]"
                            @change="
                                formData.translate.api_model = getTranslateApiList[0]?.name || ''
                            "
                        >
                            <el-option
                                v-for="item in formData.translate.api_list"
                                :value="item.model"
                                :label="item.model_name"
                                :key="item.model"
                            />
                        </el-select>
                        <div class="form-tips">
                            选择翻译接口后，需前往【key池管理】添加相应的Key才可正常使用
                        </div>
                    </div>
                </el-form-item>

                <el-form-item
                    v-if="formData.translate.api !== TranslateApi.BAIDU"
                    label="选择子模型"
                >
                    <el-select v-model="formData.translate.api_model" class="w-[400px]">
                        <el-option
                            v-for="item in getTranslateApiList"
                            :value="item.name"
                            :label="item.alias"
                            :key="item.name"
                        />
                    </el-select>
                </el-form-item>

                <el-form-item
                    v-if="formData.translate.api === TranslateApi.BAIDU"
                    label="翻译接口APPID"
                >
                    <div>
                        <div class="flex">
                            <el-input
                                placeholder="请输入APPID"
                                class="w-[400px]"
                                v-model="formData.translate.baidu_appid"
                            />
                        </div>

                        <div class="form-tips !text-[14px] flex items-center gap-1">
                            <div>申请链接：</div>
                            <el-link
                                href="https://fanyi-api.baidu.com/doc/11"
                                type="primary"
                                target="_blank"
                                >点击跳转</el-link
                            >
                        </div>
                    </div>
                </el-form-item>

                <el-form-item
                    v-if="formData.translate.api === TranslateApi.BAIDU"
                    label="翻译接口密钥"
                >
                    <div>
                        <div class="flex">
                            <el-input
                                placeholder="请输入密钥"
                                class="w-[400px]"
                                v-model="formData.translate.baidu_secret_key"
                            />
                        </div>
                    </div>
                </el-form-item>

                <el-form-item v-if="formData.translate.api !== TranslateApi.BAIDU" label="翻译指令">
                    <div class="w-[400px]">
                        <el-input
                            v-model="formData.translate.prompt"
                            :autosize="{ minRows: 7, maxRows: 7 }"
                            type="textarea"
                            show-word-limit
                            placeholder="请输入审核全局指令"
                        />
                        <div class="form-tips !text-[14px]">
                            {prompt}是内置变量，表示用户输入的描述词
                            <span
                                class="ml-3 text-primary cursor-pointer"
                                v-copy="
                                    '我会用任何语言和你交流，你只需将我的话翻译为英语，不要解释我的话或者回复其他信息，请立刻将我的话翻译返回，我的话是:{prompt}'
                                "
                            >
                                复制示例指令
                            </span>
                        </div>
                    </div>
                </el-form-item>
            </el-card>
            <el-card shadow="never" class="!border-none mt-4">
                <div class="text-xl font-medium mb-[20px]">示例设置</div>
                <el-form-item label="是否开启">
                    <div>
                        <el-switch
                            v-model="formData.video_example.status"
                            :active-value="1"
                            :inactive-value="0"
                        />
                        <div class="form-tips">开启的话，前台显示示例按钮</div>
                    </div>
                </el-form-item>
                <el-form-item label="示例内容">
                    <div class="w-[460px]">
                        <del-wrap
                            class="mb-4 block"
                            v-for="(item, index) in formData.video_example.data"
                            :key="index"
                            @close="formData.video_example.data.splice(index, 1)"
                        >
                            <el-input
                                v-model="formData.video_example.data[index]"
                                type="textarea"
                                :rows="8"
                                resize="none"
                            />
                        </del-wrap>
                        <el-button type="primary" @click="formData.video_example.data.push('')">
                            添加示例
                        </el-button>
                    </div>
                </el-form-item>
            </el-card>
        </el-form>
        <footer-btns>
            <el-button type="primary" @click="handleSave">保存</el-button>
        </footer-btns>
    </div>
</template>

<script setup lang="ts" name="videoSetting">
import { getVideoConfig, putVideoConfig } from '@/api/ai_application/video'

enum TranslateApi {
    BAIDU = 'baidu',
    OPENAI = 'openai'
}
const formRef = shallowRef()
const formRules = computed(() => {
    return {
        [`video_models.${currentKey.value}.price`]: [
            {
                required: true,
                message: '请输入消耗电力值'
            }
        ]
    }
})
const formData = ref({
    video_models: {} as any,
    video_example: {
        data: [] as string[],
        status: 0
    },
    translate: {
        api_list: []
    } as any
})

const getData = async () => {
    formData.value = await getVideoConfig()
}

const currentKey = computed({
    get() {
        const select: any = Object.values(formData.value.video_models).find(
            (item: any) => item.checked
        )
        return select?.channel || ''
    },
    set(value) {
        Object.keys(formData.value.video_models).forEach((key) => {
            formData.value.video_models[key].checked = false
            if (key == value) {
                formData.value.video_models[key].checked = true
            }
        })
    }
})
const getTranslateApiList = computed(() => {
    return (
        formData.value.translate.api_list?.find(
            (item: any) => item.model === formData.value.translate.api
        )?.model_list || []
    )
})

const handleSave = async () => {
    await formRef.value?.validate()
    await putVideoConfig(formData.value)
}
onMounted(() => {
    getData()
})
</script>
