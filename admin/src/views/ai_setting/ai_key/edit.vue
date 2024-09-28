<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            :title="popupTitle"
            :async="true"
            width="550px"
            @confirm="handleSubmit"
        >
            <el-form
                class="ls-form"
                ref="formRef"
                :rules="rules"
                :model="formData"
                label-width="98px"
            >
                <el-form-item label="接口类型">
                    <el-select v-if="!isChatModel" class="w-[330px]" v-model="formData.channel">
                        <el-option
                            v-for="(item, key) in aiModelList"
                            :key="key"
                            :label="item"
                            :value="key"
                        />
                    </el-select>
                    <el-select
                        v-else
                        class="w-[330px]"
                        v-model="formData.model_id"
                        @change="changeModel"
                    >
                        <el-option
                            v-for="item in aiModelList"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>
                <template
                    v-if="
                        formData.channel?.includes('xunfei') || formData.channel?.includes('kdxf')
                    "
                >
                    <el-form-item label="APPID" prop="appid">
                        <el-input
                            class="w-[330px]"
                            v-model="formData.appid"
                            placeholder="请输入APPID"
                            clearable
                        />
                    </el-form-item>
                </template>
                <template v-if="formData.channel?.includes('minimax')">
                    <el-form-item label="groupId" prop="appid">
                        <el-input
                            class="w-[330px]"
                            v-model="formData.appid"
                            placeholder="请输入APPID"
                            clearable
                        />
                    </el-form-item>
                </template>
                <el-form-item v-if="getFieldKey.key" :label="getFieldKey.key" prop="key">
                    <el-input
                        class="w-[330px]"
                        v-model="formData.key"
                        :placeholder="`请输入${getFieldKey.key}`"
                        :rows="4"
                        type="textarea"
                        clearable
                    />
                </el-form-item>
                <template
                    v-if="
                        getFieldKey.secret &&
                        (formData.channel?.includes('xunfei') ||
                            formData.channel?.includes('baidu') ||
                            formData.channel?.includes('kdxf') ||
                            formData.channel?.includes('tiangong'))
                    "
                >
                    <el-form-item :label="getFieldKey.secret" prop="secret">
                        <el-input
                            class="w-[330px]"
                            v-model="formData.secret"
                            :placeholder="`请输入${getFieldKey.secret}`"
                            clearable
                        />
                    </el-form-item>
                </template>
                <el-form-item label="备注">
                    <el-input
                        class="w-[330px]"
                        type="textarea"
                        rows="4"
                        v-model="formData.remark"
                        placeholder="请输入备注内容"
                        clearable
                    />
                </el-form-item>
                <el-form-item label="状态">
                    <el-switch v-model="formData.status" :active-value="1" :inactive-value="0" />
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance } from 'element-plus'
import {
    addKeyPool,
    editKeyPool,
    getKeyPoolAiModel,
    getKeyPoolDetail
} from '@/api/ai_setting/ai_key'
import Popup from '@/components/popup/index.vue'
import feedback from '@/utils/feedback'

const emit = defineEmits(['success'])
//表单ref
const formRef = shallowRef<FormInstance>()
//弹框ref
const popupRef = shallowRef<InstanceType<typeof Popup>>()
//弹框标题
const popupTitle = ref('')
const aiModelList = ref<any[]>([])
//表单数据
const formData: any = ref({
    id: '',
    type: '',
    appid: '',
    secret: '',
    channel: '',
    model_id: '',
    key: '',
    remark: '',
    status: 1
})
const isChatModel = computed(() => [1, 2].includes(Number(formData.value.type)))
const changeModel = (id: number) => {
    const current = aiModelList.value.find((item) => item.id === id)
    if (current) {
        formData.value.channel = current.channel
    }
}
const getFieldKey = computed(() => {
    switch (formData.value.channel) {
        case 'hunyuan':
            return {
                key: 'SecretKey',
                secret: 'SecretId'
            }
        case 'minimax':
            return {
                key: '密钥',
                secret: 'SecretId'
            }
        case 'kdxf': {
            if ([4].includes(formData.value.type)) {
                return {
                    key: 'SecretKey'
                }
            } else {
                return {
                    key: 'APIKey',
                    secret: 'APISecret'
                }
            }
        }
        case 'tiangong':
            return {
                key: 'APPKey',
                secret: 'APPSecret'
            }
        default:
            return {
                key: 'APIKey',
                secret: 'APISecret'
            }
    }
})
//表单校验规则
const rules = {
    key: [
        {
            required: true,
            message: '请输入APIKey',
            trigger: ['blur']
        }
    ],
    appid: [
        {
            required: true,
            message: '请输入APPID'
        }
    ],
    secret: [
        {
            required: true,
            message: '请输入APISecret'
        }
    ]
}

//提交表单
const handleSubmit = async () => {
    try {
        await formRef.value?.validate()
        if (formData.value.id == '') await addKeyPool(formData.value)
        else if (formData.value.id != '') await editKeyPool(formData.value)
        feedback.msgSuccess('操作成功')
        emit('success')
        popupRef.value?.close()
    } catch (error) {
        return error
    }
}

//打开弹框
const open = async (type: number, mode: string, value: any) => {
    //初始化数据
    if (mode == 'add') {
        formData.value = {
            id: '',
            type,
            channel: '',
            model_id: '',
            key: '',
            appid: '',
            remark: '',
            status: 1
        }
        popupTitle.value = '新增密钥'
    } else if (mode == 'edit') {
        const data = await getKeyPoolDetail({ id: value.id })
        Object.keys(data).map((item) => {
            formData.value[item] = data[item] ?? ''
        })
        formData.value.type = type
        popupTitle.value = '编辑密钥'
    }
    popupRef.value?.open()
    getAiModelList(type)
}

const getAiModelList = async (type: number) => {
    try {
        const data = await getKeyPoolAiModel({
            type: type
        })
        aiModelList.value = data
    } catch (error) {
        console.log(error)
    }
}

defineExpose({
    open
})
</script>
