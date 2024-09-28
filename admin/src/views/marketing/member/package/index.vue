<template>
    <div class="member-package">
        <el-card shadow="never" class="!border-none">
            <el-alert
                type="warning"
                title="温馨提示：等级权重数值越大等级就越高"
                :closable="false"
                show-icon
            />

            <div class="text-xl font-medium my-[20px]">会员功能</div>
            <el-form ref="formRef" :model="formData" label-width="84px">
                <el-form-item label="状态功能" prop="name">
                    <div>
                        <el-radio-group v-model="formData.is_open" class="ml-4">
                            <el-radio :label="1">开启</el-radio>
                            <el-radio :label="0">关闭</el-radio>
                        </el-radio-group>
                        <div class="form-tips">关闭后，移动端和PC端的会员入口将不会显示</div>
                    </div>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card shadow="never" class="!border-none mt-4">
            <router-link
                v-perms="['member.memberPackage/add:edit', 'member.memberPackage/add']"
                :to="getRoutePath('member.memberPackage/add:edit')"
            >
                <el-button type="primary" class="mb-[10px]"> 新增会员等级 </el-button>
            </router-link>

            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="等级名称" prop="name" min-width="100" />
                <el-table-column label="等级描述" prop="describe" min-width="100" />
                <el-table-column label="等级人数" prop="package_num" min-width="100" />
                <el-table-column label="等级权重" min-width="120">
                    <template #default="{ row }">
                        <popover-input
                            class="ml-[10px]"
                            :limit="32"
                            v-perms="['member.memberPackage/sort']"
                            @confirm="handleSort($event, row.id)"
                        >
                            <el-button type="primary" link>
                                {{ row.sort }}
                                <icon name="el-icon-EditPen" />
                            </el-button>
                        </popover-input>
                    </template>
                </el-table-column>
                <el-table-column
                    label="是否推荐"
                    min-width="120"
                    v-perms="['member.memberPackage/sort']"
                >
                    <template #default="{ row }">
                        <el-switch
                            :active-value="1"
                            :inactive-value="0"
                            v-model="row.is_recommend"
                            @click="handleRecommend(row.id)"
                        ></el-switch>
                    </template>
                </el-table-column>
                <el-table-column
                    label="是否上架"
                    min-width="120"
                    v-perms="['member.memberPackage/status']"
                >
                    <template #default="{ row }">
                        <el-switch
                            :active-value="1"
                            :inactive-value="0"
                            v-model="row.status"
                            @click="handleUpdate(row.id)"
                        ></el-switch>
                    </template>
                </el-table-column>
                <el-table-column label="操作" width="120" fixed="right">
                    <template #default="{ row }">
                        <el-button type="primary" link>
                            <router-link
                                v-perms="[
                                    'member.memberPackage/add:edit',
                                    'member.memberPackage/edit'
                                ]"
                                :to="{
                                    path: getRoutePath('member.memberPackage/add:edit'),
                                    query: {
                                        id: row.id
                                    }
                                }"
                            >
                                编辑
                            </router-link>
                        </el-button>
                        <el-button
                            v-perms="['member.memberPackage/del']"
                            type="danger"
                            link
                            @click="handleDel(row.id)"
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

        <footer-btns v-perms="['member.MemberPackage/setConfig']">
            <el-button type="primary" @click="setData">保存</el-button>
        </footer-btns>
    </div>
</template>
<script setup lang="ts">
import { usePaging } from '@/hooks/usePaging'
import {
    getMemberConfig,
    setMemberConfig,
    memberLists,
    memberDel,
    updateStatus,
    updateSort,
    updateRecommend
} from '@/api/marketing/member'
import feedback from '@/utils/feedback'
import router, { getRoutePath } from '@/router'

const formData = reactive({
    is_open: 1 as 0 | 1
})

/**
 * 获取初始化数据配置
 */
const getData = async () => {
    const res = await getMemberConfig()
    formData.is_open = res.is_open
}
getData()

/**
 * 设置初始化数据配置
 */
 const setData = async () => {
    await setMemberConfig({
        is_open: formData.is_open
    })
}

/**
 * 获取初始化数据列表
 */
const queryParams = reactive({})
const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: memberLists,
    params: queryParams
})
getLists()

/**
 * 删除数据
 */
const handleDel = async (id: number) => {
    await feedback.confirm('确认删除？')
    await memberDel({ id })
    getLists()
}

/**
 * 排序
 */
const handleSort = async (value: number, id: number) => {
    await updateSort({ sort: value, id })
    getLists()
    console.log(value)
}

/**
 * 上架
 */
const handleUpdate = async (id: number) => {
    await updateStatus({ id })
    getLists()
}

/**
 * 推荐
 */
const handleRecommend = async (id: number) => {
    await updateRecommend({ id })
    getLists()
}
</script>
