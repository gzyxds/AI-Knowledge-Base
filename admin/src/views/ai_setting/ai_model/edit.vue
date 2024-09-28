<template>
    <div v-loading="isLock">
        <AiModelForm
            ref="formRef"
            v-model="formData"
            :header-title="$route.meta.title"
            :current-id="id"
            :type="type"
        />
        <footer-btns>
            <el-button
                v-perms="['setting.ai.models/del']"
                type="danger"
                @click="handleDelete"
                v-if="!formData.is_system"
            >
                删除
            </el-button>
            <el-button type="primary" @click="handleSave">保存</el-button>
        </footer-btns>
    </div>
</template>

<script setup lang="ts" name="aiModelEdit">
import useTabsStore from '@/stores/modules/multipleTabs'
import AiModelForm from './components/form.vue'
import { getAiModelDetail, putModel, deleteModel } from '@/api/ai_setting/model'
import feedback from '@/utils/feedback'
import { useLockFn } from '@/hooks/useLockFn'
const router = useRouter()
const route = useRoute()
const type = route.query.type as string
const id = route.query.id as string
const tabsStore = useTabsStore()
const formRef = shallowRef()
const formData = ref({
    is_system: 0,
    type: 1,
    channel: '',
    logo: '',
    name: '',
    configs: {},
    is_enable: 0,
    models: []
})

const { lockFn: getDetails, isLock } = useLockFn(async () => {
    formData.value = await getAiModelDetail({
        id: route.query.id
    })
})
const handleSave = async () => {
    await formRef.value.validate()
    await putModel({ ...formData.value, type })
    setTimeout(() => {
        router.back()
        tabsStore.removeTab(route.fullPath, router)
    })
}
const handleDelete = async () => {
    await feedback.confirm(
        `确定要删除 ${formData.value.name} 吗？删除后已经选择该模型的用户将无法正常使用，请谨慎操作！`
    )
    await deleteModel({ id: route.query.id })
    router.back()
    tabsStore.removeTab(route.fullPath, router)
}
getDetails()
</script>
