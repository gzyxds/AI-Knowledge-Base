<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="用户信息">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.keyword"
                        placeholder="用户编号/昵称/手机号码/邮箱"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="注册时间">
                    <daterange-picker
                        v-model:startTime="queryParams.create_time_start"
                        v-model:endTime="queryParams.create_time_end"
                    />
                </el-form-item>
                <el-form-item label="注册来源">
                    <el-select class="w-[280px]" v-model="queryParams.channel">
                        <el-option
                            v-for="(item, key) in ClientMap"
                            :key="key"
                            :label="item"
                            :value="key"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="用户分组">
                    <el-select v-model="queryParams.group_id" class="w-[280px]">
                        <el-option
                            v-for="(item, index) in optionsData.dataList"
                            :key="index"
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                    <!-- <export-data
                        class="ml-2.5"
                        :fetch-fun="getUserList"
                        :params="queryParams"
                        :page-size="pager.size"
                    /> -->
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <el-button type="primary" @click="addUser">+ 创建用户</el-button>
            <el-button @click="setGroup" :disabled="isSelect.length == 0">批量设置分组</el-button>
            <el-table
                class="mt-2"
                size="large"
                v-loading="pager.loading"
                :data="pager.lists"
                @selection-change="handleSelectionChange"
            >
                <el-table-column type="selection" width="55" />
                <el-table-column label="用户编号" prop="sn" min-width="120" />
                <el-table-column label="头像" min-width="100">
                    <template #default="{ row }">
                        <el-avatar :src="row.avatar" :size="50" />
                    </template>
                </el-table-column>
                <el-table-column label="昵称" prop="nickname" min-width="100" />
                <el-table-column label="手机号码" prop="mobile" min-width="100" />
                <el-table-column label="邮箱" prop="email" min-width="100" />
                <el-table-column label="上级邀请人" prop="inviter_name" min-width="140">
                    <template #default="{ row }">
                        {{ row.inviter_id ? row.inviter_name : '系统'  }}
                    </template>
                </el-table-column>
                <el-table-column label="余额" min-width="160">
                    <template #default="{ row }">
                        <div>电力值数量：{{ row.balance }}</div>
                        <div>智能体：{{ row.robot_num }}</div>
                    </template>
                </el-table-column>
                <el-table-column label="会员" min-width="100">
                    <template #default="{ row }">
                        <div class="text-[#4073FA]">{{ row.package_name }}</div>
                    </template>
                </el-table-column>
                <el-table-column label="到期时间" prop="package_time" min-width="180">
                    <template #default="{ row }">
                        <div>{{ row.package_time }}</div>
                         <div v-if="row.package_is_overdue" class="text-[#FBAE00]">已到期</div>
                    </template>
                </el-table-column>
                <!-- <el-table-column label="形象余额" min-width="120">
                    <template #default="{ row }">
                        <div>{{ row.video_num }}</div>
                    </template>
                </el-table-column> -->
                <el-table-column label="累计提问" prop="total_chat" min-width="100" />
                <el-table-column label="注册来源" prop="channel" min-width="100" />
                <el-table-column label="注册时间" prop="create_time" min-width="120" />
                <el-table-column label="操作" min-width="180" fixed="right">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <el-button v-perms="['user.user/detail']" type="primary" link>
                                <router-link
                                    :to="{
                                        path: getRoutePath('user.user/detail'),
                                        query: {
                                            id: row.id
                                        }
                                    }"
                                >
                                    详情
                                </router-link>
                            </el-button>
                            <el-button
                                v-if="row.is_blacklist == 0"
                                type="primary"
                                link
                                @click="BlackList(1, row.id, row.nickname)"
                                >加入黑名单</el-button
                            >
                            <el-button
                                v-if="row.is_blacklist == 1"
                                type="warning"
                                link
                                @click="BlackList(2, row.id, row.nickname)"
                                >移出黑名单</el-button
                            >
                        </div>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
        <add-user-pop v-if="popShow" ref="addUserPopRef" @close="popClose" />
        <adjust-group v-if="popShow" ref="adjustGroupRef" @close="popClose" />
    </div>
</template>
<script lang="ts" setup name="consumerLists">
import { usePaging } from '@/hooks/usePaging'
import { getRoutePath } from '@/router'
import { getUserList, blackList, userGroupingList } from '@/api/consumer'
import { useDictOptions } from '@/hooks/useDictOptions'
import { ClientMap } from '@/enums/appEnums'
import addUserPop from '../components/addUserPop.vue'
import feedback from '@/utils/feedback'
import AdjustGroup from '../components/adjust-group.vue'

//弹框ref
const addUserPopRef = shallowRef()
const adjustGroupRef = shallowRef()
const popShow = ref(false)

const queryParams = reactive({
    keyword: '',
    channel: '',
    create_time_start: '',
    create_time_end: '',
    group_id: ''
})

const isSelect = ref<number[]>([])

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getUserList,
    params: queryParams
})
//打开创建用户弹框
const addUser = async () => {
    popShow.value = true
    await nextTick()
    addUserPopRef.value.open()
}

//调整分组
const setGroup = async () => {
    popShow.value = true
    await nextTick()
    adjustGroupRef.value.open({ id: isSelect.value, groupIds: [], type: 2 })
}

//黑名单
const BlackList = async (type: number, id: number, nickname: string) => {
    await feedback.customConfirm(
        '是否将 ',
        ` ${type == 1 ? '加入' : '移出'}黑名单？请谨慎操作！`,
        nickname,
        'color:red'
    )
    await blackList({ id })
    getLists()
}

//多选
const handleSelectionChange = (value: any) => {
    isSelect.value = value.map((item: any) => item.id)
}

const { optionsData, refresh } = useDictOptions<{ dataList: any }>({
    dataList: {
        api: userGroupingList
    }
})

//弹框关闭
const popClose = () => {
    getLists()
    popShow.value = false
}

onActivated(() => {
    getLists()
})

getLists()
</script>
