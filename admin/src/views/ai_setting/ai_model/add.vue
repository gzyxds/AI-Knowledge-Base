<template>
    <div>
        <AiModelForm
            ref="formRef"
            v-model="formData"
            :header-title="$route.meta.title"
            :type="type"
        />
        <footer-btns>
            <el-button type="primary" @click="handleSave">保存</el-button>
        </footer-btns>
    </div>
</template>

<script setup lang="ts" name="aiModelAdd">
import { postModel } from '@/api/ai_setting/model'
import AiModelForm from './components/form.vue'
import useTabsStore from '@/stores/modules/multipleTabs'

const tabsStore = useTabsStore()
const router = useRouter()
const route = useRoute()
const type = route.query.type as string
const formRef = shallowRef()
const formData = reactive({
    type: 1,
    channel: '',
    logo: '',
    name: '',
    configs: {},
    is_enable: 1,
    models: []
})
const handleSave = async () => {
    await formRef.value.validate()
    await postModel({ ...formData, type })
    setTimeout(() => {
        router.back()
        tabsStore.removeTab(route.fullPath, router)
    })
}
// const getDetails = async () => {
//     const data = await getAiModelDetail({
//         id: route.query.id
//     })
//     Object.assign(formData, data)
// }
// getDetails()
</script>
