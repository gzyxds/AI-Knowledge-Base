<template>
    <div>
        <el-card shadow="never" class="!border-none">
            <el-tabs v-model="activeTab">
                <el-tab-pane
                    v-for="(item, index) in tabLists"
                    :label="`${item.name}`"
                    :name="item.label"
                    :key="index"
                    lazy
                >
                    <!-- <component
                        ref="tabItemRef"
                        :is="item.component"
                        v-model="configLists[item.configKey]"
                    /> -->
                </el-tab-pane>
            </el-tabs>

            <draggable
                class="flex flex-wrap mx-[-10px]"
                v-model="currentModel"
                animation="300"
                @sort="handleSort"
            >
                <template v-slot:item="{ element: item }">
                    <div class="mx-[10px] mb-[20px]">
                        <div
                            class="flex items-center px-[15px] py-[25px] w-[300px] bg-[#f8f8f8] dark:bg-page rounded-[12px] h-full cursor-move hover:bg-primary-light-9 border border-solid border-page hover:border-primary"
                        >
                            <div
                                class="flex items-center flex-1 min-w-0"
                                :class="{
                                    'opacity-60': !item.is_enable
                                }"
                            >
                                <el-image :src="item.logo" class="w-[44px] h-[44px]" />
                                <div class="mx-[16px] flex-1 min-w-0">
                                    <div class="text-xl font-bold mb-[4px]">{{ item.name }}</div>
                                    <div
                                        class="flex items-center text-tx-secondary before:mr-[6px] before:block before:w-[8px] before:h-[8px] before:bg-success before:rounded-[50%]"
                                        :class="{
                                            'before:!bg-danger': !item.is_enable
                                        }"
                                    >
                                        {{ item.is_enable ? '已启用' : '已停用' }}
                                    </div>
                                </div>
                            </div>

                            <router-link
                                v-perms="['setting.ai.models/edit']"
                                :to="{
                                    path: getRoutePath('setting.ai.models/edit'),
                                    query: {
                                        id: item.id,
                                        type: item.type
                                    }
                                }"
                            >
                                <el-button type="primary" plain>编辑</el-button>
                            </router-link>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <div class="mx-[10px] mb-[20px] min-h-[100px]">
                        <router-link
                            v-perms="['setting.ai.models/add']"
                            :to="{
                                path: getRoutePath('setting.ai.models/add'),
                                query: {
                                    type: activeTab === 'chatModels' ? 1 : 2
                                }
                            }"
                        >
                            <div
                                class="flex items-center justify-center px-[15px] py-[25px] w-[300px] bg-[#f8f8f8] dark:bg-page rounded-[12px] h-full hover:bg-primary-light-9 border border-solid border-page hover:border-primary"
                            >
                                <Icon name="el-icon-Plus" :size="20" />
                                <div class="text-xl font-bold ml-1">自定义模型</div>
                            </div>
                        </router-link>
                    </div>
                </template>
            </draggable>
        </el-card>
    </div>
</template>
<script setup lang="ts" name="aiModel">
import Session from './components/session.vue'
import Embedding from './components/embedding.vue'
import { getAiModel, putAiModelSort } from '@/api/ai_setting/model'
import { getRoutePath } from '@/router'
import Draggable from 'vuedraggable'
const tabItemRef = shallowRef()
const activeTab = ref('chatModels')
const tabLists = reactive([
    {
        name: 'AI对话配置',
        label: 'chatModels',
        component: shallowRef(Session),
        type: 1
    },
    {
        name: '向量模型配置',
        label: 'vectorModels',
        component: shallowRef(Embedding),
        type: 2
    }
])

const configLists = ref<any>({
    chatModels: [],
    vectorModels: []
})
const currentModel = computed({
    get() {
        return configLists.value[activeTab.value]
    },
    set(value) {
        configLists.value[activeTab.value] = value
    }
})
const getData = async () => {
    configLists.value = await getAiModel()
}

const handleSort = async () => {
    const orders = currentModel.value.map((item: any, index: number) => ({
        id: item.id,
        sort: index
    }))
    try {
        await putAiModelSort({ orders })
    } catch (error) {
        getData()
    }
}

getData()
</script>
