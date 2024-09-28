<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="形象名称">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.name"
                        placeholder="请输入形象名称"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="创建人">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.user"
                        placeholder="请输入用户编号/昵称"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="形象状态">
                    <el-select class="w-[280px]" v-model="queryParams.is_disable">
                        <el-option label="全部" value="" />
                        <el-option label="开启" :value="0" />
                        <el-option label="关闭" :value="1" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <el-table class="mt-4" size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="ID" min-width="80" prop="id" />
                <el-table-column label="形象头像" min-width="110">
                    <template #default="{ row }">
                        <el-avatar :size="50" :src="row.avatar" />
                    </template>
                </el-table-column>
                <el-table-column label="形象封面" min-width="110">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <image-contain
                                class="flex-none"
                                v-if="row.image"
                                :src="row.image"
                                :width="80"
                                :height="40"
                                :preview-src-list="[row.image]"
                                preview-teleported
                                fit="contain"
                            />
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="形象名称" prop="name" min-width="120" />
                <el-table-column label="创建人" min-width="120">
                    <template #default="{ row }">
                        <el-popover placement="top" width="220px" trigger="hover">
                            <div class="mt-[4px]">
                                <span class="mr-4"> 昵称: </span>
                                <span>{{ row.user?.nickname }}</span>
                            </div>
                            <div class="mt-[4px]">
                                <span class="mr-4"> 编号: </span>
                                <span>{{ row.user?.sn }}</span>
                            </div>
                            <template #reference>
                                <div class="flex items-center">
                                    <div class="ml-[10px]">
                                        {{ row.user?.nickname }}
                                    </div>
                                </div>
                            </template>
                        </el-popover>
                    </template>
                </el-table-column>
                <!--                <el-table-column label="宽屏人物待机视频" min-width="150">-->
                <!--                    <template #default="{ row }">-->
                <!--                        <MaterialFile :uri="row.wide_stay_video" type="video" />-->
                <!--                    </template>-->
                <!--                </el-table-column>-->
                <!--                <el-table-column label="宽屏人物说话视频" min-width="150">-->
                <!--                    <template #default="{ row }">-->
                <!--                        <MaterialFile :uri="row.wide_talk_video" type="video" />-->
                <!--                    </template>-->
                <!--                </el-table-column>-->
                <!--                <el-table-column label="竖屏人物待机视频" min-width="150">-->
                <!--                    <template #default="{ row }">-->
                <!--                        <MaterialFile :uri="row.vertical_stay_video" type="video" />-->
                <!--                    </template>-->
                <!--                </el-table-column>-->
                <!--                <el-table-column label="竖屏人物说话视频" min-width="150">-->
                <!--                    <template #default="{ row }">-->
                <!--                        <MaterialFile :uri="row.vertical_talk_video" type="video" />-->
                <!--                    </template>-->
                <!--                </el-table-column>-->

                <el-table-column label="配音角色" prop="dubbing" min-width="100" />
                <el-table-column label="闲时回复" prop="idle_reply" min-width="150" />
                <el-table-column label="状态" min-width="100">
                    <template #default="{ row }">
                        <el-switch
                            @change="changeStatus(row.id)"
                            v-model="row.is_disable"
                            :active-value="0"
                            :inactive-value="1"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="创建时间" prop="create_time" min-width="100" />
                <el-table-column label="操作" width="120" fixed="right">
                    <template #default="{ row }">
                        <div>
                            <el-button type="primary" v-perms="['kb.digital/detail']" link>
                                <router-link
                                    :to="{
                                        path: getRoutePath('kb.digital/detail'),
                                        query: {
                                            id: row.id
                                        }
                                    }"
                                >
                                    查看
                                </router-link>
                            </el-button>
                            <el-button
                                type="danger"
                                v-perms="['kb.digital/del']"
                                @click="del(row.id)"
                                link
                            >
                                删除
                            </el-button>
                        </div>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
    </div>
</template>

<script setup lang="ts">
import { usePaging } from '@/hooks/usePaging'

import feedback from '@/utils/feedback'
import { delDigital, putDigitalStatus, getDigitalLists } from '@/api/knowledge_base/digital'
import MaterialFile from '@/components/material/file.vue'
import { getRoutePath } from '@/router'
const queryParams: any = ref({
    name: '',
    user: '',
    is_disable: ''
})

const editPopup = shallowRef()
const openPop = async () => {
    await nextTick()
    editPopup.value.open()
}

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getDigitalLists,
    params: queryParams.value
})

//修改分类状态
const changeStatus = async (id: number) => {
    await putDigitalStatus({ id })
}

//删除
const del = async (id: number) => {
    await feedback.confirm('确定删除？')
    await delDigital({ id })
    getLists()
}

onMounted(() => {
    getLists()
})
</script>

<style lang="scss" scoped></style>
