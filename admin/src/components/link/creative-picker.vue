<template>
    <div class="detail" v-loading="pager.loading">
        <el-form ref="formRef" :model="queryParams" :inline="true">
            <el-form-item label="创作名称">
                <el-input
                    class="w-[180px]"
                    v-model="queryParams.name"
                    placeholder="请输入创作名称"
                    clearable
                    @keyup.enter="resetPage"
                />
            </el-form-item>
            <el-form-item label="创作分类">
                <div class="w-[150px]">
                    <el-select v-model="queryParams.category_id">
                        <el-option label="全部" value="" />
                        <el-option
                            v-for="(item, index) in categoryList"
                            :key="index"
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </div>
            </el-form-item>
            <el-form-item label="">
                <el-button type="primary" @click="resetPage">
                    <Icon name="el-icon-Search" />
                </el-button>
            </el-form-item>
        </el-form>
        <el-table
            ref="table"
            :data="pager.lists"
            style="width: 100%"
            height="356px"
            size="small"
            row-key="id"
        >

            <el-table-column label="选择" min-width="50">
                <template #default="{ row }">
                    <div class="flex row-center">
                        <el-checkbox
                            :model-value="getSelectItem(row.id)"
                            size="large"
                            @change="handleSelectItem(row)"
                        ></el-checkbox>
                    </div>
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
</template>

<script lang="ts" setup>
import type { PropType } from "vue";
import { LinkTypeEnum } from ".";
import { usePaging } from "@/hooks/usePaging";
import { getCreationCategoryList, getCreationModelList } from '@/api/ai_creation'
import { Search } from "@element-plus/icons-vue";

//TODO TODO
const props = defineProps({
    modelValue: {
        type: Object as PropType<any>,
        default: () => ({}),
    },
    type: {
        type: String,
        default: 'pc'
    }
});
const emit = defineEmits<{
    (event: "update:modelValue", value: any): void;
}>();

const selectData = ref<any>({
    path: '/pages/news_detail/news_detail',
    name: '',
    query: {},
    type: LinkTypeEnum.CREATIVE_PICKER,
});

//分类列表
const categoryList: any = ref([])
// 查询参数
const queryParams = reactive<any>({
    name: '',
    category_id: ''
});

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getCreationModelList,
    params: queryParams,
});

// pc还是移动端
const typeLink = computed(() => props.type == 'pc' ? '/creation/produce' : '/packages/pages/create/create')

//获取分类列表
const getCategoryList = async () => {
    const { lists } = await getCreationCategoryList({
        page_type: 0
    })
    categoryList.value = lists
}

const getSelectItem = (id: number) => {
    return id == Number(selectData.value.id);
};
/**
 * @description 选择
 */
const handleSelectItem = (event: any) => {
    // 这里是跳转参数，比如说是移动端和pc端的参数会不同
    let query = null
    if (props.type == 'pc') {
        query = { modelId: event.id, cateId: 0 }
    } else {
        query = { id: event.id }
    }
    selectData.value = {
        id: event.id,
        name: event.name,
        path: typeLink.value,
        query: query,
        type: LinkTypeEnum.CREATIVE_PICKER,
    };

    emit("update:modelValue", selectData.value);
};
watch(
    () => props.modelValue,
    (value) => {
        if (value.type != LinkTypeEnum.CREATIVE_PICKER) {
            return (selectData.value = {
                id: "",
                name: "",
                path: typeLink.value,
                type: LinkTypeEnum.SHOP_PAGES,
            });
        }
        selectData.value = value;
    },
    {
        immediate: true,
    }
);

getCategoryList()
getLists();
</script>

<style lang="scss" scoped>
:deep(.el-input-group__append) {
    .el-button {
        margin: 0 0;
    }
}
</style>