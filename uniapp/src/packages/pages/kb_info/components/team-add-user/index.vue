<template>
    <view>
        <u-popup
            v-model="isVisible"
            closeable
            border-radius="16"
            mode="bottom"
            height="970"

        >
            <view class="flex flex-col min-h-0 h-full px-[20rpx]">
                <view class="py-[30rpx] font-medium text-lg">
                    添加成员
                </view>
                <view class="mb-4">
                    <u-search
                        placeholder="搜索成员"
                        v-model="keyword"
                        shape="square"
                        :show-action="false"
                        @change="fetchTeamMembers"
                    ></u-search>
                </view>
                <scroll-view scroll-y style="height: 670rpx">
                    <view>
                        <u-checkbox
                            v-model="isSelectAll"
                            :name="1"
                            @change="handleSelectAll"
                        >
                            全选
                        </u-checkbox>
                    </view>

                    <view
                        v-if="members.length === 0"
                        style="height: 500rpx"
                        class="flex justify-center items-center text-muted"
                    >
                        请搜索成员添加
                    </view>
                    <view class="mt-4" v-else>
                        <view
                            v-for="(item, index) in members"
                            :key="item.id"
                            class="flex justify-between items-center mb-4 pr-[20rpx]"
                            @click.stop="handleMemberSelect(item, 'box')"
                        >
                            <view class="flex items-center">
                                <u-checkbox
                                    v-model="item.isSelected"
                                    :name="1"
                                    :disabled="item.is_added"
                                    @click.stop="handleMemberSelect(item, 'checkbox')"
                                >
                                </u-checkbox>
                                <view class="flex items-center">
                                    <view class="flex-none">
                                        <u-image
                                            width="74"
                                            height="74"
                                            border-radius="50%"
                                            :src="item.avatar"
                                        />
                                    </view>
                                    <view class="ml-3">
                                        <view class="text-base">{{ item.nickname }}</view>
                                    </view>
                                </view>
                            </view>
                            <view
                                v-if="item.permission"
                                @click.stop="toggleItemActionSheet(item, index)"
                            >
                                <text class="mr-2 text-content">
                                    {{ TEAM_PERMISSIONS[item.permission] }}
                                </text>
                                <u-icon
                                    name="arrow-down"
                                    size="22"
                                    color="#666666"
                                ></u-icon>
                            </view>
                        </view>
                    </view>
                </scroll-view>
                <view>
                    <u-button
                        type="primary"
                        :loading="isLock"
                        @click="submit"
                    >
                        确认添加
                    </u-button>
                </view>
            </view>
        </u-popup>

        <u-action-sheet
            :list="list"
            border-radius="24"
            :safeAreaInsetBottom="true"
            :customItemStyles="{
                'font-size': '30rpx',
                'align-items': 'start',
                'padding-left': '30rpx'
            }"
            v-model="actionState.visible"
            @click="setPermission"
        ></u-action-sheet>
    </view>
</template>
<script setup lang="ts">
import { reactive, ref } from 'vue'
import { postTeam, getTeamUser } from "@/api/kb"
import { useLockFn } from "@/hooks/useLockFn"

enum TEAM_POWER {
    ADMIN = 1, // 管理员
    EDIT = 2, // 可编辑
    VIEW = 3, // 可查看
}

const TEAM_PERMISSIONS = {
    [TEAM_POWER.ADMIN]: "可管理",
    [TEAM_POWER.EDIT]: "可编辑",
    [TEAM_POWER.VIEW]: "可查看"
}

const props = withDefaults(
    defineProps<{ id: number }>(),
    { id: -1 }
)

const emit = defineEmits<{ (event: 'success', value: void): void }>()

const isVisible = ref(false)
const keyword = ref('')
const isSelectAll = ref(false)
const members = ref<any[]>([])
const selectedMembers = ref<any[]>([])

const actionState = reactive({
    visible: false,
    memberIndex: -1,
    memberId: 0
})

const list = ref([
    {
        text: '可管理',
        fontSize: 30,
        color: '#333333',
        subText: '管理整个知识库数据和信息'
    },
    {
        text: '可编辑',
        fontSize: 30,
        color: '#333333',
        subText: '只能操作数据学习，增删改查自己的数据，不能修改他人'
    },
    {
        text: '可查看',
        fontSize: 30,
        color: '#333333',
        subText: '查看知识库所有数据'
    }
])

const handleMemberSelect = (member: any, action: 'box' | 'checkbox') => {
    if (member.is_added) {
        return
    }
    const index = selectedMembers.value.findIndex((m) => m.id === member.id)
    if (index !== -1) {
        member.permission = 0
        selectedMembers.value.splice(index, 1)
    } else if (!member.is_added) {
        member.permission = TEAM_POWER.VIEW
        selectedMembers.value.push(member)
    }
    if (action === 'box') {
        member.isSelected = member.isSelected ? 0 : 1
    } else {
        member.isSelected = member.isSelected ? 1 : 0
    }
}

const handleSelectAll = (item: { value: boolean, name: number }) => {
    members.value.forEach(member => {
        if (!member.is_added) {
            member.isSelected = item.value ? 1 : 0
            member.permission = item.value ? TEAM_POWER.VIEW : 0
        }
    })
    selectedMembers.value = item.value ? members.value.filter(member => !member.is_added) : []
}

const toggleItemActionSheet = (member: any, index: number) => {
    actionState.visible = true
    actionState.memberIndex = index
    actionState.memberId = member.id
}

const setPermission = (index: number) => {
    members.value[actionState.memberIndex].permission = index + 1
}

const { lockFn: submit, isLock } = useLockFn(async () => {
    const users = Object.fromEntries(
        selectedMembers.value.map(member => [member.sn, member.permission])
    )
    await postTeam({ kb_id: props.id, users })
    emit("success")
    isVisible.value = false
})

const fetchTeamMembers = async () => {
    const res = await getTeamUser({
        kb_id: props.id,
        keyword: keyword.value
    })
    members.value = res.lists
}

const show = () => {
    keyword.value = ''
    isVisible.value = true
    selectedMembers.value = []
    isSelectAll.value = false
    fetchTeamMembers()
}

defineExpose({ show })
</script>