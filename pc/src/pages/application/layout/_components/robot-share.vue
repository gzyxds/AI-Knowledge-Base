<template>
    <div class="share-popup">
        <popup
            ref="popupRef"
            title="分享至广场"
            :async="true"
            width="400px"
            :center="true"
            cancelButtonText=""
            confirmButtonText=""
            :appendToBody="false"
            @confirm="handleSubmit"
            @close="handleClose"
        >
            <div class="h-[100px]">
                <el-select
                    size="large"
                    class="w-[360px]"
                    v-model="formData.cate_id"
                    placeholder="全部"
                    style="--el-fill-color-blank: #F7F7FB"
                >
                    <el-option
                        v-for="item in cateLists"
                        :key="item.id"
                        :label="item.name"
                        :value="item.id"
                    />
                </el-select>
            </div>
            <template #footer>
                <div class="dialog-footer flex justify-center pb-2">
                    <el-button
                        type="primary"
                        :loading="isLock"
                        class="!rounded-md"
                        @click="handleSubmit"
                    >
                        分享至广场
                    </el-button>
                </div>
            </template>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import { getAgentCategoryList, shareAgent } from '~/api/task_reward'
import Popup from '~/components/popup/index.vue'
import { useLockFn } from '@/composables/useLockFn'
import { useUserStore } from '~/stores/user'
const userStore = useUserStore()

const emit = defineEmits(['success', 'close'])
const popupRef = shallowRef<InstanceType<typeof Popup>>()

const cateLists = ref<{
    name: string,
    id: string
    image: string
}[]>([])
const formData = reactive<{
    cate_id: string,
    id: string
}>({
    cate_id: '',
    id: ''
})

const getData = async () => {
    try {
        const list = await getAgentCategoryList()
        list.unshift({ name: '全部', id: '' })
        cateLists.value = list
    } catch (error) {
        console.log('获取视频分类失败=>', error)
    }
}

//提交
const { lockFn: handleSubmit, isLock } = useLockFn(async () => {
    await shareAgent(formData)
    await userStore.getUser()
    popupRef.value?.close()
    emit('success', formData.id)
})

const handleClose = () => {
    emit('close')
}

const open = (id: string) => {
    getData()
    popupRef.value?.open()
    formData.id = id
}

defineExpose({ open })
</script>

<style scoped>
.share-popup {
    :deep() {
        .el-dialog {
            border-radius: 20px;
        }
        .el-select__wrapper {
            box-shadow: none;
        }
        .el-select__wrapper:hover {
            box-shadow: 0 0 0 1px var(--el-border-color) inset;
        }
    }
}
</style>