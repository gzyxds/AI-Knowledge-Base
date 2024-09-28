<template>
    <div class="locality-draw-popup">
        <ElDialog
            v-model="isVisible"
            width="980px"
            class="!rounded-[12px]"
            :center="true"
            :draggable="true"
            :destroy-on-close="true"
            :close-on-click-modal="false"
        >
            <template #header>
                <div class="w-full text-left">
                    <div class="text-lg font-medium">添加成员</div>
                </div>
            </template>

            <div class="flex" @click="closePermissionPopover">
                <!-- 左侧选择成员区域 -->
                <div class="w-1/2">
                    <div class="px-4">
                        <ElInput
                            v-model="queryParams.keyword"
                            style="width: 100%"
                            size="large"
                            placeholder="搜索成员"
                            :prefix-icon="Search"
                            @input="fetchMemberList"
                        />
                    </div>
                    <ElScrollbar height="500px">
                        <div class="flex flex-col">
                            <div
                                v-if="pager.lists.length === 0"
                                style="height: 500px"
                                class="flex justify-center items-center text-tx-placeholder"
                            >
                                请搜索成员添加
                            </div>
                            <div class="mt-4">
                                <div class="my-2 px-4">
                                    <ElCheckbox
                                        v-model="isSelectAll"
                                        :true-value="1"
                                        :false-value="0"
                                        label="全选"
                                        size="large"
                                        @change="handleSelectAll"
                                    />
                                </div>
                                <template v-if="pager.lists.length !== 0">
                                    <div
                                        class="my-4 mr-4 py-2 px-4 flex items-center cursor-pointer hover:bg-primary-light-9 rounded-[12px]"
                                        v-for="member in pager.lists"
                                        :key="member.id"
                                        :class="{
                                            '!cursor-not-allowed':
                                                member.is_added
                                        }"
                                        @click="
                                            handleMemberSelect(member, 'box')
                                        "
                                    >
                                        <ElCheckbox
                                            v-model="member.isSelected"
                                            :true-value="1"
                                            :false-value="0"
                                            label=""
                                            size="large"
                                            :disabled="member.is_added"
                                            @click.stop="
                                                handleMemberSelect(
                                                    member,
                                                    'checkbox'
                                                )
                                            "
                                        />
                                        <ElAvatar
                                            :src="member.avatar"
                                            size="26"
                                            class="flex-none ml-2"
                                        />
                                        <div class="ml-2 text-xs">
                                            <span>{{ member.nickname }}</span>
                                            <div
                                                v-if="member.is_added"
                                                class="text-tx-placeholder"
                                            >
                                                已添加
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </ElScrollbar>
                </div>

                <ElDivider direction="vertical" style="height: 500px" />

                <!-- 右侧已选择成员区域 -->
                <ElScrollbar height="500px" class="w-1/2">
                    <div class="flex flex-col px-4">
                        <div class="text-base">
                            已选择：{{ selectedMembers.length }} 个
                        </div>
                        <div
                            class="mt-4 py-2 px-4 flex items-center justify-between cursor-pointer hover:bg-primary-light-9 rounded-[12px]"
                            v-for="(member, index) in selectedMembers"
                            :key="member.id"
                        >
                            <div class="flex items-center">
                                <ElAvatar
                                    :src="member.avatar"
                                    size="26"
                                    class="flex-none ml-2"
                                />
                                <div class="ml-2 text-tx-regular text-xs">
                                    {{ member.nickname }}
                                </div>
                            </div>

                            <div class="flex items-center">
                                <ElPopover
                                    placement="bottom-end"
                                    :width="380"
                                    trigger="click"
                                    :show-arrow="false"
                                    transition="custom-popover"
                                    :visible="
                                        isPermissionPopoverVisible &&
                                        currentPopoverIndex === index
                                    "
                                >
                                    <div class="cursor-pointer">
                                        <div
                                            class="flex items-center p-4 hover:bg-page rounded-xl"
                                            @click="
                                                setPermission(
                                                    member,
                                                    TEAM_POWER.EDIT
                                                )
                                            "
                                        >
                                            <div style="width: 320px">
                                                <div
                                                    class="text-base text-tx-primary"
                                                >
                                                    可编辑
                                                </div>
                                                <div
                                                    class="text-xs text-tx-placeholder mt-2"
                                                >
                                                    只能操作数据学习，增删改查自己的数据，不能修改他人
                                                </div>
                                            </div>
                                            <ElCheckbox
                                                :model-value="member.permission"
                                                :true-value="TEAM_POWER.EDIT"
                                                label=""
                                                size="large"
                                                @click.stop="
                                                    setPermission(
                                                        member,
                                                        TEAM_POWER.EDIT
                                                    )
                                                "
                                            />
                                        </div>
                                        <div
                                            class="flex items-center p-4 hover:bg-page rounded-xl"
                                            @click="
                                                setPermission(
                                                    member,
                                                    TEAM_POWER.VIEW
                                                )
                                            "
                                        >
                                            <div style="width: 320px">
                                                <div
                                                    class="text-base text-tx-primary"
                                                >
                                                    可查看
                                                </div>
                                                <div
                                                    class="text-xs text-tx-placeholder mt-2"
                                                >
                                                    查看知识库所有数据
                                                </div>
                                            </div>
                                            <ElCheckbox
                                                :model-value="member.permission"
                                                :true-value="TEAM_POWER.VIEW"
                                                label=""
                                                size="large"
                                                @click.stop="
                                                    setPermission(
                                                        member,
                                                        TEAM_POWER.VIEW
                                                    )
                                                "
                                            />
                                        </div>

                                        <div
                                            class="flex items-center p-4 hover:bg-page rounded-xl"
                                            @click="
                                                setPermission(
                                                    member,
                                                    TEAM_POWER.ADMIN
                                                )
                                            "
                                        >
                                            <div style="width: 320px">
                                                <div
                                                    class="text-base text-tx-primary"
                                                >
                                                    可管理
                                                </div>
                                                <div
                                                    class="text-xs text-tx-placeholder mt-2"
                                                >
                                                    管理整个知识库数据和信息
                                                </div>
                                            </div>
                                            <ElCheckbox
                                                :model-value="member.permission"
                                                :true-value="TEAM_POWER.ADMIN"
                                                label=""
                                                size="large"
                                                @click.stop="
                                                    setPermission(
                                                        member,
                                                        TEAM_POWER.ADMIN
                                                    )
                                                "
                                            />
                                        </div>
                                    </div>

                                    <template #reference>
                                        <div
                                            class="flex items-center cursor-pointer"
                                            @click.stop="
                                                openPermissionPopover(index)
                                            "
                                        >
                                            <span class="text-xs">{{
                                                TEAM_MAP[member.permission]
                                            }}</span>
                                            <Icon name="el-icon-ArrowDown" />
                                        </div>
                                    </template>
                                </ElPopover>
                                <div
                                    class="flex items-center ml-6"
                                    @click="removeMember(member)"
                                >
                                    <Icon name="el-icon-CloseBold" />
                                </div>
                            </div>
                        </div>
                    </div>
                </ElScrollbar>
            </div>

            <template #footer>
                <div class="flex justify-end">
                    <ElButton @click="isVisible = false">取消</ElButton>
                    <ElButton type="primary" :loading="isLock" @click="submit">
                        确认
                    </ElButton>
                </div>
            </template>
        </ElDialog>
    </div>
</template>

<script lang="ts" setup>
import { ref, reactive, defineExpose } from 'vue'
import {
    ElInput,
    ElCheckbox,
    ElAvatar,
    ElButton,
    ElDialog,
    ElDivider,
    ElPopover
} from 'element-plus'
import { Search } from '@element-plus/icons-vue'
import { postTeam, getTeamUser } from '~/api/knowledge'

enum TEAM_POWER {
    ADMIN = 1, // 管理员
    EDIT = 2, // 可编辑
    VIEW = 3 // 可查看
}

const TEAM_MAP = {
    [TEAM_POWER.ADMIN]: '可管理',
    [TEAM_POWER.EDIT]: '可编辑',
    [TEAM_POWER.VIEW]: '可查看'
}

const emit = defineEmits(['success'])
const props = defineProps({ id: { type: Number, default: 0 } })

const isVisible = ref(false)
const isSelectAll = ref(false)
const isPermissionPopoverVisible = ref(false)
const currentPopoverIndex = ref(-1)

const queryParams = reactive({
    keyword: '',
    kb_id: props.id,
    page_type: 0
})

const selectedMembers = ref<any[]>([])

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getTeamUser,
    params: queryParams
})

const fetchMemberList = () => {
    resetPage()
    getLists()
}

const handleMemberSelect = (member: any, action: 'box' | 'checkbox') => {
    if (member.is_added) return
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

const handleSelectAll = (isSelected: boolean) => {
    pager.lists.forEach((member: any) => {
        if (!member.is_added) {
            member.isSelected = isSelected ? 1 : 0
            member.permission = isSelected ? TEAM_POWER.VIEW : 0
        }
    })
    selectedMembers.value = isSelected
        ? pager.lists.filter((member: any) => !member.is_added)
        : []
}

const removeMember = (member: any) => {
    selectedMembers.value = selectedMembers.value.filter(
        (m) => m.id !== member.id
    )
    pager.lists.find((m) => m.id === member.id)!.isSelected = false
}

const openPermissionPopover = (index: number) => {
    currentPopoverIndex.value = index
    isPermissionPopoverVisible.value = true
}

const closePermissionPopover = () => {
    isPermissionPopoverVisible.value = false
}

const setPermission = (member: any, permission: TEAM_POWER) => {
    member.permission = permission
    closePermissionPopover()
}

const { lockFn: submit, isLock } = useLockFn(async () => {
    const users: Record<number, number> = {}
    selectedMembers.value.forEach((member) => {
        users[member.sn] = member.permission
    })
    await postTeam({ kb_id: props.id, users })
    emit('success')
    isVisible.value = false
})

defineExpose({
    show: () => {
        isVisible.value = true
        resetParams()
        resetPage()
        selectedMembers.value = []
        isSelectAll.value = false
        getLists()
    }
})
</script>

<style scoped lang="scss">
.locality-draw-popup .el-dialog__body {
    padding-top: 4px;
}

.custom-popover {
    display: none !important;
}
</style>