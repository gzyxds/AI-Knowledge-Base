<template>
    <ModelForm
        v-loading="loading"
        v-model="formData"
        :header-title="$route.meta.title || '编辑创作模型'"
        @submit="handelSubmit"
    />
</template>

<script setup lang="ts" name="aiCreationModelAdd">
import { getCreationModelDetail, putCreationModel } from '@/api/ai_creation'
import ModelForm from './components/model-form.vue'
const router = useRouter()
const route = useRoute()
const formData = reactive({
    name: '', //名称
    tips: '', //副标题
    category_id: '', //分类id
    content: '', //内容
    sort: 0, //排序
    status: 1, //状态
    system: '',
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
    await putCreationModel(params)
    router.back()
}
const loading = ref(false)
const getDetails = async () => {
    loading.value = true
    try {
        const data = await getCreationModelDetail({
            id: route.query.id
        })
        Object.assign(formData, data)
    } finally {
        loading.value = false
    }
}
getDetails()
</script>
