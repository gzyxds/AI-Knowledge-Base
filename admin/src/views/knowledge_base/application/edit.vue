<template>
    <div>
        <ApplicationForm v-model="formData" :header-title="$route.meta.title || '查看智能体'" />
    </div>
</template>

<script setup lang="ts" name="knowledgeBaseApplicationEdit">
import { getApplyManageDetail } from '@/api/knowledge_base/application'
import ApplicationForm from './components/form/index.vue'
const router = useRouter()
const route = useRoute()
const formData = reactive({
    kb_ids: [],
    category_id: '' as string | number,
    image: '',
    name: '',
    intro: '',
    systemPrompt: '',
    limitPrompt: '',
    searchEmptyText: '',
    searchSimilarity: 0.81,
    searchLimit: 5,
    sort: 0,
    is_enable: 1,
    null_reply_type: 1,
    is_show_context: 1,
    is_show_quote: 1
})

const getDetails = async () => {
    const data = await getApplyManageDetail({
        id: route.query.id
    })
    Object.assign(formData, data)
}
getDetails()
</script>
