<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="用户信息">
                    <el-input
                        class="w-[280px]"
                        placeholder="用户编号/昵称/手机号码/邮箱"
                        clearable
                        v-model="formData.user_info"
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="文件名称">
                    <el-input
                        class="w-[280px]"
                        placeholder="请输入"
                        clearable
                        @keyup.enter="resetPage"
                        v-model="formData.name"
                    />
                </el-form-item>
                <el-form-item label="生成状态">
                    <el-select class="w-[280px]" v-model="formData.status">
                        <el-option label="全部" value />
                        <el-option label="草稿" value="1" />
                        <el-option label="合成中" value="2" />
                        <el-option label="合成成功" value="3" />
                        <el-option label="合成失败" value="4" />
                    </el-select>
                </el-form-item>
                <el-form-item label="生成时间">
                    <daterange-picker
                        v-model:start-time="formData.start_time"
                        v-model:end-time="formData.end_time"
                    />
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <el-button :disabled="!btnStatus" @click="batchDel">批量删除</el-button>
            <el-table
                class="mt-2"
                size="large"
                v-loading="pager.loading"
                :data="pager.lists"
                ref="tableRef"
                @selection-change="handleSelectionChange"
            >
                <el-table-column type="selection" width="55" />
                <el-table-column label="ID" prop="id" min-width="80" />
                <el-table-column label="用户信息" min-width="200">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <el-avatar class="flex-none" :src="row.avatar" :size="50" />
                            <div class="ml-2">{{ row.nickname }}</div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="文件名称" min-width="210">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <file
                                @click="previewVideo(row.video_url)"
                                class="cursor-pointer"
                                type="video"
                                :uri="row.video_url"
                            />
                            <div class="ml-2">{{ row.name }}</div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="时长" prop="long_time_desc" min-width="120" />
                <el-table-column label="生成状态" prop="status" min-width="100">
                    <template #default="{ row }">
                        <div>
                            <el-tag v-if="row.status == 1" type="info">草稿</el-tag>
                            <el-tag v-if="row.status == 2">合成中</el-tag>
                            <el-tag v-if="row.status == 3" type="success"> 合成成功 </el-tag>
                            <el-tag
                                v-if="row.status == 4"
                                class="cursor-pointer"
                                type="danger"
                                @click="showFail(row.fail_reason)"
                            >
                                合成失败
                            </el-tag>
                        </div>
                    </template>
                </el-table-column>

                <el-table-column label="生成时间" prop="create_time" min-width="120" />
                <el-table-column label="操作" width="100" fixed="right">
                    <template #default="{ row }">
                        <div>
                            <el-button @click="del(row.id)" type="danger" link>删除</el-button>
                        </div>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
        <material-preview v-model="previewStatus" :url="previewUrl" type="video" />
    </div>
</template>

<script setup lang="ts">
import { getRecordList, delRecord } from '@/api/digital_human/manage'
import { usePaging } from '@/hooks/usePaging'
import file from '@/components/material/file.vue'
import { ref } from 'vue'
import feedback from '@/utils/feedback'

const formData = ref({
    user_info: '',
    name: '',
    status: '',
    start_time: '',
    end_time: ''
})

const previewStatus = ref(false)
const queryParams: any = ref()

const previewUrl = ref('')

//按钮状态
const btnStatus = ref(false)

//表格ref
const tableRef = shallowRef()

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getRecordList,
    params: formData.value
})

//预览视频
const previewVideo = async (url: string) => {
    previewStatus.value = true
    // await nextTick()
    previewUrl.value = url
}

//删除
const del = async (id: number) => {
    await feedback.confirm('确认删除？')
    await delRecord({ ids: [id] })
    getLists()
}

const batchDel = async () => {
    const ids = tableRef.value.getSelectionRows().map((item: any) => {
        return item.id
    })
    await feedback.confirm('是否确认批量删除！')
    await delRecord({ ids })
    getLists()
}

const handleSelectionChange = (value: any[]) => {
    console.log(value)
    btnStatus.value = value.length != 0
}

const showFail = (reason: string) => {
    feedback.confirm(`错误信息：${reason}`, '失败原因', {
        showConfirmButton: false,
        type: 'error',
        cancelButtonText: '关闭'
    })
}

onMounted(() => {
    getLists()
})
</script>

<style lang="scss" scoped></style>
