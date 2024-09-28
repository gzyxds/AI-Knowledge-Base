<template>
  <div class="maps-select h-full flex flex-col">
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
          <DropdownMore class="mb-[10px] mt-[-5px]" :default-height="42">
            <el-radio-group v-model="state.index" class="el-radio-group-margin">
              <el-radio-button
                v-for="(item, index) in mapsCategory"
                :key="index"
                :label="index"
              >
                {{ item.name }}
              </el-radio-button>
            </el-radio-group>
          </DropdownMore>
          <div v-if="mapsLists.length" class="flex flex-wrap mx-[-7px]">
            <div v-for="item in mapsLists" :key="item.id" class="w-[33.33%]">
              <div class="px-[7px] mb-[14px]" @click="insertMaps(item)">
                <div class="bg-[#101010] rounded-md cursor-pointer">
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
import { getMapsList } from '@/api/digital_human'
import { useCanvasStore, ImageTypes } from '@/stores/canvas'
import empty_con from '@/assets/image/empty_con.png'
const canvasStore = useCanvasStore()
const tabList = [
  {
    type: 1,
    label: '系统贴图'
  }
]
const state = reactive({
  type: 1,
  index: 0
})
const { data: mapsCategory } = await useAsyncData(() => getMapsList(), {
  lazy: true
})

const mapsLists = computed(() => {
  return mapsCategory.value[state.index].decals || []
})
const insertMaps = (item: any) => {
  canvasStore.addImage(item.url, ImageTypes.MAPS, item)
}
</script>
<style lang="scss" scoped>
.maps-select {
}
</style>
