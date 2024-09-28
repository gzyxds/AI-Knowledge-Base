<template>
  <div class="h-full flex flex-col">
    <div class="mt-[5px] px-main">
      <el-tabs v-model="state.type">
        <el-tab-pane
          v-for="item in tabList"
          :key="item.type"
          :label="item.label"
          :name="item.type"
        />
      </el-tabs>
    </div>
    <div class="flex-1 min-h-0">
      <ElScrollbar>
        <div class="p-main pt-0">
          <div v-if="lists.length" class="flex flex-wrap mx-[-7px]">
            <div v-for="item in lists" :key="item.id" class="w-[50%]">
              <div class="px-[7px] mb-[14px]" @click="insertMaps(item)">
                <div
                  class="border border-solid border-br-light rounded-md cursor-pointer p-[10px]"
                >
                  <div>
                    <div class="pic-wrap h-0 pt-[100%] relative">
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
          <ElEmpty :image="empty_con" v-else description="暂无数据～" />
        </div>
      </ElScrollbar>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { getProspectsList } from '@/api/digital_human'
import { useCanvasStore, ImageTypes } from '@/stores/canvas'
import empty_con from '@/assets/image/empty_con.png'
const canvasStore = useCanvasStore()
const tabList = [
  {
    type: 1,
    label: '系统前景'
  }
]
const state = reactive({
  type: 1
})
const { data: lists } = await useAsyncData(() => getProspectsList(), {
  lazy: true
})
const insertMaps = (item: any) => {
  canvasStore.addImage(item.url, ImageTypes.PROSPECT, item)
}
</script>
<style lang="scss" scoped></style>
