<template>
  <div class="h-full w-full overflow-hidden">
    <ElScrollbar>
      <div class="flex flex-wrap py-[20px] mx-[-10px]">
        <div
          class="md:w-[25%] w-[50%] px-[10px] mb-[20px]"
          v-for="(item, index) in dataList"
          :key="item.key"
        >
          <div
            class="bg-[#fafafc] flex flex-col items-center rounded-[10px] p-main border border-solid border-br-light h-full dark:bg-page"
          >
            <div class="text-lg">{{ item.title }}</div>
            <div class="text-[30px] mt-[12px]">
              {{ get(data, item.key) }}
            </div>
          </div>
        </div>
      </div>
    </ElScrollbar>
  </div>
</template>
<script lang="ts" setup>
import { getRobotChatData } from '@/api/robot'
import { get } from 'lodash-es'
const props = defineProps<{
  appId: string | number
}>()

const { appId } = toRefs(props)
const dataList = ref([
  {
    title: '今日对话次数',
    key: 'robot.todayChatCount'
  },
  {
    title: '昨日对话次数',
    key: 'robot.yesterdayChatCount'
  },
  {
    title: '本周对话次数',
    key: 'robot.weekChatCount'
  },
  {
    title: '全部对话次数',
    key: 'robot.wholeChatCount'
  },
  {
    title: '今日访问用户/人',
    key: 'visitor.todayVisitorCount'
  },
  {
    title: '昨日访问用户/人',
    key: 'visitor.yesterdayVisitorCount'
  },
  {
    title: '本周访问用户/人',
    key: 'visitor.weekVisitorCount'
  },
  {
    title: '全部用户/人',
    key: 'visitor.wholeVisitorCount'
  }
])

const { data, refresh } = useAsyncData(
  () =>
    getRobotChatData({
      robot_id: appId.value
    }),
  {
    default() {
      return {
        robot: {},
        visitor: {}
      }
    },
    lazy: true
  }
)

watch(
  () => props.appId,
  () => {
    refresh()
  }
)
</script>
