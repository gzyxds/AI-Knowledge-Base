<template>
    <el-page-header :content="title" @back="emit('back')" />
    <div class="flex items-center mt-4">
        <div class="flex">
            <el-button type="primary" @click="createApiRef?.open()">
                创建API
            </el-button>
            <CallDescription v-if="type == 4" type="app" class="ml-[10px]" />
            <CallDescription v-if="type == 5" type="wx" class="ml-[10px]" />
        </div>
        <div class="ml-4 flex items-center">
            API根地址：
            <span>{{ path }}</span>
            <ElButton @click="handleCopy(path)" type="primary" link
                >复制</ElButton
            >
        </div>
    </div>
    <div class="mt-4">
        <el-table v-loading="pager.loading" :data="pager.lists" size="large">
            <el-table-column label="apikey" prop="apikey" min-width="200" />
            <el-table-column
                label="名称"
                prop="name"
                min-width="180"
                show-tooltip-when-overflow
            />
            <el-table-column
                label="最后使用时间"
                prop="use_time"
                min-width="180"
            />
            <el-table-column label="操作" width="150" fixed="right">
                <template #default="{ row }">
                    <div class="flex items-center">
                        <el-button
                            type="primary"
                            link
                            @click="showUsageSettings(row)"
                        >
                            用量设置
                        </el-button>
                        <el-button
                            type="danger"
                            link
                            @click="shareDelete(row.id)"
                        >
                            删除
                        </el-button>
                    </div>
                </template>
            </el-table-column>
        </el-table>
        <div class="flex justify-end mt-4">
            <pagination v-model="pager" @change="getLists" />
        </div>
    </div>
    <CreateApi ref="createApiRef" @confirm="handleCreateApi" />
    <UsageSettings ref="usageSettingsRef" @confirm="handelUsageSettings" />
</template>

<script setup lang="ts">
import CreateApi from './create-api.vue'
import UsageSettings from './usage-settings.vue'
import CallDescription from './call-description.vue'

import feedback from '@/utils/feedback'
import {
    getReleaseList,
    putRelease,
    delRelease,
    postRelease
} from '@/api/robot'
const props = defineProps<{
    appId: number | string
    type: number | string
}>()
const emit = defineEmits(['back'])
const { copy } = useCopy()
const createApiRef = shallowRef<InstanceType<typeof CreateApi>>()
const usageSettingsRef = shallowRef<InstanceType<typeof UsageSettings>>()
const { appId } = toRefs(props)
const queryParams = reactive({
    robot_id: appId,
    type: props.type
})

const { pager, getLists } = usePaging({
    fetchFun: getReleaseList,
    params: queryParams
})
getLists()

const shareDelete = async (id: number) => {
    await feedback.confirm('确定删除？')
    await delRelease({
        id,
        type: queryParams.type
    })
    getLists()
}

const title = computed(() => {
    switch (props.type) {
        case 5:
            return '发布企业微信'
        default:
            return '发布API'
    }
})

const handleCreateApi = async (formData: any) => {
    await putRelease({
        ...formData,
        ...queryParams
    })
    createApiRef.value?.close()
    getLists()
}
const handelUsageSettings = async (formData: any) => {
    await postRelease({
        ...formData,
        ...queryParams
    })
    usageSettingsRef.value?.close()
    getLists()
}

const path = ref('')
path.value = `${window.location.origin}/api`
const handleCopy = async (str: string) => {
    await copy(str)
}

const showUsageSettings = (row: any) => {
    usageSettingsRef.value?.open()
    usageSettingsRef.value?.setFormData(row)
}

watch(
    () => props.appId,
    () => {
        getLists()
    }
)
</script>
