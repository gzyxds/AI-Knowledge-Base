<template>
    <el-page-header content="发布网页/朋友圈海报" @back="emit('back')" />

    <div class="mt-4">
        <el-button class="mb-4" type="primary" @click="showCreatePopup()">
            创建链接
        </el-button>
        <div class="text-xs text-tx-secondary">
            可以直接分享该模型给其他用户去进行对话，对方无需登录即可直接进行对话。注意，这个功能会消耗你账号的问答条数。请保管好链接和密码。
        </div>
    </div>
    <div class="mt-4">
        <el-table v-loading="pager.loading" :data="pager.lists" size="large">
            <el-table-column label="apikey" prop="apikey" min-width="200" />
            <el-table-column
                label="分享名称"
                prop="name"
                min-width="180"
                show-tooltip-when-overflow
            />
            <el-table-column label="访问密码" prop="secret" min-width="120" />
            <el-table-column label="对话模式" min-width="120">
                <template #default="{ row }">
                    <div>
                        {{ row.chat_type == 1 ? '文本对话' : '形象对话' }}
                    </div>
                </template>
            </el-table-column>

            <el-table-column
                label="最后使用时间"
                prop="use_time"
                min-width="180"
            />
            <el-table-column label="操作" width="220" fixed="right">
                <template #default="{ row }">
                    <div class="flex items-center">
                        <el-button type="primary" link @click="showPoster(row)">
                            生成海报
                        </el-button>
                        <el-button type="primary" link @click="copyLink(row)">
                            复制链接
                        </el-button>
                        <el-dropdown
                            class="ml-[10px]"
                            @command="handleCommand($event, row)"
                        >
                            <el-button type="primary" link>
                                更多
                                <Icon name="el-icon-ArrowDown" />
                            </el-button>
                            <template #dropdown>
                                <el-dropdown-menu>
                                    <el-dropdown-item command="edit">
                                        <el-button type="primary" link>
                                            编辑
                                        </el-button>
                                    </el-dropdown-item>
                                    <el-dropdown-item command="preview">
                                        <el-button type="primary" link>
                                            预览
                                        </el-button>
                                    </el-dropdown-item>
                                    <el-dropdown-item command="usage">
                                        <el-button type="primary" link>
                                            用量设置
                                        </el-button>
                                    </el-dropdown-item>
                                    <el-dropdown-item command="delete">
                                        <el-button type="danger" link>
                                            删除
                                        </el-button>
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </template>
                        </el-dropdown>
                    </div>
                </template>
            </el-table-column>
        </el-table>
        <div class="flex justify-end mt-4">
            <pagination v-model="pager" @change="getLists" />
        </div>
    </div>
    <CreateShare
        ref="createShareRef"
        :isShowChatType="true"
        @confirm="handleCreateShare"
    />
    <UsageSettings ref="usageSettingsRef" @confirm="handelUsageSettings" />
    <Poster
        v-bind="posterState"
        v-model:show="posterState.show"
        @update="getLists()"
    />
</template>

<script setup lang="ts">
import CreateShare from './create-share.vue'
import UsageSettings from './usage-settings.vue'
import Poster from './poster.vue'
import feedback from '@/utils/feedback'
import {
    delRelease,
    getReleaseList,
    putRelease,
    postRelease,
    postReleaseInfo
} from '@/api/robot'
const props = defineProps<{
    appId: number | string
}>()
const emit = defineEmits(['back'])
const { copy } = useCopy()
const createShareRef = shallowRef<InstanceType<typeof CreateShare>>()
const usageSettingsRef = shallowRef<InstanceType<typeof UsageSettings>>()
const posterState = reactive({
    show: false,
    url: '',
    apikey: '',
    shareId: ''
})
const { appId } = toRefs(props)
const queryParams = reactive({
    robot_id: appId,
    type: 1
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

const handleCommand = (command: string, row: any) => {
    switch (command) {
        case 'edit':
            showCreatePopup(row)
            break
        case 'delete':
            shareDelete(row.id)
            break
        case 'usage':
            showUsageSettings(row)
            break
        case 'preview':
            preview(row)
            break
    }
}
const getLink = (row: any) => {
    const link = `${location.origin}/chat/${row.apikey} #${row.name} ${
        row.secret ? `密码: ${row.secret}` : ''
    }`
    return link
}
const preview = async (row: any) => {
    const link = getLink(row)
    window.open(link, '_blank')
}
const showCreatePopup = (row?: any) => {
    let data = null
    if (row) {
        data = {
            id: row.id,
            name: row.name,
            password: row.secret
        }
    }
    createShareRef.value?.open(data)
}
const handleCreateShare = async (formData: any, type: string) => {
    await (type == 'add'
        ? putRelease({
              ...formData,
              ...queryParams
          })
        : postReleaseInfo({
              id: formData.id,
              name: formData.name,
              password: formData.password
          }))
    createShareRef.value?.close()
    getLists()
}

const copyLink = async (row: any) => {
    const link = getLink(row)
    await copy(link)
}

const showUsageSettings = (row: any) => {
    usageSettingsRef.value?.open()
    usageSettingsRef.value?.setFormData(row)
}

const handelUsageSettings = async (formData: any) => {
    await postRelease({
        ...formData,
        ...queryParams
    })
    usageSettingsRef.value?.close()
    getLists()
}

const showPoster = (row: any) => {
    posterState.show = true
    posterState.url = row.share_bg
    posterState.apikey = row.apikey
    posterState.shareId = row.id
}

watch(
    () => props.appId,
    () => {
        getLists()
    }
)
</script>
