<template>
    <div>
        <el-card shadow="never" class="!border-none">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="用户信息">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.user_info"
                        placeholder="请输入用户ID/用户昵称"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="关键词">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.prompt"
                        placeholder="请输入关键词"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="生成结果">
                    <el-select class="!w-[280px]" v-model="queryParams.status">
                        <el-option label="全部" :value="-1" />
                        <el-option
                            v-for="(item, index) in options.status"
                            :label="item"
                            :value="index"
                            :key="index"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="生成类型">
                    <el-select class="!w-[280px]" v-model="queryParams.type">
                        <el-option label="全部" :value="''" />
                        <el-option
                            v-for="(item, index) in options.type"
                            :label="item"
                            :value="index"
                            :key="index"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="视频风格">
                    <el-select class="!w-[280px]" v-model="queryParams.style_id">
                        <el-option label="全部" :value="''" />
                        <el-option
                            v-for="(item, index) in options.style"
                            :label="item.name"
                            :value="item.id"
                            :key="index"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="音乐渠道">
                    <el-select class="!w-[280px]" v-model="queryParams.channel">
                        <el-option label="全部" :value="''" />
                        <el-option
                            v-for="(item, index) in options.channel"
                            :label="item"
                            :value="index"
                            :key="index"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="生成时间">
                    <daterange-picker
                        v-model:startTime="queryParams.start_time"
                        v-model:endTime="queryParams.end_time"
                    />
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <el-button
                v-perms="['video.videoRecord/del']"
                class="mb-4"
                @click="handleDelete(selectData)"
                :disabled="!selectData.length"
            >
                批量删除
            </el-button>
            <el-table
                size="large"
                v-loading="pager.loading"
                :data="pager.lists"
                row-key="id"
                @selection-change="handleSelectionChange"
            >
                <el-table-column type="selection" width="55" />
                <el-table-column label="用户昵称" min-width="180">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <ImageContain
                                class="flex-none rounded-[50%]"
                                v-if="row.avatar"
                                :src="row.avatar"
                                :width="48"
                                :height="48"
                                fit="contain"
                            />
                            <div class="line-clamp-2 ml-[10px]">{{ row.nickname }}</div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="用户输入" min-width="180">
                    <template #default="{ row }">
                        <div
                            class="line-clamp-3 cursor-pointer"
                            @click="openTips(row.prompt, '用户输入')"
                        >
                            {{ row.prompt }}
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="参考图" width="100">
                    <template #default="{ row }">
                        <ImageContain
                            class="flex-none"
                            v-if="row.image"
                            :src="row.image"
                            preview-teleported
                            :preview-src-list="[row.image]"
                            :width="62"
                            :height="62"
                            fit="contain"
                        />
                    </template>
                </el-table-column>

                <el-table-column label="生成结果" min-width="180">
                    <template #default="{ row }">
                        <el-tag v-if="row.status == 0" type="info">{{ row.status_desc }}</el-tag>
                        <el-tag v-if="row.status == 1" type="warning">{{ row.status_desc }}</el-tag>
                        <material-file
                            v-if="row.status == 2"
                            file-size="100px"
                            class="cursor-pointer"
                            type="video"
                            :uri="row.video_url"
                            @click="handlePreview(row.video_url)"
                        />

                        <el-tag v-if="row.status == 3" type="danger">{{ row.status_desc }}</el-tag>
                        <el-button
                            v-if="row.status == 3 && row.fail_reason"
                            type="danger"
                            :link="true"
                            size="small"
                            @click="openTips(`错误信息：${row.fail_reason}`, '错误原因')"
                        >
                            查看原因
                        </el-button>
                    </template>
                </el-table-column>
                <el-table-column label="渠道" min-width="120">
                    <template #default="{ row }">
                        <div>{{ row.channel || '-' }}</div>
                    </template>
                </el-table-column>
                <el-table-column label="视频风格" min-width="100">
                    <template #default="{ row }">
                        <div>{{ row.style_desc || '-' }}</div>
                    </template>
                </el-table-column>
                <el-table-column label="消耗电力值" min-width="120" prop="tokens" />
                <el-table-column label="创建时间" prop="create_time" min-width="180" />
                <el-table-column label="请求ip" prop="ip" min-width="140" />
                <el-table-column label="操作" width="100" fixed="right">
                    <template #default="{ row }">
                        <el-button
                            v-perms="['video.videoRecord/del']"
                            type="danger"
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
        <material-preview v-model="previewState.show" type="video" :url="previewState.url" />
    </div>
</template>
<script setup lang="ts" name="videoRecord">
import { usePaging } from '@/hooks/usePaging'
import {
    getVideoRecord,
    deleteVideoRecord,
    getVideoRecordOptions
} from '@/api/ai_application/video'
import feedback from '@/utils/feedback'
import { reactive } from 'vue'

//搜索参数
const queryParams = reactive({
    user_info: '',
    prompt: '',
    status: -1,
    style_id: '',
    type: '',
    start_time: '',
    end_time: '',
    channel: ''
})

const selectData = ref<any[]>([])
const handleSelectionChange = (val: any[]) => {
    selectData.value = val.map((item) => item.id)
}

const previewState = reactive({
    show: false,
    url: ''
})

const handlePreview = (url: string) => {
    previewState.url = url
    setTimeout(() => {
        previewState.show = true
    })
}

//删除
const handleDelete = async (id: number[]) => {
    await feedback.confirm('确定要删除？')
    await deleteVideoRecord({ id })
    getLists()
}

const openTips = (content: string, title: string) => {
    feedback.confirm(content, title, {
        showCancelButton: false,
        type: '',
        customStyle: { 'max-width': '550px' }
    })
}

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getVideoRecord,
    params: queryParams
})
const options = ref({
    style: [] as any[],
    status: [],
    type: [],
    channel: []
})
const getOptions = async () => {
    options.value = await getVideoRecordOptions()
}
getOptions()
getLists()
</script>
