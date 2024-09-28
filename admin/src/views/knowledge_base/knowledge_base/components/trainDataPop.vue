<template>
    <Popup ref="popRef" width="900px" title="训练数据" @close="$emit('close')">
        <div>
            <el-form inline>
                <el-form-item>
                    <el-input
                        v-model="queryParams.keyword"
                        class="w-[250px]"
                        placeholder="输入问题/回答内容关键词进行搜索"
                    />
                </el-form-item>
                <el-form-item>
                    <el-select v-model="queryParams.status">
                        <el-option label="全部" value />
                        <el-option label="等待学习" value="0" />
                        <el-option label="学习中" value="1" />
                        <el-option label="学习完成" value="2" />
                        <el-option label="失败" value="3" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button @click="resetPage" type="primary">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </div>
        <div>
            <el-table class="mt-4" size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="文档内容" prop="question">
                    <template #default="{ row }">
                        <div class="line-clamp-3">{{ row.question }}</div>
                    </template>
                </el-table-column>
                <el-table-column label="补充内容" prop="answer">
                    <template #default="{ row }">
                        <div class="line-clamp-3">{{ row.answer }}</div>
                    </template>
                </el-table-column>
                <el-table-column label="学习状态" prop="status_msg" />
                <el-table-column label="消耗电力值" prop="tokens" />
                <el-table-column label="最后更新时间" prop="update_time" />
                <el-table-column label="操作" width="150" fixed="right">
                    <template #default="{ row }">
                        <el-button type="primary" link @click="handleView(row)"> 查看 </el-button>
                        <el-button type="danger" link @click="delData(row)"> 删除 </el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </div>
    </Popup>
    <DataView v-model:model-value="dataState.current" v-model:show="dataState.show" />
</template>

<script setup lang="ts">
import { shallowRef } from 'vue'
import { usePaging } from '@/hooks/usePaging'
import { delFileData, fileDataList } from '@/api/knowledge_training/manage'
import DataView from '../../components/data-view.vue'
import feedback from '@/utils/feedback'

const emits = defineEmits(['close'])

const props = defineProps({
    kb_id: {
        type: Number,
        default: -1
    },
    fd_id: {
        type: Number,
        default: -1
    }
})

const popRef = shallowRef()
const dataState = reactive({
    current: {} as any,
    show: false
})
const kb_id = computed({
    get() {
        return props.kb_id
    },
    set(value) {
        return value
    }
})
const fd_id = computed({
    get() {
        return props.fd_id
    },
    set(value) {
        return value
    }
})

//搜索参数
const queryParams: any = ref({
    kb_id: kb_id,
    fd_id: fd_id,
    keyword: '',
    status: ''
})

//分页组件
const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: fileDataList,
    params: queryParams.value
})

const open = async () => {
    popRef.value.open()
    // ;[queryParams.value.kb_id, queryParams.value.fd_id] = [option.kb_id, option.fd_id]
    getLists()
}

const handleView = (row: any) => {
    dataState.current = row
    dataState.show = true
}

const delData = async (row: any) => {
    await feedback.confirm('删除数据会影响到用户前台智能体的正常使用，请谨慎操作！')
    await delFileData({
        uuid: row.uuid
    })
    getLists()
}

defineExpose({ open })
</script>

<style scoped lang="scss"></style>
