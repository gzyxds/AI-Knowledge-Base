<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form
                ref="formRef"
                class="mb-[-16px]"
                :model="queryParams"
                :inline="true"
            >
                <el-form-item label="模型名称">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.title"
                        placeholder="请输入模型名称"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item class="w-[280px]" label="模型状态">
                    <el-select v-model="queryParams.status" clearable>
                        <el-option label="全部" value="" />
                        <el-option label="开启" :value="1" />
                        <el-option label="关闭" :value="0" />
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
            <div>
                <el-button
                    v-perms="['creation.creationCategory/add']"
                    type="primary"
                    @click="handleAdd"
                >
                    <template #icon>
                        <icon name="el-icon-Plus" />
                    </template>
                    新增
                </el-button>
            </div>
            <el-table
                size="large"
                class="mt-4"
                v-loading="pager.loading"
                :data="pager.lists"
            >
                <el-table-column label="模型封面" min-width="100">
                    <template #default="{ row }">
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
                    min-width="120"
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
                        <el-button
                            v-perms="['creation.creationCategory/edit']"
                            type="primary"
                            link
                            @click="handleEdit(row)"
                        >
                            编辑
                        </el-button>
                        <el-button
                            v-perms="['creation.creationCategory/del']"
                            type="danger"
                            link
                            @click="handleDelete(row.id, row.model_count)"
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
        <edit-popup
            v-if="showEdit"
            ref="editRef"
            @success="getLists"
            @close="showEdit = false"
        />
    </div>
</template>
<script lang="ts" setup name="problemExample">
import { usePaging } from "@/hooks/usePaging";
import EditPopup from "./edit.vue";
import { delLora, editLoraStatus, getLoraList } from "@/api/ai_draw/draw_lora";
import feedback from "@/utils/feedback";
const editRef = shallowRef<InstanceType<typeof EditPopup>>();
//搜索参数
const queryParams = reactive({
    title: "",
    status: "",
});
const showEdit = ref(false);
//添加
const handleAdd = async () => {
    showEdit.value = true;
    await nextTick();
    editRef.value?.open("add");
};
//编辑
const handleEdit = async (data: any) => {
    showEdit.value = true;
    await nextTick();
    editRef.value?.open("edit");
    editRef.value?.setFormData(data);
};
//删除
const handleDelete = async (id: number, model_count: number) => {
    await feedback.confirm("确定要删除？");
    await delLora({ id });
    getLists();
};

//修改状态
const changeStatus = (id: any) => {
    editLoraStatus({ id });
};

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getLoraList,
    params: queryParams,
});

getLists();
</script>
