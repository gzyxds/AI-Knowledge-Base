<template>
    <div>
        <el-card shadow="never" class="!border-none">
            <el-tabs v-model="activeTab">
                <el-tab-pane label="DALLE基础配置" name="configs" lazy>
                    <el-form
                        label-width="150px"
                        ref="sdConfigsRef"
                        :model="configs"
                        :rules="sdConfigsRules"
                    >
                        <el-form-item label="功能开启" prop="status">
                            <div>
                                <el-switch
                                    v-model="configs.status"
                                    :active-value="'1'"
                                    :inactive-value="'0'"
                                ></el-switch>
                                <div class="form-tips !text-[14px]">
                                    默认关闭；功能关闭后，用户将无法访问该功能
                                </div>
                            </div>
                        </el-form-item>

                        <el-form-item label="API域名" prop="proxy_url">
                            <div>
                                <div class="flex">
                                    <el-input
                                        placeholder="请输入自定义API域名"
                                        class="w-[400px]"
                                        v-model="configs.proxy_url"
                                    />
                                </div>

                                <div class="form-tips !text-[14px]">
                                    该项为必填项，不填入则无法使用
                                </div>
                            </div>
                        </el-form-item>

                        <el-form-item label="翻译功能" prop="translate_switch">
                            <div>
                                <el-switch
                                    v-model="configs.translate_switch"
                                    :active-value="'1'"
                                    :inactive-value="'0'"
                                ></el-switch>
                                <div class="form-tips !text-[14px]">
                                    默认关闭；功能关闭后，前台提示词输入框将不显示翻译按钮，也不会自动翻译提示词内容
                                </div>
                            </div>
                        </el-form-item>

                        <el-form-item label="翻译接口" >
                            <div>
                                <el-select
                                    v-model="configs.translate_api"
                                    class="w-[400px]"
                                    @change="configs.translate_api_model = getTranslateApiList[0]?.name || ''"
                                >
                                    <el-option
                                        v-for="item in configs.translate_api_list"
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
                            v-if="configs.translate_api !== TranslateApi.BAIDU"
                            label="选择子模型"
                        >
                            <el-select v-model="configs.translate_api_model" class="w-[400px]">
                                <el-option
                                    v-for="item in getTranslateApiList"
                                    :value="item.name"
                                    :label="item.alias"
                                    :key="item.name"
                                />
                            </el-select>
                        </el-form-item>

                        <el-form-item
                            v-if="configs.translate_api === TranslateApi.BAIDU"
                            label="翻译接口APPID"
                            prop="appid"
                        >
                            <div>
                                <div class="flex">
                                    <el-input
                                        placeholder="请输入APPID"
                                        class="w-[400px]"
                                        v-model="configs.appid"
                                    />
                                </div>

                                <div
                                    class="form-tips !text-[14px] flex items-center gap-1"
                                >
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
                            v-if="configs.translate_api === TranslateApi.BAIDU"
                            label="翻译接口密钥"
                            prop="secret_key"
                        >
                            <div>
                                <div class="flex">
                                    <el-input
                                        placeholder="请输入密钥"
                                        class="w-[400px]"
                                        v-model="configs.secret_key"
                                    />
                                </div>
                            </div>
                        </el-form-item>

                        <el-form-item
                            v-if="configs.translate_api !== TranslateApi.BAIDU"
                            label="翻译指令"
                            prop="translate_prompt"
                        >
                            <div class="w-[400px]">
                                <el-input
                                    v-model="configs.translate_prompt"
                                    :autosize="{ minRows: 7, maxRows: 7 }"
                                    type="textarea"
                                    show-word-limit
                                    placeholder="请输入审核全局指令"
                                />
                                <div class="form-tips !text-[14px]">
                                    {prompt}是内置变量，表示用户输入的描述词
                                    <span class="ml-3 text-primary cursor-pointer" v-copy="'我会用任何语言和你交流，你只需将我的话翻译为英语，不要解释我的话或者回复其他信息，请立刻将我的话翻译返回，我的话是:{prompt}'">
                                        复制示例指令
                                    </span>
                                </div>
                            </div>
                        </el-form-item>
                    </el-form>
                </el-tab-pane>
                <el-tab-pane label="应用计费" name="charging" lazy>
                    <el-form
                        label-width="120px"
                        ref="sdChargingRef"
                        :model="charging"
                        :rules="sdChargingRules"
                    >
                        <el-form-item label="应用名称" prop="app_name">
                            <el-input
                                placeholder="DALLE绘画"
                                disabled
                                class="w-[400px]"
                                v-model="charging.app_name"
                            />
                        </el-form-item>
                        <el-form-item label="自定义名称" prop="diy_name">
                            <div>
                                <div class="flex">
                                    <el-input
                                        placeholder="请输入自定义名称"
                                        class="w-[400px]"
                                        v-model="charging.diy_name"
                                    />
                                </div>
                                <div class="form-tips !text-[14px]">
                                    不填写前台则默认显示模型名称
                                </div>
                            </div>
                        </el-form-item>
                        <el-form-item label="消耗电力值" prop="power">
                            <div>
                                <el-input-number
                                    v-model="charging.power"
                                    :min="0"
                                />
                                <div class="form-tips !text-[14px]">
                                    填写0则表示不消耗电力值
                                </div>
                            </div>
                        </el-form-item>
                    </el-form>
                </el-tab-pane>
            </el-tabs>
        </el-card>
        <footer-btns v-perms="['setting.ai.draw/save']">
            <el-button type="primary" @click="handleSave">保存</el-button>
        </footer-btns>
    </div>
</template>
<script setup lang="ts" name="aiDrawModel">
import { getDeawConfig, setDeawConfig } from '@/api/ai_draw/draw_setting';
import type { FormInstance, FormRules } from 'element-plus';

interface SdConfig {
    status?: string;
    translate_api?: string;
    translate_api_model: string
    translate_api_list?: {
        model: string;
        model_name: string;
        prompt: string;
    }[];
    translate_prompt?: string;
    time_out?: string;
    proxy_url?: string;
    secret_key?: string;
    appid?: string;
    file_size?: number;
    translate_switch?: string;
    model?: string;
}

interface SdCharging {
    app_name?: string;
    diy_name?: string;
    power?: number;
}

enum TranslateApi {
    BAIDU = 'baidu',
    OPENAI = 'openai',
}

const activeTab = ref('configs');
const configs = ref<SdConfig>({
    status: '0',
    translate_api: '',
    translate_api_model: '',
    translate_api_list: [],
    translate_prompt: '',
    time_out: '',
    proxy_url: '',
    secret_key: '',
    appid: '',
    file_size: 10,
    translate_switch: '0',
    model: 'dalle3'
});
const charging = ref<SdCharging>({
    app_name: '',
    diy_name: '',
    power: 0,
});
const configData = ref<SdConfig & SdCharging>({});
const sdConfigsRef = ref<FormInstance>();
const sdChargingRef = ref<FormInstance>();

const sdConfigsRules = reactive<FormRules<SdConfig & SdCharging>>({
    proxy_url: [
        {
            required: true,
            message: '请填写API域名',
            trigger: ['blur', 'change'],
        },
    ],
    file_size: [
        {
            required: true,
            message: '请填写图生图参考图大小限制',
            trigger: ['blur', 'change'],
        },
    ],
});

const sdChargingRules = reactive<FormRules<SdConfig | SdCharging>>({
    power: [
        {
            required: true,
            message: '请填写消耗电力值',
            trigger: ['blur', 'change'],
        },
    ],
});

const getTranslateApiList = computed(() => {
    return configs.value.translate_api_list?.find(item => item.model === configs.value.translate_api)?.model_list || []
})

const getData = async () => {
    const res = await getDeawConfig({
        model: 'dalle3'
    });
    Object.keys(configs.value).map((key) => {
        configs.value[key as keyof SdConfig] = res[key] || '';
    });
    Object.keys(charging.value).map((key) => {
        charging.value[key as keyof SdCharging] = res[key] || '';
    });
    configData.value = res;
};

const handleSave = async () => {
    if (activeTab.value === 'configs') {
        await sdConfigsRef.value?.validate();
        await setDeawConfig({
            ...configData.value,
            ...configs.value,
          model: 'dalle3'
        });
    } else {
        await sdChargingRef.value?.validate();
        await setDeawConfig({
            ...configData.value,
            ...charging.value,
          model: 'dalle3'
        });
    }
    await getData();
};

onMounted(() => {
    getData();
});
</script>
