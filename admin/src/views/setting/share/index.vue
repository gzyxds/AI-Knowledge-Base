<template>
    <div class="">
        <el-card class="!border-none" shadow="never">
            <el-alert
                type="warning"
                title="分享说明：如果选择的是分享当前页面，创作详情、技能详情、文章详情等页面，分享的时候就不读取分享标题、分享简介、分享封面的配置。"
                :closable="false"
                show-icon
            ></el-alert>
        </el-card>
        <el-form ref="formRef" class="ls-form mt-4" :model="formData" label-width="120px">
            <el-card shadow="never" class="!border-none">
                <div class="text-xl font-medium mb-[20px]">分享设置</div>
                <el-form-item label="分享页面" prop="name">
                    <div>
                        <el-radio-group v-model="formData.share_page" class="ml-4">
                            <el-radio :label="1">当前页面</el-radio>
                            <el-radio :label="2">首页</el-radio>
                        </el-radio-group>
                        <div class="form-tips">
                            {{
                                formData.share_page === 1
                                    ? '分享者把哪个页面分享出去，用户打开的就是当前分享对应的链接'
                                    : '分享者不管把哪个页面分享出去，用户打开的链接都是首页'
                            }}
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="分享标题" prop="name">
                    <div class="w-80">
                        <el-input
                            v-model.trim="formData.share_title"
                            :placeholder="
                                formData.share_page === 1
                                    ? '分享标题，不填则为当前页面标题'
                                    : '分享标题，不填则默认显示PC端名称'
                            "
                        />
                    </div>
                </el-form-item>
                <el-form-item label="分享简介" prop="name">
                    <div class="w-80">
                        <el-input
                            v-model.trim="formData.share_content"
                            placeholder="分享简介，不填则为空"
                            type="textarea"
                            :rows="5"
                        />
                    </div>
                </el-form-item>
                <el-form-item label="分享封面" prop="name">
                    <div>
                        <material-picker v-model="formData.share_image" :limit="1" />
                        <div class="form-tips">
                            不填分享时则默认显示PC端logo，建议尺寸：宽200px*高200px。jpg，jpeg，png格式
                        </div>
                    </div>
                </el-form-item>
            </el-card>
        </el-form>

        <footer-btns v-perms="['setting.shareSetting/setConfig']">
            <el-button type="primary" @click="handleSubmit">保存</el-button>
        </footer-btns>
    </div>
</template>
<script setup lang="ts">
import { getshareConfig, setshareConfig } from '@/api/setting/share'

interface formDataInter {
    share_page: number
    share_title: string
    share_content: string
    share_image: string
}
const formData = ref<formDataInter>({
    share_page: 1,
    share_title: '',
    share_content: '',
    share_image: ''
})
/**
 * 初始化数据
 */
const getData = async () => {
    formData.value = await getshareConfig()
}
getData()
/**
 * 保存数据
 */
const handleSubmit = async () => {
    await setshareConfig(formData.value)
    getData()
}
</script>
