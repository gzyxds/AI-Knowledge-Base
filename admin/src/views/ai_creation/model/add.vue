<template>
    <ModelForm
        v-model="formData"
        :header-title="$route.meta.title || '添加创作模型'"
        @submit="handelSubmit"
    />
</template>

<script setup lang="ts" name="aiCreationModelAdd">
import { postCreationModel } from '@/api/ai_creation'
import ModelForm from './components/model-form.vue'
const router = useRouter()
const formData = reactive({
    name: '', //名称
    tips: '', //副标题
    category_id: '', //分类id
    content: '', //内容
    sort: 1, //排序
    status: 1, //状态
    image: '', //图标
    form: [] as any[],
    n: 1, // 最大回复
    temperature: 0.7, //词汇属性
    top_p: 0.9, //随机属性
    presence_penalty: 0.5
})

const handelSubmit = async () => {
    const params = {
        ...formData,
        form: JSON.stringify(formData.form)
    }
    await postCreationModel(params)
    router.back()
}
</script>
