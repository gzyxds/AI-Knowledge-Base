<template>
    <div class="inline-flex" @click="showPopup = true">
        <slot name="trigger">
            <el-button :disabled="disabled" size="mini" type="primary">选择创作</el-button>
        </slot>
    </div>
    <ElDialog v-model="showPopup" width="800px">
        <div class="detail" v-loading="pager.loading">
            <el-form ref="formRef" :model="queryParams" :inline="true">
                <el-form-item label="创作名称">
                    <el-input
                        v-model="queryParams.name"
                        placeholder="请输入创作名称"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="创作分类">
                    <el-select v-model="queryParams.category_id">
                        <el-option label="全部" value="" />
                        <el-option
                            v-for="(item, index) in categoryList"
                            :key="index"
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="">
                    <el-button type="primary" @click="resetPage">
                        <Icon name="el-icon-Search" />
                    </el-button>
                </el-form-item>
            </el-form>
            <div class="my-[10px]">
                <el-checkbox v-model="selectAll">全选</el-checkbox>
            </div>
            <el-table
                ef="table"
                :data="pager.lists"
                style="width: 100%"
                height="370px"
                size="mini"
                row-key="id"
            >
                <el-table-column width="45">
                    <template v-slot="{ row }">
                        <el-checkbox
                            :model-value="selectItem(row)"
                            @change="handleSelect($event, row)"
                        />
                    </template>
                </el-table-column>

                <el-table-column label="图标" min-width="100">
                    <template #default="{ row }">
                        <el-image :src="row.image" class="w-[44px] h-[44px]" />
                    </template>
                </el-table-column>
                <el-table-column label="模型名称" prop="name" min-width="120" />
                <el-table-column label="模型描述" prop="tips" min-width="150" />
                <el-table-column label="所属类目" prop="category_name" min-width="120" />

                <el-table-column label="状态" min-width="100">
                    <template #default="{ row }">
                        <el-switch
                            :disabled="true"
                            v-model="row.status"
                            :active-value="1"
                            :inactive-value="0"
                        />
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-5">
                <pagination v-model="pager" @change="getLists()" />
            </div>
        </div>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="showPopup = false"> 取消 </el-button>
                <el-button type="primary" @click="handleConfirm"> 确定 </el-button>
            </div>
        </template>
    </ElDialog>
</template>

<script lang="ts" setup>
import { useVModel } from '@vueuse/core'
import { getCreationCategoryList, getCreationModelList } from '@/api/ai_creation'
import { usePaging } from '@/hooks/usePaging'
import { cloneDeep, isArray } from 'lodash-es'
import feedback from '@/utils/feedback'
const props = withDefaults(
    defineProps<{
        modelValue: any[]
        disabled?: boolean
        limit?: number
    }>(),
    {
        disabled: false,
        modelValue: () => [],
        limit: 50
    }
)
const emit = defineEmits<{
    (event: 'update:modelValue', value: any): void
}>()

const showPopup = ref(false)
//分类列表
const categoryList: any = ref([])
//搜索参数
const queryParams = reactive({
    name: '',
    category_id: ''
})
const selectData = ref<any[]>([])
const selectAll = computed({
    get() {
        const { lists } = pager
        const ids: any[] = selectData.value.map((item: any) => item.id)
        if (!lists.length) {
            return false
        }
        return lists.every((item) => ids.includes(item.id))
    },
    set(value) {
        const { lists } = pager
        if (value) {
            for (let i = 0; i < lists.length; i++) {
                const item = lists[i]
                const ids: any[] = selectData.value.map((item: any) => item.id)
                if (!ids.includes(item.id)) {
                    if (checkLength()) {
                        return
                    }
                    selectData.value.push(item)
                }
            }
            console.log(selectData.value)
        } else {
            lists.forEach((row) => {
                setSelectData(row)
            })
        }
    }
})

const selectItem = (row: any) => {
    return selectData.value.some((item: any) => item.id == row.id)
}

const setSelectData = (row: any) => {
    const index = selectData.value.findIndex((item: any) => item.id == row.id)
    if (index != -1) {
        selectData.value.splice(index, 1)
    }
}
const checkLength = () => {
    if (selectData.value.length >= props.limit) {
        feedback.msgWarning(`最多选择${props.limit}条`)
        return true
    }
    return false
}

const handleSelect = ($event: boolean, row: any) => {
    if ($event) {
        if (checkLength()) {
            return
        }
        selectData.value.push(row)
    } else {
        setSelectData(row)
    }
}

const handleConfirm = () => {
    emit('update:modelValue', selectData.value)
    showPopup.value = false
}

const { pager, getLists, resetPage } = usePaging({
    fetchFun: getCreationModelList,
    params: queryParams
})
//获取分类列表
const getCategoryList = async () => {
    const { lists } = await getCreationCategoryList({
        page_type: 0
    })
    categoryList.value = lists
}

watch(showPopup, (value) => {
    if (value) {
        getCategoryList()
        getLists()
    }
})

watch(
    () => props.modelValue,
    (value) => {
        selectData.value = cloneDeep(value)
    },
    { immediate: true, deep: true}
)
</script>
