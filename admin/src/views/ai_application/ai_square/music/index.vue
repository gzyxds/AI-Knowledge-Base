<template>
    <div>
        <el-card class="!border-none" shadow="never">
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
                        v-model="queryParams.keyword"
                        placeholder="请输入关键词"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="所属分类">
                    <el-select class="w-[280px]" v-model="queryParams.category_id">
                        <el-option label="全部" value=""/>
                        <el-option
                            v-for="item in catePager.lists"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="审核状态">
                    <el-select class="w-[280px]" v-model="queryParams.verify_status">
                        <el-option label="全部" value=""/>
                        <el-option label="待审核" :value="0"/>
                        <el-option label="审核通过" :value="1"/>
                        <el-option label="审核不通过" :value="2"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="是否显示">
                    <el-select class="w-[280px]" v-model="queryParams.is_show">
                        <el-option label="全部" value=""/>
                        <el-option label="显示" :value="1"/>
                        <el-option label="隐藏" :value="0"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="添加来源">
                    <el-select class="w-[280px]" v-model="queryParams.source">
                        <el-option label="全部" value=""/>
                        <el-option label="后台添加" :value="1"/>
                        <el-option label="前台用户" :value="2"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="创建时间">
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
            <div class="mb-4">
<!--                <el-button-->
<!--                    v-perms="['music.musicSquare/add']"-->
<!--                    type="primary"-->
<!--                    @click="handleAdd"-->
<!--                >-->
<!--                    <template #icon>-->
<!--                        <icon name="el-icon-Plus"/>-->
<!--                    </template>-->
<!--                    新增音乐-->
<!--                </el-button>-->
                <el-button
                    v-perms="['music.musicSquare/verifyStatus']"
                    type="default"
                    :plain="true"
                    :disabled="!multipleSelection.length"
                    @click="handleAudit(multipleSelection.map((item) => item.id))"
                >
                    批量审核
                </el-button>
                <el-button
                    v-perms="['music.musicSquare/removeCategory']"
                    type="default"
                    :plain="true"
                    :disabled="!multipleSelection.length"
                    @click="handleTransfer(multipleSelection.map((item) => item.id))"
                >
                    移动分类
                </el-button>
                <el-button
                    v-perms="['music.musicSquare/del']"
                    type="default"
                    :plain="true"
                    :disabled="!multipleSelection.length"
                    @click="handleDelete(multipleSelection.map((item) => item.id))"
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
                <el-table-column type="selection" width="55"/>
                <el-table-column label="歌曲封面" width="100">
                    <template #default="{ row }">
                        <ImageContain
                            class="flex-none"
                            v-if="row.image_url"
                            :src="row.image_url"
                            preview-teleported
                            :preview-src-list="[row.image_url]"
                            :width="58"
                            :height="58"
                            fit="contain"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="歌曲名称" min-width="180">
                    <template #default="{ row }">
                        <MusicItem :name="row.title" :url="row.audio_url"/>
                    </template>
                </el-table-column>
                <el-table-column label="歌词" min-width="180">
                    <template #default="{ row }">
                        <div
                            class="line-clamp-3 cursor-pointer"
                            @click="openTips(row.lyric, '歌词')"
                        >
                            {{ row.lyric }}
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="所属分类" prop="category_name" min-width="120"/>
                <el-table-column label="添加来源" prop="source_desc" min-width="120"/>
                <el-table-column label="用户信息" min-width="200">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <image-contain
                                class="flex-none"
                                v-if="row?.user_info.image"
                                :src="row?.user_info.image"
                                :width="48"
                                :height="48"
                                :preview-src-list="[row?.user_info.image]"
                                :preview-teleported="true"
                                :hide-on-click-modal="true"
                                fit="contain"
                            />
                            <span class="ml-4">{{ row?.user_info.name }}</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="审核状态" min-width="140">
                    <template #default="{ row }">
                        <div>
                            <el-tag class="ml-2" v-if="row.verify_status == 1" type="success">
                                {{ row.verify_status_desc }}
                            </el-tag>
                            <el-tag class="ml-2" v-else-if="row.verify_status == 0" type="warning">
                                {{ row.verify_status_desc }}
                            </el-tag>
                            <el-tag
                                class="ml-2 cursor-pointer"
                                v-else
                                type="danger"
                                @click="handleReply(row.verify_result)"
                            >
                                {{ row.verify_status_desc }}
                            </el-tag>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                    label="是否显示"
                    min-width="100"
                    v-perms="['music.musicSquare/isShow']"
                >
                    <template #default="{ row }">
                        <el-switch
                            @change="changeStatus(row.id)"
                            v-model="row.is_show"
                            :active-value="1"
                            :inactive-value="0"
                        />
                    </template>
                </el-table-column>
                <el-table-column
                    label="创建时间"
                    prop="create_time" sortable
                    min-width="180"
                    show-tooltip-when-overflow
                />
                <el-table-column label="操作" min-width="180" fixed="right">
                    <template #default="{ row }">
                        <el-button
                            v-perms="['music.musicSquare/edit']"
                            type="primary"
                            link
                            @click="handleEdit(row.id)"
                        >
                            编辑
                        </el-button>
                        <el-button
                            v-if="!row.verify_status"
                            v-perms="['music.musicSquare/verifyStatus']"
                            type="primary"
                            link
                            @click="handleAudit([row.id])"
                        >
                            审核
                        </el-button>
                        <el-button
                            type="danger"
                            v-perms="['music.musicSquare/del']"
                            link
                            @click="handleDelete([row.id])"
                        >
                            删除
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists"/>
            </div>
        </el-card>
        <EditPopup
            v-if="showEdit"
            ref="editRef"
            @success="getLists"
            @close="showEdit = false"
        ></EditPopup>
        <AuditPopup
            v-if="showAudit"
            ref="auditRef"
            @success="getLists"
            @close="showAudit = false"
        ></AuditPopup>
        <ReplyPopup
            v-if="showReply"
            ref="replyRef"
            @success="getLists"
            @close="showReply = false"
        ></ReplyPopup>
        <TransferCatePopup
            v-if="showTransfer"
            :type="2"
            ref="transferRef"
            @success="getLists"
            @close="showTransfer = false"
        ></TransferCatePopup>
    </div>
</template>
<script lang="ts" setup name="musicSquareLists">
import {
    getMusicSquareList,
    delMusicSquare,
    editMusicSquareStatus,
    getSquareCategoryAll
} from '@/api/ai_application/ai_square'
import {usePaging} from '@/hooks/usePaging'
import feedback from '@/utils/feedback'

import EditPopup from './edit.vue'
import AuditPopup from './audit.vue'
import ReplyPopup from './reply.vue'
import MusicItem from "@/views/ai_application/music/record/components/music-item.vue";
import TransferCatePopup from '../_components/transfer-cate.vue'

const queryParams = reactive({
    verify_status: '',
    user_info: '', //用户信息
    keyword: '', //关键词
    category_id: '',
    is_show: '',
    source: '',
    start_time: '',
    end_time: ''
})
const catePager = reactive({
    loading: true,
    lists: []
})

//弹框ref
const editRef = shallowRef()
const auditRef = shallowRef()
const replyRef = shallowRef()
const transferRef = shallowRef()
const showEdit = ref<boolean>(false)
const showAudit = ref<boolean>(false)
const showReply = ref<boolean>(false)
const showTransfer = ref<boolean>(false)
const multipleSelection = ref<any[]>([])

const {pager, getLists, resetPage, resetParams} = usePaging({
    fetchFun: getMusicSquareList,
    params: queryParams
})

const getData = async () => {
    catePager.loading = true
    try {
        const  lists = await getSquareCategoryAll({
            type: 2
        })
        catePager.lists = lists
        catePager.loading = false
    } catch (error) {
        catePager.loading = false
        console.log('获取绘画分类失败=>', error)
    }
}

const handleSelectionChange = (val: any[]) => {
    multipleSelection.value = val
}

const openTips = (content: string, title: string) => {
    feedback.confirm(content, title, {
        showCancelButton: false,
        type: '',
        customStyle: {'max-width': '550px'}
    })
}

//添加
const handleAdd = async () => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open('add')
}
//编辑
const handleEdit = async (id: number) => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open('edit', id)
}

//修改状态
const changeStatus = async (id: any) => {
    try {
        await editMusicSquareStatus({id})
    } catch (error) {
        console.log('修改状态失败=>', error)
    }
}

// 审核
const handleAudit = async (ids: number[]) => {
    showAudit.value = true
    await nextTick()
    auditRef.value?.open(ids)
}

// 查看审核原因
const handleReply = async (result: string) => {
    showReply.value = true
    await nextTick()
    replyRef.value?.open(result)
}

const handleTransfer = async (ids: number[]) => {
    showTransfer.value = true
    await nextTick()
    transferRef.value?.open(ids)
}

// 删除
const handleDelete = async (id: number[]) => {
    await feedback.confirm('确定要删除？')
    await delMusicSquare({id})
    getLists()
}

getData()
getLists()
</script>
