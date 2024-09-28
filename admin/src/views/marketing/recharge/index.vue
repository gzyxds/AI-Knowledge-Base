<template>
    <div>
        <el-card shadow="never" class="!border-none">
            <div class="text-xl font-medium mb-[20px]">充值功能</div>
            <el-form ref="formRef" :model="pagerData" label-width="84px">
                <el-form-item label="状态功能" prop="name">
                    <div>
                        <el-radio-group v-model="pagerData.rechargeStatus" class="ml-4">
                            <el-radio :label="1">开启</el-radio>
                            <el-radio :label="0">关闭</el-radio>
                        </el-radio-group>
                        <div class="form-tips">关闭后，移动端和PC端的充值入口将不会显示</div>
                    </div>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card shadow="never" class="!border-none mt-4">
            <div v-perms="['recharge.package/add', 'recharge.package/add:edit']">
                <router-link :to="getRoutePath('recharge.package/add:edit')">
                    <el-button type="primary">
                        <template #icon>
                            <icon name="el-icon-Plus"/>
                        </template>
                        新增充值套餐
                    </el-button>
                </router-link>
            </div>

            <el-table size="large" :data="pager.lists" class="mt-4">
                <el-table-column label="套餐名称" min-width="120" prop="name"/>
                <el-table-column label="套餐价格" min-width="120">
                    <template #default="{ row }"> ¥{{ row.sell_price }}</template>
                </el-table-column>
                <el-table-column label="套餐状态" min-width="100">
                    <template #default="{ row }">
                        <el-switch
                            :active-value="1"
                            :inactive-value="0"
                            v-model="row.status"
                            @change="changeStatus($event, 'status', row.id)"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="是否推荐" min-width="100">
                    <template #default="{ row }">
                        <el-switch
                            :active-value="1"
                            :inactive-value="0"
                            v-model="row.is_recommend"
                            @change="changeStatus($event, 'is_recommend', row.id)"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="套餐内容" min-width="150">
                    <template #default="{ row }">
                        <template v-if="row.chat_balance > 0 || row.robot_number > 0">
                            <div v-if="row.chat_balance > 0">电力值数量：{{ row.chat_balance }}</div>
                            <div v-if="row.robot_number > 0">智能体数量：{{ row.robot_number }}</div>
                            <!-- <div v-if="row.video_duration > 0">
                                形象时长：{{ row.video_duration }}
                            </div> -->
                        </template>
                        <div v-else>-</div>
                    </template>
                </el-table-column>

                <el-table-column label="额外赠送" min-width="100">
                    <template #default="{ row }">
                        <el-tag v-if="row.is_give == 1" type="success">开启</el-tag>
                        <el-tag v-if="row.is_give == 0" type="danger">关闭</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="赠送内容" min-width="150">
                    <template #default="{ row }">
                        <template v-if="row.give_chat_balance > 0 || row.give_robot_number > 0">
                            <div v-if="row.give_chat_balance > 0">
                                赠送电力值：{{ row.give_chat_balance }}
                            </div>
                            <div v-if="row.give_robot_number > 0">
                                智能体数量：{{ row.give_robot_number }}
                            </div>
                            <!-- <div v-if="row.give_video_duration > 0">
                                形象时长：{{ row.give_video_duration }}
                            </div> -->
                        </template>
                        <div v-else>-</div>
                    </template>
                </el-table-column>
                <el-table-column label="排序" min-width="120">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <span>{{ row.sort }}</span>
                            <popover-input
                                :value="row.sort"
                                class="ml-[10px]"
                                @confirm="handleSort($event, row.id)"
                            >
                                <el-button link type="primary">
                                    <icon name="el-icon-EditPen" class="ml-1"/>
                                </el-button>
                            </popover-input>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="操作" width="120" fixed="right">
                    <template #default="{ row }">
                        <el-button type="primary" link>
                            <router-link
                                v-perms="['recharge.package/edit', 'recharge.package/add:edit']"
                                :to="{
                                path: getRoutePath('recharge.package/add:edit'),
                                query: {
                                    id: row.id,
                                    mode: 'edit'
                                }
                            }"
                            >
                                编辑
                            </router-link>
                        </el-button>
                        <el-button
                            v-perms="['recharge.package/del']"
                            type="danger"
                            link
                            @click="handleDelete(row.id)"
                        >
                            删除
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-card>
        <footer-btns v-perms="['recharge.package/setConfig']">
            <el-button type="primary" @click="handleSave">保存</el-button>
        </footer-btns>
    </div>
</template>
<script setup lang="ts">
import {
    getRechargeConfig,
    setRechargeConfig,
    getRechargeLists,
    rechargeStatus,
    rechargeDelete,
    rechargeSort
} from '@/api/marketing/recharge'

import {usePaging} from '@/hooks/usePaging'
import {getRoutePath} from '@/router'
import feedback from '@/utils/feedback'

const pagerData = ref({
    rechargeStatus: 1
})
const getData = async () => {
    pagerData.value = await getRechargeConfig()
}

const handleSave = async () => {
    await setRechargeConfig(pagerData.value)
    getData()
}
const {pager, getLists} = usePaging({
    fetchFun: getRechargeLists
})

//修改状态
const changeStatus = async (value: any, field: string, id: any) => {
    await rechargeStatus({id, field, value})
    getLists()
}

//修改排序
const handleSort = async (value: string, id: number | string) => {
    await rechargeSort({value, id})
    getLists()
}

//删除
const handleDelete = async (id: number) => {
    await feedback.confirm('确定要删除？')
    await rechargeDelete({id})
    getLists()
}

getData()
getLists()
</script>
