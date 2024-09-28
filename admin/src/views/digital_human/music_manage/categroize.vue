<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="分类名称">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.name"
                        placeholder="请输入分类名称"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="状态">
                    <el-select class="w-[280px]" v-model="queryParams.status">
                        <el-option label="开启" value="1" />
                        <el-option label="关闭" value="0" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <el-button type="primary" v-perms="['digital.musicCategory/add']" @click="openPop"
                >+ 新增分类</el-button
            >
            <el-table class="mt-2" size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="分类名称" prop="name" min-width="120" />
                <el-table-column label="关联音乐数" prop="music_count" min-width="100" />
                <el-table-column label="状态" min-width="100">
                    <template #default="{ row }">
                        <el-switch
                            v-perms="['digital.musicCategory/status']"
                            @change="changStatus(row.id)"
                            v-model="row.status"
                            :active-value="1"
                            :inactive-value="0"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="排序" prop="sort" min-width="100" />

                <el-table-column label="创建时间" prop="create_time" min-width="120" />
                <el-table-column label="操作" width="120" fixed="right">
                    <template #default="{ row }">
                        <div>
                            <el-button
                                type="primary"
                                v-perms="['digital.musicCategory/edit']"
                                link
                                @click="openEdit(row)"
                            >
                                编辑
                            </el-button>
                            <el-button
                                type="danger"
                                v-perms="['digital.musicCategory/del']"
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
        <add-classify
            v-if="popShow"
            @close="popShow = false"
            @success="
                () => {
                    getLists()
                    popShow = false
                }
            "
            ref="classPop"
        />
    </div>
</template>

<script setup lang="ts">
import { usePaging } from '@/hooks/usePaging'
import { ref } from 'vue'
import AddClassify from './components/addClassify.vue'
import { getMusicCategoryList, changCategoryStatus, delCategory } from '@/api/digital_human/music'
import feedback from '@/utils/feedback'

const classPop = shallowRef()

const queryParams: any = ref({
    name: '',
    status: ''
})

const popShow = ref(false)
const openPop = async () => {
    popShow.value = true
    await nextTick()
    classPop.value.open()
}

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getMusicCategoryList,
    params: queryParams.value
})

//修改分类状态
const changStatus = async (id: number) => {
    await changCategoryStatus({ id })
}

//打开编辑
const openEdit = async (row: any) => {
    popShow.value = true
    await nextTick()
    classPop.value.open(row)
}

//删除
const del = async (id: number) => {
    await feedback.confirm('确定删除？')
    await delCategory({ id })
    getLists()
}

onMounted(() => {
    getLists()
})
</script>

<style lang="scss" scoped></style>
