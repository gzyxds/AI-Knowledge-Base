<template>
    <Popup
        ref="popRef"
        title="创建用户"
        width="500px"
        async
        @confirm="submit"
        @close="$emit('close')"
    >
        <div>
            <el-form
                label-width="90px"
                :model="formData"
                ref="formRef"
                :rules="rules"
                @submit.prevent
            >
                <el-form-item label="用户头像" prop="avatar">
                    <MaterialPicker v-model="formData.avatar" />
                </el-form-item>
                <el-form-item label="用户昵称" prop="nickname">
                    <el-input placeholder="请输入用户昵称" v-model="formData.nickname" />
                </el-form-item>
                <el-form-item label="手机号码" prop="mobile">
                    <el-input placeholder="请输入手机号码" v-model="formData.mobile" />
                </el-form-item>
                <el-form-item label="邮箱" prop="email">
                    <el-input placeholder="请输入邮箱" v-model="formData.email" />
                </el-form-item>
                <el-form-item label="真实名称" prop="real_name">
                    <el-input placeholder="请输入真实名称" v-model="formData.real_name" />
                </el-form-item>
                <el-form-item label="用户分组">
                    <el-select v-model="formData.group_ids" multiple>
                        <el-option
                            v-for="(item, index) in optionsData.dataList"
                            :key="index"
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="登录密码" prop="password">
                    <el-input
                        placeholder="请输入登录密码"
                        type="password"
                        v-model="formData.password"
                        show-password
                    />
                </el-form-item>
                <el-form-item label="确认密码" prop="password_confirm">
                    <el-input
                        placeholder="请输入确认密码"
                        type="password"
                        show-password
                        v-model="formData.password_confirm"
                    />
                </el-form-item>
            </el-form>
        </div>
    </Popup>
</template>

<script setup lang="ts">
import { addUser, userGroupingList } from '@/api/consumer'
import { useDictOptions } from '@/hooks/useDictOptions'
import feedback from '@/utils/feedback'
import type { FormInstance, FormRules } from 'element-plus'

interface IFormData {
    avatar: string
    nickname: string
    mobile: string
    email: string
    real_name: string
    password: string
    password_confirm: string
    group_ids: number | string
}

const emit = defineEmits(['close'])

//弹框ref
const popRef = shallowRef()
//表单ref
const formRef = shallowRef<FormInstance>()

//表单数据
const formData: IFormData = reactive({
    avatar: '',
    nickname: '',
    mobile: '',
    email: '',
    real_name: '',
    password: '',
    password_confirm: '',
    group_ids: ''
})

const { optionsData, refresh } = useDictOptions<{ dataList: any }>({
    dataList: {
        api: userGroupingList
    }
})

const rules = reactive<FormRules<IFormData>>({
    avatar: [
        {
            required: true,
            message: '请选择头像',
            trigger: 'change'
        }
    ],
    nickname: [
        {
            required: true,
            message: '请填写昵称',
            trigger: 'change'
        }
    ],
    // mobile: [
    //     {
    //         required: true,
    //         message: '请填写手机号码',
    //         trigger: 'change'
    //     }
    // ],
    // email: [
    //     {
    //         required: true,
    //         message: '请填写邮箱号',
    //         trigger: 'change'
    //     }
    // ],
    // real_name: [
    //     {
    //         required: true,
    //         message: '请填写真实名称',
    //         trigger: 'change'
    //     }
    // ],
    password: [
        {
            required: true,
            message: '请填写密码',
            trigger: 'change'
        }
    ],
    password_confirm: [
        {
            required: true,
            message: '请填写确认密码',
            trigger: 'change'
        }
    ]
})

//提交表单
const submit = async () => {
    await formRef.value?.validate()
    await addUser({ ...formData })
    feedback.msgSuccess('新增成功！')
    popRef.value.close()
}

//打开弹框
const open = () => {
    popRef.value.open()
    refresh()
}

defineExpose({ open })
</script>

<style scoped lang="scss"></style>
