<template>
    <el-page-header content="发布微信公众号" @back="emit('back')" />

    <div class="mt-4">
        <el-button type="primary" @click="showCreatePopup()">
            创建链接
        </el-button>
        <!-- <div class="text-xs text-tx-secondary">
      可以直接分享该模型给其他用户去进行对话，对方无需登录即可直接进行对话。注意，这个功能会消耗你账号的问答条数。请保管好链接和密码。
    </div> -->
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
            <!--      <el-table-column label="对话模式" min-width="120">-->
            <!--        <template #default="{ row }">-->
            <!--          <div>{{ row.chat_type == 1 ? '文本对话' : '形象对话' }}</div>-->
            <!--        </template>-->
            <!--      </el-table-column>-->
            <el-table-column
                label="最后使用时间"
                prop="use_time"
                min-width="180"
            />
            <el-table-column label="操作" min-width="150" fixed="right">
                <template #default="{ row }">
                    <div class="flex items-center">
                        <el-button
                            type="primary"
                            link
                            @click="oaConfigRef?.open(row.apikey)"
                        >
                            公众号配置
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
        :isShowChatType="false"
        @confirm="handleCreateShare"
    />
    <UsageSettings ref="usageSettingsRef" @confirm="handelUsageSettings" />
    <OaConfig ref="oaConfigRef" />
</template>

<script setup lang="ts">
import CreateShare from './create-share.vue'
import UsageSettings from './usage-settings.vue'
import OaConfig from './oa-config.vue'

import feedback from '@/utils/feedback'
import {
    delRelease,
    getReleaseList,
    postRelease,
    putRelease,
    postReleaseInfo
} from '@/api/robot'
const props = defineProps<{
    appId: number | string
}>()
const emit = defineEmits(['back'])
const createShareRef = shallowRef<InstanceType<typeof CreateShare>>()
const usageSettingsRef = shallowRef<InstanceType<typeof UsageSettings>>()
const oaConfigRef = shallowRef<InstanceType<typeof OaConfig>>()

const { appId } = toRefs(props)
const queryParams = reactive({
    robot_id: appId,
    type: 3
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
        case 'delete':
            shareDelete(row.id)
            break
        case 'edit':
            showCreatePopup(row)
            break
        case 'usage':
            showUsageSettings(row)
    }
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

watch(
    () => props.appId,
    () => {
        getLists()
    }
)
</script>
