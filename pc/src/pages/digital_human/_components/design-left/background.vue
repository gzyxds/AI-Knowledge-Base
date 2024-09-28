<template>
  <div class="avatar-select h-full flex flex-col">
    <div class="px-main">
      <!-- <Upload class="" type="image" multiple :show-file-list="false">
        <template #default="{ loading }">
          <el-button
            type="primary"
            size="large"
            class="w-full"
            :loading="loading"
          >
            上传图片
          </el-button>
        </template>
      </Upload> -->
      <div class="pt-[5px]">
        <el-tabs
          v-model="state.type"
          @tab-change=";(bgList = []), refreshList()"
        >
          <el-tab-pane
            v-for="item in tabList"
            :key="item.type"
            :label="item.label"
            :name="item.type"
          />
        </el-tabs>
      </div>

      <DropdownMore :default-height="42" class="my-[-5px]">
        <el-radio-group
          v-model="state.category_id"
          class="el-radio-group-margin"
          @change="refreshList()"
        >
          <el-radio-button
            v-for="item in category"
            :key="item.id"
            :label="item.id"
          >
            {{ item.name }}
          </el-radio-button>
        </el-radio-group>
      </DropdownMore>
    </div>

    <div class="flex-1 min-h-0">
      <el-scrollbar>
        <div class="p-main">
          <div v-if="bgList.length" class="flex flex-wrap mx-[-7px]">
            <div v-for="item in bgList" :key="item.id" class="w-[50%]">
              <div class="px-[7px] mb-[14px]" @click="insertBackground(item)">
                <div
                  class="border border-solid border-br-light rounded-md p-[10px] cursor-pointer"
                  :class="{
                    '!border-primary': currentBg?.id == item.id
                  }"
                >
                  <div>
                    <div
                      class="pic-wrap h-0 relative"
                      :class="{
                        'pt-[177%]': state.type == 1,
                        'pt-[56.3%]': state.type == 2
                      }"
                    >
                      <div class="absolute inset-0">
                        <ElImage
                          :src="item.url"
                          class="w-full h-full"
                          fit="contain"
                          lazy
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <ElEmpty
            v-else-if="!pending"
            :image="empty_con"
            description="暂无数据～"
          />
        </div>
      </el-scrollbar>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { useCanvasStore, ImageTypes, canvasSizeData } from '@/stores/canvas'
import { getBgCategory, getBgList } from '@/api/digital_human'
import empty_con from '@/assets/image/empty_con.png'
const canvasStore = useCanvasStore()
const tabList = [
  {
    type: 1,
    label: '竖版背景'
  },
  {
    type: 2,
    label: '横版背景'
  }
]

const state = reactive({
  type: 1,
  category_id: 0
})

const { data: category } = await useAsyncData(() => getBgCategory(), {
  lazy: true
})
const {
  data: bgList,
  refresh: refreshList,
  pending
} = await useAsyncData(() => getBgList(state), { lazy: true })
const insertBackground = async (item: any) => {
  if (currentTab.value?.type !== canvasStore.defaultSize.resolution) {
    const size = (canvasSizeData as any)[currentTab.value?.type!]
    if (canvasStore.getCanvasJson()?.objects.length) {
      await feedback.confirm(
        '是否确认更改画布尺寸？当前画面所有设置将被重置且无法恢复'
      )
    }
    canvasStore.changeSize(size)
    canvasStore.initObject()
  }
  canvasStore.addImage(item.url, ImageTypes.BACKGROUND, item)
}
const currentTab = computed(() =>
  tabList.find((item) => item.type === state.type)
)
const currentBg = computed(() => {
  const object = canvasStore.canvasJson.objects?.find(
    (item) => item.customType === ImageTypes.BACKGROUND
  )
  if (object?.data) {
    return object.data
  }
  return null
})
</script>

<style lang="scss" scoped>
.avatar-select {
  :deep() {
    .el-upload {
      display: flex;
    }
  }
}
</style>
