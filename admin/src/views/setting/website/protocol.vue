<template>
    <div>

        <div class="xl:flex">
            <el-card class="!border-none flex-1 xl:mr-4 mb-4" shadow="never">
                <template #header>
                    <span class="font-medium">服务协议</span>
                </template>
                <el-form :model="formData" label-width="80px">
                    <el-form-item label="协议名称">
                        <el-input v-model="formData.service_title"/>
                    </el-form-item>
                </el-form>

                <editor class="mb-10" v-model="formData.service_content" height="500"/>
            </el-card>
            <el-card class="!border-none flex-1 mb-4" shadow="never">
                <template #header>
                    <span class="font-medium">隐私协议</span>
                </template>
                <el-form :model="formData" label-width="80px">
                    <el-form-item label="协议名称">
                        <el-input v-model="formData.privacy_title"/>
                    </el-form-item>
                </el-form>

                <editor class="mb-10" v-model="formData.privacy_content" height="500"/>
            </el-card>
        </div>
        <div class="xl:flex">
            <el-card class="!border-none flex-1 xl:mr-4 mb-4" shadow="never">
                <template #header>
                    <span class="font-medium">支付协议</span>
                </template>
                <el-form :model="formData" label-width="80px">
                    <el-form-item label="协议名称">
                        <el-input v-model="formData.payment_title"/>
                    </el-form-item>
                </el-form>

                <editor class="mb-10" v-model="formData.payment_content" height="500"/>
            </el-card>

            <el-card class="!border-none flex-1 xl:mr-4 mb-4" shadow="never">
                <template #header>
                    <span class="font-medium">用户分销协议</span>
                    <!-- <el-switch
                        class="ml-2"
                        :active-value="1"
                        :inactive-value="0"
                        v-model="formData.pay_status"
                    ></el-switch>
                    <span class="ml-2">关闭时，在开通会员页将不显示</span>
                    <span class="ml-2 text-[#4073FA]">查看</span> -->
                </template>
                <el-form :model="formData" label-width="80px">
                    <el-form-item label="协议名称">
                        <el-input v-model="formData.distribution_title" />
                    </el-form-item>
                </el-form>

                <editor class="mb-10" v-model="formData.distribution_content" height="500"></editor>
            </el-card>
        </div>

        <footer-btns v-perms="['setting.website/setAgreement']">
            <el-button type="primary" @click="handleProtocolEdit">保存</el-button>
        </footer-btns>
    </div>
</template>

<script setup lang="ts" naem="webProtocol">
import {getProtocol, setProtocol} from '@/api/setting/website'

interface formDataObj {
    service_title: string
    service_content: string
    privacy_title: string
    privacy_content: string
    payment_title: string
    payment_content: string
    distribution_title: string
    distribution_content: string
}

const formData = ref<formDataObj>({
    payment_title: '',
    payment_content: '',
    service_title: '',
    service_content: '',
    privacy_title: '',
    privacy_content: '',
    distribution_title: '',
    distribution_content: ''
})
const protocolGet = async () => {
    formData.value = await getProtocol()
}

const handleProtocolEdit = async (): Promise<void> => {
    await setProtocol({...formData.value})
    protocolGet()
}
protocolGet()
</script>
