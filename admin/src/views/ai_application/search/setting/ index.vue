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
                <div class="text-xl font-medium mb-[20px]">应用设置</div>
                <el-form-item label="功能开启">
                    <div>
                        <el-switch
                            v-model="formData.status"
                            :active-value="1"
                            :inactive-value="0"
                        />
                        <div class="form-tips !text-[14px]">
                            默认关闭；功能关闭后，用户将无法访问该功能
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="搜索通道" prop="channel">
                    <div>
                        <el-select v-model="formData.channel" class="w-[400px]">
                            <el-option value="tiangong" label="天工AI" />
                        </el-select>
                        <div class="form-tips">
                            开通地址：https://model-platform.tiangong.cn/
                            <a
                                class="text-primary"
                                target="_blank"
                                href="https://model-platform.tiangong.cn/"
                            >
                                前往开通
                            </a>
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="消耗电力值" prop="price">
                    <div class="w-[400px]">
                        <el-input
                            v-model="formData.price"
                            placeholder="请输入消耗电力值数量"
                            type="number"
                        />
                        <div class="form-tips">填写0则表示不消耗电力值</div>
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
import { getSearchConfig, setSearchConfig } from '@/api/ai_application/search'

const formRef = shallowRef()
const formRules = computed(() => {
    return {
        price: [
            {
                required: true,
                message: '请输入消耗电力值'
            }
        ],
        channel: [
            {
                required: true,
                message: '请选择搜索通道'
            }
        ]
    }
})
const formData = ref({
    status: 0,
    channel: 'tiangong',
    price: 0
})

const getData = async () => {
    formData.value = await getSearchConfig()
}

const handleSave = async () => {
    await formRef.value?.validate()
    await setSearchConfig(formData.value)
}
onMounted(() => {
    getData()
})
</script>
