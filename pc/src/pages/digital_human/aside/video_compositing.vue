<template>
  <div class="h-full">
    <ElScrollbar>
      <div class="p-main">
        <div class="bg-white rounded-lg p-[20px]">
          <div class="border-b border-solid border-br-light pb-[10px]">
            <span class="text-2xl font-medium">视频合成</span>
            <span class="text-tx-secondary ml-[10px]">
              剩余合成时长：{{ userStore.userInfo.video_num }}分钟
            </span>
          </div>
          <div class="py-main">
            <div class="flex items-center flex-wrap">
              <div class="flex-1">
                <NuxtLink
                  custom
                  :to="`/digital_human/design`"
                  v-slot="{ href }"
                >
                  <a :href="href" class="mr-[10px]" target="_blank">
                    <ElButton type="primary">新建视频</ElButton>
                  </a>
                </NuxtLink>

                <ElButton
                  :disabled="!selectData.length"
                  @click="handleDelete(selectData)"
                  >批量删除</ElButton
                >
              </div>
              <div class="flex-none flex">
                <el-input
                  v-model="queryParams.keyword"
                  placeholder="搜索"
                  @input="searchDebounce"
                >
                  <template #prepend>
                    <el-button :loading="pager.loading">
                      <template #icon>
                        <Icon name="el-icon-Search" />
                      </template>
                    </el-button>
                  </template>
                </el-input>
                <ElButton circle plain class="ml-[10px]" @click="getLists">
                  <template #icon>
                    <Icon name="el-icon-Refresh" />
                  </template>
                </ElButton>
              </div>
            </div>
          </div>
          <div class="flex-1 min-h-0">
            <el-table
              v-loading="pager.loading"
              height="100%"
              size="large"
              :data="pager.lists"
              @selection-change="handleSelectionChange"
            >
              <el-table-column type="selection" width="55" />

              <el-table-column label="文件名称" min-width="200">
                <template #default="{ row }">
                  <div class="flex items-center">
                    <MaterialFile
                      v-if="row.video_url"
                      @click="previewVideo(row.video_url)"
                      class="cursor-pointer"
                      type="video"
                      file-size="70px"
                      :uri="row.video_url"
                    />
                    <div class="ml-2">{{ row.name }}</div>
                  </div>
                </template>
              </el-table-column>

              <el-table-column label="生成进度" min-width="200">
                <template #default="{ row }">
                  <el-progress
                    v-if="row.status !== 1"
                    :percentage="row.synthetic_schedule"
                  />
                  <template v-else>-</template>
                </template>
              </el-table-column>
              <el-table-column label="任务状态" min-width="120">
                <template #default="{ row }">
                  <span v-if="row.status == 1">{{ row.status_desc }}</span>
                  <span v-else-if="row.status == 2" class="text-warning">
                    {{ row.status_desc }}
                  </span>
                  <span v-else-if="row.status == 3" class="text-success">
                    {{ row.status_desc }}
                  </span>
                  <span
                    v-else
                    class="text-error cursor-pointer"
                    @click="showFail(row.fail_reason)"
                  >
                    {{ row.status_desc }}
                  </span>
                </template>
              </el-table-column>
              <el-table-column
                label="消耗时长"
                prop="consume_time"
                min-width="120"
              >
              </el-table-column>

              <el-table-column
                label="最后更新时间"
                prop="update_time"
                min-width="180"
                show-tooltip-when-overflow
              />
              <el-table-column label="操作" width="180" fixed="right">
                <template #default="{ row }">
                  <el-button
                    v-if="row.status == 3 && row.video_url"
                    type="primary"
                    link
                    @click="download(row.video_url, row.name)"
                  >
                    下载
                  </el-button>
                  <NuxtLink
                    custom
                    :to="`/digital_human/design?id=${row.id}`"
                    v-if="row.status == 1 || row.status == 4"
                    v-slot="{ href }"
                  >
                    <a :href="href" class="mr-[10px]" target="_blank">
                      <el-button type="primary" link> 编辑 </el-button>
                    </a>
                  </NuxtLink>

                  <el-button
                    type="primary"
                    link
                    @click="handleRename(row.id, row.name)"
                  >
                    重命名
                  </el-button>
                  <el-button
                    v-if="row.delete_btn"
                    type="danger"
                    link
                    @click="handleDelete([row.id])"
                  >
                    删除
                  </el-button>
                </template>
              </el-table-column>
            </el-table>
          </div>
          <div class="flex justify-end mt-4">
            <pagination v-model="pager" @change="getLists" />
          </div>
        </div>
      </div>
    </ElScrollbar>
    <material-preview
      v-model="previewState.show"
      :url="previewState.url"
      type="video"
    />
  </div>
</template>
<script setup lang="ts">
import { useDebounceFn } from '@vueuse/core'
import {
  getVideoRecordsList,
  delVideoRecord,
  putVideoRename
} from '@/api/digital_human'
import { useUserStore } from '@/stores/user'
import { downloadFile } from '@/utils/file'

const userStore = useUserStore()
const router = useRouter()
const previewState = reactive({
  show: false,
  url: ''
})
const queryParams = reactive({
  keyword: ''
})

const { pager, getLists, resetPage } = usePaging({
  fetchFun: getVideoRecordsList,
  params: queryParams
})

const selectData = ref<number[]>([])
const handleSelectionChange = (val: any[]) => {
  selectData.value = val.map((item) => item.id)
}
const handleDelete = async (ids: number[]) => {
  await feedback.confirm('确定要删除？')
  await delVideoRecord({ ids })
  getLists()
}

const handleRename = async (id: number, name: string) => {
  const { value } = await feedback.prompt('修改视频名称', '', {
    inputValue: name
  })
  await putVideoRename({
    name: value,
    id
  })
  getLists()
}
const searchDebounce = useDebounceFn(() => {
  resetPage()
}, 1000)

const showFail = (reason: string) => {
  feedback.confirm(`错误信息：${reason}`, '失败原因', {
    showConfirmButton: false,
    type: 'error',
    cancelButtonText: '关闭'
  })
}

const previewVideo = async (url: string) => {
  previewState.show = true
  await nextTick()
  previewState.url = url
}

const download = (url: string, name: string) => {
  downloadFile(url, name)
}

await getLists()

let timer: any = null
watch(
  () => pager.extend,
  (value) => {
    if (value.unfinished_num > 0) {
      timer = setTimeout(() => {
        getLists()
      }, 30 * 1000)
    } else {
      userStore.getUser()
    }
  },
  {
    immediate: true
  }
)

onUnmounted(() => {
  timer && clearTimeout(timer)
})

definePageMeta({
  parentPath: '/digital_human',
  title: '视频合成',
  hiddenFooter: true,
  auth: true
})
</script>
