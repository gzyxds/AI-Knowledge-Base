<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form
                ref="formRef"
                class="mb-[-16px]"
                :model="queryParams"
                :inline="true"
            >
                <el-form-item class="w-[280px]" label="模型名称">
                    <el-input
                        v-model="queryParams.title"
                        placeholder="请输入模型名称"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item class="w-[280px]" label="模型状态">
                    <el-select v-model="queryParams.status">
                        <el-option label="全部" value="" />
                        <el-option label="开启" :value="1" />
                        <el-option label="关闭" :value="0" />
                    </el-select>
                </el-form-item>
                <el-form-item class="w-[280px]" label="模型分类">
                    <el-select
                        v-model="queryParams.category_id"
                        placeholder="请选择所属分类"
                        class="w-full"
                        clearable
                    >
                        <el-option
                            v-for="(item, index) in optionsData.categoryList"
                            :key="index"
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage"
                        >查询</el-button
                    >
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>

        <el-card class="!border-none mt-4" shadow="never">
            <div class="flex gap-2">
                <router-link
                    v-perms="['application.sd.model/edit']"
                    :to="getRoutePath('application.sd.model/edit')"
                >
                    <el-button type="primary">
                        <template #icon>
                            <icon name="el-icon-Plus" />
                        </template>
                        新增
                    </el-button>
                </router-link>
                <el-button
                    type="default"
                    :plain="true"
                    :disabled="!multipleSelection.length"
                    @click="
                        handleDelete(multipleSelection.map((item) => item.id))
                    "
                >
                    批量删除
                </el-button>
            </div>
            <el-table
                size="large"
                class="mt-4"
                v-loading="pager.loading"
                :data="pager.lists"
                @selection-change="handleSelectionChange"
            >
                <el-table-column type="selection" width="55" />
                <el-table-column label="模型封面" min-width="100">
                    <template #default="{ row }">
                        {{ row.cover ? "" : "暂无" }}
                        <el-image
                            v-if="row.cover"
                            :src="row.cover"
                            class="w-[44px] h-[44px]"
                        />
                    </template>
                </el-table-column>
                <el-table-column
                    label="模型名称"
                    prop="title"
                    min-width="160"
                />
                <el-table-column
                    label="模型标识"
                    prop="model_name"
                    min-width="160"
                />
                <el-table-column
                    label="模型分类"
                    prop="category_name"
                    min-width="120"
                />
                <el-table-column
                    label="关联微调模型"
                    prop="lora_total"
                    min-width="100"
                />
                <el-table-column label="状态" min-width="100">
                    <template #default="{ row }">
                        <el-switch
                            @change="changeStatus(row.id)"
                            v-model="row.status"
                            :active-value="1"
                            :inactive-value="0"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="排序" prop="sort" min-width="120" />
                <el-table-column
                    label="创建时间"
                    prop="create_time"
                    min-width="100"
                />
                <el-table-column label="操作" width="150" fixed="right">
                    <template #default="{ row }">
                        <el-button type="primary" link>
                            <router-link
                                v-perms="['application.sd.model/edit']"
                                :to="{
                                    path: getRoutePath(
                                        'application.sd.model/edit'
                                    ),
                                    query: {
                                        id: row.id,
                                    },
                                }"
                            >
                                编辑
                            </router-link>
                        </el-button>
                        <el-button
                            v-perms="['application.sd.model/del']"
                            type="danger"
                            link
                            @click="handleDelete(row.id)"
                        >
                            删除
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
    </div>
</template>
<script lang="ts" setup name="problemExample">
import { usePaging } from "@/hooks/usePaging";
import { getRoutePath } from "@/router";
import {
    getModelList,
    delModel,
    editModelStatus,
} from "@/api/ai_draw/draw_model";
import { getModelCategoryList } from "@/api/ai_draw/draw_model_category";
import feedback from "@/utils/feedback";
import { useDictOptions } from "@/hooks/useDictOptions";

const route = useRoute();
//搜索参数
const queryParams: {
    title: string;
    status: string;
    category_id: number | string;
} = reactive({
    title: "",
    status: "",
    category_id: "",
});
const multipleSelection = ref<any[]>([]);

queryParams.category_id = parseInt(route.query.category_id as string) || "";

//删除
const handleDelete = async (id: number | number[]) => {
    await feedback.confirm("确定要删除？");
    await delModel({ id });
    getLists();
};

const handleSelectionChange = (val: any[]) => {
    multipleSelection.value = val;
};

//修改状态
const changeStatus = (id: any) => {
    editModelStatus({ id });
};

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getModelList,
    params: queryParams,
});

const { optionsData } = useDictOptions<{ categoryList: any[] }>({
    categoryList: {
        api: getModelCategoryList,
        transformData(data) {
            return data;
        },
    },
});

getLists();
</script>
