<!-- 网站信息 -->
<template>
    <div class="website-information">
        <el-form ref="formRef" :rules="rules" class="ls-form" :model="formData" label-width="120px">
            <el-card shadow="never" class="!border-none">
                <div class="text-xl font-medium mb-[20px]">形象配置</div>
                <el-form-item label="AI接口" required>
                    <div>
                        <el-radio model-value>{{ getPlatformInfo?.name }} </el-radio>
                        <div class="form-tips" v-if="getPlatformInfo?.href">
                            申请地址：
                            <a :href="getPlatformInfo?.href" target="_blank">
                                {{ getPlatformInfo?.href }}
                            </a>
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label=" 存储空间名称" prop="ali_bucket">
                    <div class="w-[400px]">
                        <el-input
                            v-model="formData.ali_bucket"
                            placeholder="请输入存储空间名称(Bucket)"
                            clearable
                        />
                    </div>
                </el-form-item>
                <el-form-item label="AccessKeyID" prop="access_key_id">
                    <div class="w-[400px]">
                        <el-input
                            v-model="formData.access_key_id"
                            placeholder="请输入AccessKeyID"
                            clearable
                        />
                    </div>
                </el-form-item>
                <el-form-item label="AccessKeySecret" prop="access_key_secret">
                    <div class="w-[400px]">
                        <el-input
                            v-model="formData.access_key_secret"
                            placeholder="请输入AccessKeySecret"
                            clearable
                        />
                    </div>
                </el-form-item>
                <el-form-item label="对象存储(OSS)" prop="ali_oss">
                    <div class="w-[400px]">
                        <div class="mb-[20px]">
                            <el-select class="w-full" v-model="formData.regionid">
                                <el-option
                                    label="华北2（北京）"
                                    value="ice.cn-beijing.aliyuncs.com"
                                />
                                <el-option
                                    label="华东2（上海）"
                                    value="ice.cn-shanghai.aliyuncs.com"
                                />
                                <el-option
                                    label="华南1（深圳）"
                                    value="ice.cn-shenzhen.aliyuncs.com"
                                />
                            </el-select>
                        </div>
                        <el-input
                            v-model="formData.ali_oss"
                            placeholder="请输入对象存储 (OSS)"
                            clearable
                        />
                        <div class="form-tips">用于存储形象合成的视频</div>
                    </div>
                </el-form-item>
            </el-card>
        </el-form>
        <footer-btns>
            <el-button type="primary" @click="handleSubmit">保存</el-button>
        </footer-btns>
    </div>
</template>

<script lang="ts" setup name="digitalHumanSetting">
import { getAvatarConfig, setAvatarConfig } from '@/api/digital_human/manage'
import type { FormInstance } from 'element-plus'

enum PlatformEnum {
    ALIYUN = 'aliyun'
}
const formRef = ref<FormInstance>()
const platform = ref(PlatformEnum.ALIYUN)
// 表单数据
const formData = reactive({
    title: '',
    intro: '',
    access_key_id: '',
    access_key_secret: '',
    vod_oss: '',
    ali_oss: '',
    ali_bucket: '',
    regionid: 'ice.cn-shanghai.aliyuncs.com'
})

const platformArr = [
    {
        name: '阿里云',
        type: PlatformEnum.ALIYUN,
        href: `https://www.aliyun.com/`
    }
]

const getPlatformInfo = computed(() => {
    return platformArr.find((item) => item.type == platform.value)
})

// 表单验证
const rules = {
    access_key_id: [
        {
            required: true,
            message: '请输入AccessKeyID'
        }
    ],
    access_key_secret: [
        {
            required: true,
            message: '请输入AccessKeySecret'
        }
    ],
    vod_oss: [
        {
            required: true,
            message: '请输入视频点播存储 (VOD)'
        }
    ],
    ali_oss: [
        {
            required: true,
            message: '请输入对象存储 (OSS)'
        }
    ],
    ali_bucket: [
        {
            required: true,
            message: '请输入存储空间名称'
        }
    ]
}

// 获取备案信息
const getData = async () => {
    const data = await getAvatarConfig()
    for (const key in formData) {
        //@ts-ignore
        formData[key] = data[key]
    }
}

// 设置备案信息
const handleSubmit = async () => {
    await formRef.value?.validate()
    await setAvatarConfig(formData)
    getData()
}

getData()
</script>

<style lang="scss" scoped></style>
