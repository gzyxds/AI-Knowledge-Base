<!-- 网站信息 -->
<template>
    <div class="website-information">
        <el-form ref="formRef" :rules="rules" class="ls-form" :model="formData" label-width="120px">
            <el-card shadow="never" class="!border-none">
                <div class="text-xl font-medium mb-[20px]">示例设置</div>
                <el-form-item label="思维导图示例" prop="is_example">
                    <div>
                        <el-switch
                            v-model="formData.is_example"
                            :active-value="1"
                            :inactive-value="0"
                        />
                        <div class="form-tips">开启的话，前台显示示例按钮</div>
                    </div>
                </el-form-item>
                <el-form-item label="示例内容" prop="example_content">
                    <div class="w-[460px]">
                        <del-wrap
                            class="mb-4 block"
                            v-for="(item, index) in formData.example_content"
                            :key="index"
                            @close="formData.example_content.splice(index, 1)"
                        >
                            <el-input
                                v-model="formData.example_content[index]"
                                type="textarea"
                                :rows="10"
                                resize="none"
                            />
                        </del-wrap>
                        <el-button type="primary" @click="formData.example_content.push('')">
                            添加示例
                        </el-button>
                    </div>
                </el-form-item>
            </el-card>
        </el-form>
        <footer-btns v-perms="['setting.Mindmap/setExampleConfig']">
            <el-button type="primary" @click="handleSubmit">保存</el-button>
        </footer-btns>
    </div>
</template>

<script lang="ts" setup name="mindMapExample">
import { getMindMapExample, setMindMapExample } from '@/api/ai_application/mind_map'

import type { FormInstance } from 'element-plus'
const formRef = ref<FormInstance>()

// 表单数据
const formData = reactive({
    is_example: 0,
    example_content: []
})

// 表单验证
const rules = {}

// 获取备案信息
const getData = async () => {
    const data = await getMindMapExample()
    for (const key in formData) {
        //@ts-ignore
        formData[key] = data[key]
    }
}

// 设置备案信息
const handleSubmit = async () => {
    await formRef.value?.validate()
    await setMindMapExample(formData)
    getData()
}

getData()
</script>

<style lang="scss" scoped></style>
