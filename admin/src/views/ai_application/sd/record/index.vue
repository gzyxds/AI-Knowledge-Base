<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form
                ref="formRef"
                class="mb-[-16px]"
                :model="queryParams"
                :inline="true"
            >
                <el-form-item class="w-[280px]" label="用户信息">
                    <el-input
                        v-model="queryParams.user_info"
                        placeholder="请输入用户ID/用户昵称"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item class="w-[280px]" label="提示词">
                    <el-input
                        v-model="queryParams.prompt"
                        placeholder="请输入提示词"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item class="w-[280px]" label="绘画类型">
                    <el-select v-model="queryParams.type">
                        <el-option label="全部" value></el-option>
                        <el-option
                            v-for="(item, index) in drawType"
                            :key="item.value"
                            :label="item.label"
                            :value="item.value"
                        ></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item class="w-[280px]" label="生成结果">
                    <el-select v-model="queryParams.status">
                        <el-option label="全部" value></el-option>
                        <el-option label="生成中" :value="1"></el-option>
                        <el-option label="生成失败" :value="2"></el-option>
                        <el-option label="生成成功" :value="3"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="生成时间">
                    <daterange-picker
                        v-model:startTime="queryParams.start_time"
                        v-model:endTime="queryParams.end_time"
                    />
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage"
                        >查询</el-button
                    >
                    <el-button @click="resetParams">重置</el-button>
                    <export-data
                        class="ml-2.5"
                        :fetch-fun="drawRecordsLists"
                        :params="queryParams"
                        :page-size="pager.size"
                    />
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <div class="mb-4">
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
                v-loading="pager.loading"
                :data="pager.lists"
                @selection-change="handleSelectionChange"
            >
                <el-table-column type="selection" width="55" />
                <el-table-column label="ID" prop="id" min-width="80" />
                <el-table-column label="用户信息" min-width="150">
                    <template #default="{ row }">
                        <div class="flex items-center overflow-hidden">
                            <image-contain
                                class="flex-none"
                                v-if="row.avatar"
                                :src="row.avatar"
                                :width="48"
                                :height="48"
                                :preview-src-list="[row.avatar]"
                                :preview-teleported="true"
                                :hide-on-click-modal="true"
                                fit="contain"
                            />
                            <div class="ml-4 line-clamp-2">
                                {{ row.nickname }}
                            </div>
                        </div>
                    </template>
                </el-table-column>

                <el-table-column label="用户输入" prop="prompt" min-width="280">
                    <template #default="{ row }">
                        <Popup
                            ref="popRef"
                            title="用户输入"
                            width="700px"
                            clickModalClose
                            cancelButtonText="取消"
                            confirmButtonText="确定"
                        >
                            <template #trigger>
                                <div class="line-clamp-2 cursor-pointer">
                                    {{ row.prompt }}
                                </div>
                            </template>
                            <div>{{ row.prompt }}</div>
                            <template v-if="row.prompt_en">
                                <div class="mt-[20px]">用户输入翻译：</div>
                                <div class="mt-[6px]">{{ row.prompt_en }}</div>
                            </template>
                        </Popup>
                    </template>
                </el-table-column>
                <el-table-column label="生成结果" min-width="160">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <span v-if="row.status == 1">生成中...</span>
                            <image-contain
                                class="flex-none"
                                v-else-if="row.status == 3"
                                :src="row.thumbnail"
                                :width="64"
                                :height="64"
                                :preview-src-list="[row.image]"
                                :preview-teleported="true"
                                :hide-on-click-modal="true"
                                fit="contain"
                            />
                            <span
                                v-else
                                class="text-error cursor-pointer"
                                @click="
                                    feedback.alert(
                                        '生成失败: ' + row.fail_reason,
                                        '失败原因'
                                    )
                                "
                            >
                                生成失败
                            </span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="上传图片" min-width="160">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <image-contain
                                class="flex-none"
                                v-if="row.image_base"
                                :src="row.image_base"
                                :width="64"
                                :height="64"
                                :preview-src-list="[row.image_base]"
                                :preview-teleported="true"
                                :hide-on-click-modal="true"
                                fit="contain"
                            />
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                    label="消耗电力值"
                    prop="use_tokens"
                    min-width="120"
                />
                <el-table-column label="图片审核" min-width="180">
                    <template #default="{ row }">
                        <div>
                            <el-tag
                                class="mr-2"
                                v-if="row.censor_status == 1"
                                type="success"
                            >
                                {{ row.censor_status_text }}
                            </el-tag>
                            <template v-else-if="row.censor_status >= 2">
                                <el-tag
                                    class="mr-2 cursor-pointer"
                                    type="danger"
                                >
                                    {{ row.censor_status_text }}
                                </el-tag>
                                <span
                                    class="text-error text-sm cursor-pointer"
                                    @click="openAuditDesc(row)"
                                >
                                    查看原因
                                </span>
                            </template>
                            <el-tag
                                class="mr-2"
                                v-else-if="row.censor_status == 0"
                                type="warning"
                                >{{ row.censor_status_text }}</el-tag
                            >
                            <el-tag class="mr-2" v-else type="danger">
                                {{ row.censor_status_text }}
                            </el-tag>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                    label="生成时间"
                    prop="create_time"
                    min-width="180"
                    show-tooltip-when-overflow
                />
                <el-table-column label="请求ip" prop="ip" min-width="140" />
                <el-table-column label="操作" min-width="180" fixed="right">
                    <template #default="{ row }">
                        <el-button
                            type="danger"
                            v-perms="['chat_records/del']"
                            link
                            @click="handleDelete([row.id])"
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
        <auditPop ref="auditRef"></auditPop>
    </div>
</template>
<script lang="ts" setup name="sdRecord">
import {
    drawRecordsLists,
    drawRecordsDel,
    drawModel,
} from '@/api/ai_draw/draw_records';
import { usePaging } from '@/hooks/usePaging';
import feedback from '@/utils/feedback';
import AuditPop from './auditPop.vue';

const queryParams = reactive({
    type: '',
    status: '',
    user_info: '', //用户信息
    prompt: '', //关键词
    model: "sd",
    start_time: '',
    end_time: '',
});

// 审核弹窗
const auditRef = shallowRef();
// 模型列表
const modelList = ref([]);
const multipleSelection = ref<any[]>([]);

const drawType: { label: string; value: number }[] = [
    {
        label: '文生图',
        value: 1,
    },
    {
        label: '图生图',
        value: 2,
    },
];

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: drawRecordsLists,
    params: queryParams,
});

const getDrawModel = async () => {
    try {
        modelList.value = await drawModel();
    } catch (error) {
        console.log('获取绘画模型', error);
    }
};

const handleSelectionChange = (val: any[]) => {
    multipleSelection.value = val;
};

const openAuditDesc = (record: any) => {
    auditRef.value.open(record);
};

const handleDelete = async (ids: number[]) => {
    await feedback.confirm('确定要删除？');
    await drawRecordsDel({ ids });
    await feedback.msgSuccess('删除成功');
    getLists();
};

getLists();
getDrawModel();
</script>
