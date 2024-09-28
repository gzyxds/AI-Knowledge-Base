<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-alert
                class="text-xxl"
                type="warning"
                :closable="false"
                show-icon
                title="温馨提示：设置好的模型，请勿随意删除或关闭，否则会导致知识库使用不了，请谨慎操作！"
            />
        </el-card>
        <el-card shadow="never" class="!border-none mt-4">
            <el-tabs v-model="activeTab" class="mt-[-10px]" @tab-change="getData">
                <el-tab-pane
                    v-for="(item, index) in tabLists"
                    :label="`${item.name}`"
                    :name="item.label"
                    :key="index"
                    lazy
                >
                    <el-form-item label="当前默认接口" v-if="item.label == 'chat'">
                        <div>
                            <el-radio-group :model-value="currentTab?.data.defaultModel">
                                <el-radio :label="currentTab?.data.defaultModel">
                                    {{ currentTab?.data.defaultModel }}
                                </el-radio>
                            </el-radio-group>
                            <div class="form-tips !text-[14px]">在「AI模型」选中的默认AI接口</div>
                        </div>
                    </el-form-item>
                    <ModelLists
                        v-model:data="item.data.lists"
                        :models="modelList[item.label] || []"
                    />
                </el-tab-pane>
            </el-tabs>
        </el-card>
        <footer-btns>
            <el-button type="primary" @click="handelSave"> 保存 </el-button>
        </footer-btns>
    </div>
</template>
<script setup lang="ts" name="ModelBilling">
import { getAiModelAll, setModelBilling, getModelBilling } from '@/api/ai_setting/model'
import ModelLists from './components/model-lists.vue'
import feedback from '@/utils/feedback'
const activeTab = ref('chat')
const tabLists = reactive([
    {
        name: 'AI对话模型',
        label: 'chat',
        data: {
            defaultModel: '',
            lists: []
        }
    },
    {
        name: '向量模型',
        label: 'emb',
        data: {
            defaultModel: '',
            lists: []
        }
    }
])
const currentTab = computed(() => {
    return tabLists?.find((item) => item.label === activeTab.value)
})

const modelList = ref<Record<string, any[]>>({
    chat: [],
    emb: []
})
const getAiModelList = async () => {
    const data = await getAiModelAll()
    modelList.value.chat = data.chatModels
    modelList.value.emb = data.embModels
}

const handelSave = async () => {
    const currentTab = tabLists.find((item) => item.label === activeTab.value)
    if (!currentTab) {
        return feedback.msgError('保存失败，无选中标签页')
    }
    await setModelBilling({
        type: currentTab.label,
        configs: currentTab.data.lists
    })
    getData()
}

const getData = async () => {
    const data = await getModelBilling({
        type: activeTab.value
    })
    const currentTab = tabLists.find((item) => item.label === activeTab.value)
    if (currentTab) {
        currentTab.data = data
    }
}
getAiModelList()
getData()
</script>
