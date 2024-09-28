<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-page-header content="用户详情" @back="$router.back()" />
        </el-card>
        <el-card class="mt-4 !border-none" header="基本资料" shadow="never">
            <el-form ref="formRef" class="ls-form" :model="formData" label-width="120px">
                <div class="bg-page flex py-2.5 mb-10 items-center flex-wrap">
                    <div
                        class="basis-40 flex flex-1 flex-col py-[10px] justify-center items-center"
                    >
                        <div class="mb-2 text-tx-regular">用户头像</div>
                        <el-avatar :src="formData.avatar" :size="58" />
                    </div>
                    <div
                        class="basis-40 flex flex-1 flex-col py-[10px] justify-center items-center"
                    >
                        <div class="text-tx-regular">电力值数量</div>
                        <div class="mt-2 flex items-center text-[20px]">
                            {{ formData.balance || 0 }}
                            <el-button
                                v-perms="['user.user/adjustAccount']"
                                type="primary"
                                link
                                @click="handleAdjust('chat', formData.balance)"
                            >
                                调整
                            </el-button>
                        </div>
                    </div>
                    <div
                        class="basis-40 flex-1 flex flex-col py-[10px] justify-center items-center"
                    >
                        <div class="text-tx-regular">已使用/智能体总数</div>
                        <div class="mt-2 flex items-center text-[20px]">
                            {{ formData.use_robot_num }}/{{ formData.total_robot_num }}
                            <el-button
                                v-perms="['user.user/adjustAccount']"
                                type="primary"
                                link
                                @click="
                                    handleAdjustNum(
                                        'robot',
                                        formData.total_robot_num,
                                        formData.use_robot_num
                                    )
                                "
                            >
                                调整
                            </el-button>
                        </div>
                    </div>
                    <!-- <div class="basis-40 flex flex-col py-[10px] justify-center items-center">
                        <div class="text-tx-regular">知识库数量</div>
                        <div class="mt-2 flex items-center text-[20px]">
                            {{ formData.use_kb }}/{{ formData.kb_num }}
                            <el-button
                                v-perms="['user.user/adjustKb']"
                                type="primary"
                                link
                                @click="handleAdjustNum('kb', formData.kb_num, formData.use_kb)"
                            >
                                调整
                            </el-button>
                        </div>
                    </div> -->
                    <!-- <div class="basis-40 flex flex-col py-[10px] justify-center items-center">
                        <div class="text-tx-regular">形象余额/分钟</div>
                        <div class="mt-2 flex items-center text-[20px]">
                            {{ formData.video_num }}
                            <el-button
                                v-perms="['user.user/adjustVideo']"
                                type="primary"
                                link
                                @click="handleAdjust('duration', formData.video_num)"
                            >
                                调整
                            </el-button>
                        </div>
                    </div> -->
                    <div
                        class="basis-40 flex flex-1 flex-col py-[10px] justify-center items-center"
                    >
                        <div class="text-tx-regular">累计提问次数</div>
                        <div class="mt-2 flex items-center text-[20px]">
                            {{ formData.total_chat }}
                        </div>
                    </div>
                </div>
                <el-form-item label="用户编号："> {{ formData.sn }} </el-form-item>
                <el-form-item label="用户昵称：">
                    {{ formData.nickname }}
                </el-form-item>
                <el-form-item label="会员等级：">
                    {{ `${formData.package_name}(${formData.package_time || '无'})` }}
                    <el-button type="primary" link @click="handleAdjustVip">
                        <icon name="el-icon-EditPen" />
                    </el-button>
                </el-form-item>
                <el-form-item label="账号：">
                    {{ formData.account }}
                    <popover-input
                        class="ml-[10px]"
                        @confirm="handleEdit($event, 'account')"
                        :limit="32"
                        v-perms="['user.user/edit']"
                    >
                        <el-button type="primary" link>
                            <icon name="el-icon-EditPen" />
                        </el-button>
                    </popover-input>
                </el-form-item>
                <el-form-item label="真实姓名：">
                    {{ formData.real_name || '-' }}
                    <popover-input
                        class="ml-[10px]"
                        @confirm="handleEdit($event, 'real_name')"
                        :limit="32"
                        v-perms="['user.user/edit']"
                    >
                        <el-button type="primary" link>
                            <icon name="el-icon-EditPen" />
                        </el-button>
                    </popover-input>
                </el-form-item>
                <el-form-item label="性别：">
                    {{ formData.sex }}
                    <popover-input
                        class="ml-[10px]"
                        type="select"
                        :options="[
                            {
                                label: '未知',
                                value: 0
                            },
                            {
                                label: '男',
                                value: 1
                            },
                            {
                                label: '女',
                                value: 2
                            }
                        ]"
                        @confirm="handleEdit($event, 'sex')"
                        v-perms="['user.user/edit']"
                    >
                        <el-button type="primary" link>
                            <icon name="el-icon-EditPen" />
                        </el-button>
                    </popover-input>
                </el-form-item>
                <el-form-item label="联系电话：">
                    {{ formData.mobile || '-' }}
                    <popover-input
                        class="ml-[10px]"
                        type="number"
                        @confirm="handleEdit($event, 'mobile')"
                        v-perms="['user.user/edit']"
                    >
                        <el-button type="primary" link>
                            <icon name="el-icon-EditPen" />
                        </el-button>
                    </popover-input>
                </el-form-item>
                <el-form-item label="邮箱账号：">
                    {{ formData.email || '-' }}
                    <popover-input
                        class="ml-[10px]"
                        @confirm="handleEdit($event, 'email')"
                        :limit="32"
                        v-perms="['user.user/edit']"
                    >
                        <el-button type="primary" link>
                            <icon name="el-icon-EditPen" />
                        </el-button>
                    </popover-input>
                </el-form-item>
                <el-form-item label="上级邀请人">
                    {{ formData.inviter_name || '-' }}
                    <el-button type="primary" link @click="handleLeaderAdjust">
                        <icon name="el-icon-EditPen" />
                    </el-button>
                </el-form-item>
                <el-form-item label="邀请的用户"
                >{{ formData.invite_num ?? '-' }}
                    <router-link
                        :to="{
                            path: getRoutePath('distribution.distributor/belowLists'),
                            query: {
                                id: formData.id
                            }
                        }"
                    ><el-button link type="primary"> 查看邀请人数</el-button>
                    </router-link>
                </el-form-item>
                <el-form-item label="分销资格"
                ><span class="text-[#F2A626]">{{ formData.is_distribution_desc }}</span>
                    <router-link
                        :to="{
                            path: getRoutePath('distribution.distributor/detail'),
                            query: {
                                id: formData.id
                            }
                        }"
                    >
                        <el-button link type="primary" v-if="formData.is_distribution == 1">
                            查看分销信息</el-button
                        >
                    </router-link>
                </el-form-item>
                <el-form-item label="用户分组：">
                    {{ formData.group_name || '-' }}
                    <el-button
                        type="primary"
                        v-perms="['user.user/setGroup']"
                        @click="setGroup"
                        class="ml-[10px]"
                        link
                    >
                        <icon name="el-icon-EditPen" /> </el-button
                ></el-form-item>
                <el-form-item label="注册来源："> {{ formData.channel }} </el-form-item>
                <el-form-item label="多处登录：">
                    <span>{{ formData.multipoint_login == 1 ? '已开启' : '已关闭' }}</span>
                    <el-button link type="primary" @click="editMultipoint_login">{{
                        formData.multipoint_login == 1 ? '关闭' : '开启'
                    }}</el-button>
                </el-form-item>
                <el-form-item label="注册时间："> {{ formData.create_time }} </el-form-item>
                <el-form-item label="最近登录时间：">
                    {{ formData.login_time || '-' }}
                </el-form-item>
            </el-form>

            <el-button @click="recordPopRef.open()"> 会员开通记录 </el-button>
            <el-button
                v-if="formData.is_blacklist == 0"
                v-perms="['user.user/blacklist']"
                @click="BlackList(1, formData.id, formData.nickname)"
                >加入黑名单</el-button
            >
            <el-button
                v-if="formData.is_blacklist == 1"
                @click="BlackList(2, formData.id, formData.nickname)"
            >
                移出黑名单
            </el-button>
            <el-button @click="resetPassword" v-perms="['user.user/rePassword']">
                重置密码
            </el-button>
            <el-button @click="onUnsubscribe" v-perms="['user.user/rePassword']">
                注销账号
            </el-button>
        </el-card>

        <account-adjust
            v-bind="adjustState"
            v-model:show="adjustState.show"
            @confirm="handleConfirmAdjust"
        />
        <reset-password-pop v-if="popShow" ref="popRef" @close="popShow = false" />
        <adjust-group
            v-if="popShow"
            ref="adjustGroupRef"
            @close="
                () => {
                    getDetails()
                    popShow = false
                }
            "
        />
        <!-- @vue-ignore -->
        <num-adjust
            v-bind="adjustNumState"
            v-model:show="adjustNumState.show"
            @confirm="handleConfirmAdjustNum"
        />
        <VipAdjust
            v-if="showVip"
            ref="vipRef"
            @success="getDetails"
            @close="showVip = false"
        ></VipAdjust>
        <LeaderAdjust
            v-if="showLeaderAdjust"
            ref="leaderAdjustRef"
            :user-info="formData"
            @success="getDetails"
            @close="showLeaderAdjust = false"
        >
        </LeaderAdjust>

        <VipRecord ref="recordPopRef"></VipRecord>
    </div>
</template>

<script lang="ts" setup name="consumerDetail">
import type { FormInstance } from 'element-plus'
import {
    adjustMoney,
    getUserDetail,
    userEdit,
    blackList,
    adjustRobot,
    adjustKb,
    adjustVideo,
    userCancelled
} from '@/api/consumer'
import { isEmpty } from '@/utils/util'
import AccountAdjust from '../components/account-adjust.vue'
import feedback from '@/utils/feedback'
import resetPasswordPop from '../components/resetPasswordPop.vue'
import AdjustGroup from '../components/adjust-group.vue'
import NumAdjust from '../components/num-adjust.vue'
import VipAdjust from '../components/vip-adjust.vue'
import LeaderAdjust from '../components/leader-adjust.vue'
import VipRecord from '../components/vip-record.vue'
import {getRoutePath} from '@/router'

const popRef = shallowRef()
const adjustGroupRef = shallowRef()
const popShow = ref(false)
const route = useRoute()
const router = useRouter()
const formData = reactive({
    id: 0,
    avatar: '',
    channel: '',
    create_time: '',
    login_time: '',
    mobile: '',
    nickname: '',
    real_name: 0,
    package_name: '',
    package_time: '',
    package_id: '',
    is_perpetual: '',
    sex: 0,
    sn: '',
    account: '',
    balance: 0,
    total_chat: '',
    is_blacklist: 0,
    is_distribution: 1,
    is_distribution_desc: '',
    distribution_status: '',
    distribution_time: '',
    inviter_name: '',
    invite_num: '',
    email: '',
    group_name: '',
    robot_num: 0,
    use_robot_num: 0,
    use_robot: 0,
    total_robot_num: 0,
    use_kb: 0,
    kb_num: 0,
    group_ids: [],
    video_num: 0,
    multipoint_login: 1
})

// 会员调整
const showVip = ref<boolean>(false)
const vipRef = shallowRef()
const adjustState = reactive({
    show: false,
    value: 0,
    title: '',
    unit: '',
    type: ''
})
// 会员开通记录
const recordPopRef = shallowRef()

// 余额调整
const adjustNumState = reactive({
    type: '',
    show: false,
    value: '',
    use: ''
})

const formRef = shallowRef<FormInstance>()

// 分销邀请人调整
const showLeaderAdjust = ref(false)
const leaderAdjustRef = shallowRef()


const getDetails = async () => {
    const data = await getUserDetail({
        id: route.query.id
    })
    Object.keys(formData).forEach((key) => {
        //@ts-ignore
        formData[key] = data[key]
    })
}

const handleEdit = async (value: string, field: string) => {
    if (isEmpty(value)) return
    await userEdit({
        id: route.query.id,
        field,
        value
    })
    getDetails()
}

//编辑多点登录
const editMultipoint_login = async () => {
    await userEdit({
        id: route.query.id,
        field: 'multipoint_login',
        value: formData.multipoint_login == 1 ? 0 : 1
    })
    getDetails()
}

//余额调整
const handleAdjust = (type: 'chat' | 'duration', value: number) => {
    adjustState.show = true
    adjustState.value = value
    adjustState.type = type
    switch (type) {
        case 'chat':
            {
                adjustState.title = '电力值'
                adjustState.unit = '电力值'
            }
            break
        // case 'duration':
        //     {
        //         adjustState.title = '形象'
        //         adjustState.unit = '分钟'
        //     }
        //     break
    }
}
//余额调整提交
const handleConfirmAdjust = async (value: any) => {
    console.log(value)

    switch (adjustState.type) {
        case 'chat':
            await adjustMoney({ user_id: route.query.id, scene: 'balance', ...value })
            break
        case 'duration':
            await adjustVideo({ user_id: route.query.id, scene: 'video', ...value })
            break
    }

    adjustState.show = false
    getDetails()
}

const handleAdjustVip = async () => {
    showVip.value = true
    await nextTick()
    vipRef.value?.open('add')
    vipRef.value?.setFormData(formData, route.query.id)
}

const handleAdjustNum = (type: string, value: any, use: any) => {
    adjustNumState.type = type
    adjustNumState.show = true
    adjustNumState.value = value
    adjustNumState.use = use
}
//智能体调整提交
const handleConfirmAdjustNum = async (value: any) => {
    switch (adjustNumState.type) {
        case 'robot':
            await adjustMoney({ user_id: route.query.id, scene: 'robot', ...value })
            break
        case 'kb':
            await adjustKb({ user_id: route.query.id, ...value })
            break
    }
    adjustNumState.show = false
    getDetails()
}

//调整分组
const setGroup = async () => {
    popShow.value = true
    await nextTick()
    adjustGroupRef.value.open({ id: [formData.id], groupIds: formData.group_ids, type: 1 })
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
    getDetails()
}

//重置密码
const resetPassword = async () => {
    popShow.value = true
    await nextTick()
    popRef.value.open(formData.id)
}

// 注销账号
const onUnsubscribe = async () =>{
    await feedback.customConfirm(
        '确认注销',
        '吗？注销后该用户的全部资产将被清空，且对方将无法登录！',
        formData.nickname,
        'color:red',
        '注销账号'
    )
    await userCancelled({ user_id: route.query.id as any })
    setTimeout(() => {
        router.back()
    }, 1000)
}

const handleLeaderAdjust = async () => {
    showLeaderAdjust.value = true
    await nextTick()
    leaderAdjustRef.value?.open(route.query.id)
}

getDetails()
</script>
