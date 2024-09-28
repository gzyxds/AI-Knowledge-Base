<template>
    <div>
        <NuxtLayout name="default">
            <div class="h-full flex">
                <div class="p-4 h-full">
                    <div
                        class="w-[300px] h-full flex flex-col bg-body rounded-lg"
                    >
                        <div class="p-[15px]">
                            <div class="flex items-center">
                                <NuxtLink
                                    class="flex bg-body p-[5px] text-bold rounded-[50%] text-primary shadow-light"
                                    to="/robot_square"
                                    :replace="true"
                                >
                                    <Icon name="el-icon-Back" :size="18" />
                                </NuxtLink>
                                <div class="text-xl flex-1 min-w-0 ml-[10px]">
                                    智能体广场
                                </div>
                            </div>
                        </div>

                        <div class="flex-1 min-h-0">
                            <ElScrollbar>
                                <div class="px-[15px]">
                                    <NuxtLink
                                        v-for="item in robotRecord"
                                        :key="item.id"
                                        :to="{
                                            path: '',
                                            query: {
                                                id: item.id
                                            }
                                        }"
                                        class="flex mb-[15px] rounded-[10px] px-[15px] py-[10px] items-center border border-br-light bg-body"
                                        :class="{
                                            'text-white !border-primary !bg-primary':
                                                currentId == item.id
                                        }"
                                        :replace="true"
                                    >
                                        <el-image
                                            class="w-[50px] h-[50px] rounded-[50%]"
                                            :src="item.image"
                                            alt=""
                                        />
                                        <div class="flex-1 min-w-0 ml-[15px]">
                                            <div
                                                class="line-clamp-1 text-xl font-medium"
                                            >
                                                {{ item.name }}
                                            </div>

                                            <div
                                                class="line-clamp-1 mt-[4px] text-tx-secondary"
                                                :class="{
                                                    '!text-white':
                                                        currentId == item.id
                                                }"
                                            >
                                                {{ item.intro }}
                                            </div>
                                        </div>
                                    </NuxtLink>
                                </div>
                            </ElScrollbar>
                        </div>
                    </div>
                </div>

                <div class="flex-1 min-w-0 pr-4 py-4">
                    <div class="bg-body rounded-[10px] h-full">
                        <TheChat
                            v-if="currentRobot.id"
                            :robot-id="currentRobot.robot_id"
                            :square-id="currentRobot.id"
                        />
                    </div>
                </div>
            </div>
        </NuxtLayout>
    </div>
</template>
<script setup lang="ts">
import { getRobotRecord } from '@/api/robot'
const route = useRoute()
const navList = [
    {
        name: '智能体广场',
        icon: 'local-icon-robot1',
        path: '/robot_square/chat'
    }
]

const { data: robotRecord } = await useAsyncData(() => getRobotRecord(), {
    default() {
        return []
    },
    lazy: true
})
const currentId = computed(() => route.query.id)
const currentRobot = computed(() => {
    return (
        robotRecord.value.find(
            (item: any) => item.id === Number(currentId.value)
        ) || {}
    )
})
definePageMeta({
    layout: false,
    hasPanel: true,
    hiddenFooter: true,
    parentPath: '/robot_square'
})
</script>
<style lang="scss" scoped></style>
