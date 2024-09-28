<!-- 网站信息 -->
<template>
    <div class="website-information">
        <el-form
            ref="formRef"
            :rules="rules"
            class="ls-form"
            :model="formData"
            scroll-to-error
            :scroll-into-view-options="{
                behavior: 'smooth',
                block: 'center',
            }"
            label-width="120px">
            <el-card shadow="never" class="!border-none">
                <div class="text-xl font-medium mb-[20px]">后台设置</div>
                <el-form-item label="网站名称" prop="name">
                    <div class="w-80">
                        <el-input
                            v-model.trim="formData.name"
                            placeholder="请输入网站名称"
                            maxlength="30"
                            show-word-limit />
                    </div>
                </el-form-item>
                <el-form-item label="网站图标" prop="web_favicon" required>
                    <div>
                        <material-picker
                            v-model="formData.web_favicon"
                            :limit="1" />
                        <div class="form-tips">
                            建议尺寸：100*100像素，支持jpg，jpeg，png格式
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="网站LOGO" prop="web_logo" required>
                    <div>
                        <material-picker
                            v-model.trim="formData.web_logo"
                            :limit="1" />
                        <div class="form-tips">
                            建议尺寸：100*100像素，支持jpg，jpeg，png格式
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="登录页广告图" prop="login_image" required>
                    <div>
                        <material-picker
                            v-model.trim="formData.login_image"
                            :limit="1" />
                        <div class="form-tips">
                            建议尺寸：100*100像素，支持jpg，jpeg，png格式
                        </div>
                    </div>
                </el-form-item>
            </el-card>
            <!-- <el-card shadow="never" class="!border-none mt-4">
                <div class="text-xl font-medium mb-[20px]">前台设置</div>
                <el-form-item label="前台名称" prop="shop_name">
                    <div class="w-80">
                        <el-input
                            v-model.trim="formData.shop_name"
                            placeholder="请输入前台名称"
                            maxlength="30"
                            show-word-limit
                        />
                    </div>
                </el-form-item>
                <el-form-item label="前台LOGO" prop="shop_logo">
                    <div>
                        <material-picker v-model="formData.shop_logo" :limit="1" />
                        <div class="form-tips">建议尺寸：100*100px，支持jpg，jpeg，png格式</div>
                    </div>
                </el-form-item>
            </el-card> -->
            <el-card shadow="never" class="!border-none mt-4">
                <div class="text-xl font-medium mb-[20px]">PC端设置</div>
                <el-form-item label="PC端LOGO" prop="pc_logo">
                    <div>
                        <material-picker
                            v-model="formData.pc_logo"
                            :limit="1" />
                        <div class="form-tips">
                            建议尺寸：120*28px，支持jpg，jpeg，png格式
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="网站名称" prop="pc_name">
                    <div class="w-80">
                        <el-input
                            v-model.trim="formData.pc_name"
                            placeholder="请输入PC端网站名称"
                            maxlength="30"
                            show-word-limit />
                        <div class="form-tips">显示于PC左上角名称</div>
                    </div>
                </el-form-item>
                <el-form-item label="网站标题" prop="pc_title">
                    <div class="w-80">
                        <el-input
                            v-model.trim="formData.pc_title"
                            placeholder="请输入PC端网站标题"
                            maxlength="30"
                            show-word-limit />
                    </div>
                </el-form-item>

                <el-form-item label="网站图标" prop="pc_ico">
                    <div>
                        <material-picker v-model="formData.pc_ico" :limit="1" />
                        <div class="form-tips">
                            建议尺寸：100*100像素，支持jpg，jpeg，png格式
                        </div>
                    </div>
                </el-form-item>

                <el-form-item label="登录封面" prop="pc_login_image">
                    <div>
                        <material-picker
                            v-model="formData.pc_login_image"
                            :limit="1" />
                        <div class="flex items-center">
                            <div class="form-tips">建议尺寸：320*500px</div>
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="网站描述" prop="pc_desc">
                    <div class="w-80">
                        <el-input
                            v-model.trim="formData.pc_desc"
                            placeholder="请输入PC端网站描述"
                            type="textarea"
                            :rows="4" />
                    </div>
                </el-form-item>
                <el-form-item label="网站关键词" prop="pc_key">
                    <div class="w-80">
                        <el-input
                            v-model.trim="formData.pc_key"
                            placeholder="请输入PC端网站关键词"
                            type="textarea"
                            :rows="4" />
                    </div>
                </el-form-item>
            </el-card>
            <el-card shadow="never" class="!border-none mt-4">
                <div class="text-xl font-medium mb-[20px]">联系方式</div>
                <el-form-item label="联系人姓名" prop="contacts">
                    <div class="w-80">
                        <el-input
                            v-model.trim="formData.contacts"
                            placeholder="请输入联系人姓名" />
                    </div>
                </el-form-item>
                <el-form-item label="手机号码" prop="mobile">
                    <div class="w-80">
                        <el-input
                            v-model.trim="formData.mobile"
                            placeholder="请输入联系人姓名" />
                        <div class="form-tips whitespace-nowrap">
                            平台联系人手机号：有订单产生时，可发送短信至联系人手机
                        </div>
                    </div>
                </el-form-item>
            </el-card>
        </el-form>
        <footer-btns v-perms="['setting.website/setWebsite']">
            <el-button type="primary" @click="handleSubmit">保存</el-button>
        </footer-btns>
    </div>
</template>

<script lang="ts" setup name="webInformation">
import { getWebsite, setWebsite } from "@/api/setting/website";
import useAppStore from "@/stores/modules/app";
import type { FormInstance } from "element-plus";
const formRef = ref<FormInstance>();

const appStore = useAppStore();
// 表单数据
const formData = reactive({
    name: "", // 网站名称
    web_favicon: "", // 网站图标
    web_logo: "", // 网站logo
    login_image: "", // 登录页广告图
    shop_name: "",
    shop_logo: "",
    pc_logo: "",
    pc_title: "",
    pc_desc: "",
    pc_ico: "",
    pc_key: "",
    pc_login_image: "", //登录封面
    contacts: "", //联系人
    mobile: "", //手机号码
    pc_name: "",
    pc_introduce: "",
});

// 表单验证
const rules = {
    name: [
        {
            required: true,
            message: "请输入网站名称",
            trigger: ["blur"],
        },
    ],
    web_favicon: [
        {
            required: true,
            message: "请选择网站图标",
            trigger: ["change"],
        },
    ],
    web_logo: [
        {
            required: true,
            message: "请选择网站logo",
            trigger: ["change"],
        },
    ],
    login_image: [
        {
            required: true,
            message: "请选择登录页广告图",
            trigger: ["change"],
        },
    ],
    shop_name: [
        {
            required: true,
            message: "请输入店铺/商城名称",
            trigger: ["blur"],
        },
    ],
    shop_logo: [
        {
            required: true,
            message: "请选择商城LOGO",
            trigger: ["change"],
        },
    ],
    pc_logo: [
        {
            required: true,
            message: "请选择PC端LOGO",
            trigger: ["change"],
        },
    ],
    pc_title: [
        {
            required: true,
            message: "请输入PC端网站标题",
            trigger: ["blur"],
        },
    ],
    pc_name: [
        {
            required: true,
            message: "请输入PC端网站名称",
            trigger: ["blur"],
        },
    ],
    pc_introduce: [
        {
            required: true,
            message: "请输入PC端网站介绍",
            trigger: ["blur"],
        },
    ],
    pc_ico: [
        {
            required: true,
            message: "请选择PC端网站图标",
            trigger: ["change"],
        },
    ],

    contacts: [
        {
            required: true,
            message: "请填写联系人姓名",
            trigger: ["change"],
        },
    ],
    mobile: [
        {
            required: true,
            message: "请填写手机号码",
            trigger: ["change"],
        },
    ],
};

// 获取备案信息
const getData = async () => {
    const data = await getWebsite();
    for (const key in formData) {
        //@ts-ignore
        formData[key] = data[key];
    }
};

// 设置备案信息
const handleSubmit = async () => {
    await formRef.value?.validate();
    await setWebsite(formData);
    appStore.getConfig();
    getData();
};

getData();
</script>

<style lang="scss" scoped></style>
