<template>
    <div class="p-main flex h-full flex-col">
        <div class="text-xl font-medium">知识库设置</div>
        <el-tabs class="flex-1 min-h-0" v-model="tabsState.current">
            <el-tab-pane
                v-for="(item, index) in tabsState.lists"
                :label="item.name"
                :name="item.type"
                :key="index"
            >
                <component
                    :is="item.component"
                    :data="kbData"
                    @update="handleUpdate"
                />
            </el-tab-pane>
        </el-tabs>
    </div>
</template>

<script setup lang="ts">
import { knowKnowledgeDetail } from '@/api/my_database'
import BaseSetting from './base-setting.vue'
import { shallowRef } from 'vue'
const emit = defineEmits(['update'])
const route = useRoute()
const tabsState = reactive({
    current: 'baseSetting',
    lists: [
        {
            type: 'baseSetting',
            name: '基础信息',
            component: shallowRef(BaseSetting)
        }
    ]
})

const kbData = ref({})
//获取详情
const getDetail = async () => {
    kbData.value = await knowKnowledgeDetail({ id: route.query.id })
}

const handleUpdate = () => {
    getDetail()
    emit('update')
}
getDetail()
</script>

<style scoped lang="scss">
.el-tabs {
    :deep(.el-tab-pane) {
        flex: 1;
        min-height: 0;
    }
}
</style>
