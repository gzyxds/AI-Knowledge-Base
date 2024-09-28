<template>
  <div class="h-full flex flex-col">
    <div>
      <!-- <div class="px-main pt-main">
        <el-input
          v-model="state.keyword"
          placeholder="搜索配音"
          size="large"
          @input="searchDebounce"
        >
          <template #prepend>
            <el-button :loading="pending">
              <template #icon>
                <Icon name="el-icon-Search" />
              </template>
            </el-button>
          </template>
        </el-input>
      </div> -->

      <div class="pt-[15px] px-main">
        <DropdownMore class="my-[-5px]" :default-height="42">
          <el-radio-group v-model="state.index" class="el-radio-group-margin">
            <el-radio-button
              v-for="(item, index) in list"
              :key="index"
              :label="index"
            >
              {{ item.type }}
            </el-radio-button>
          </el-radio-group>
        </DropdownMore>
      </div>
    </div>

    <div class="flex-1 min-h-0">
      <el-scrollbar>
        <div class="p-main">
          <div v-if="dubList.length">
            <DubItem
              v-for="item in dubList"
              :active-id="canvasStore.dub.Voice"
              :item-id="item.Voice"
              :key="item.Voice"
              class="mb-[15px]"
              :name="`${item.Name}-${item.Desc}`"
              :pic="dub"
              :url="item.VoiceUrl"
              :disabled="canvasStore.voiceContent.type === 2"
              @click="selectItem(item)"
            />
          </div>

          <ElEmpty :image="empty_con" v-else description="暂无数据～" />
        </div>
      </el-scrollbar>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { useDebounceFn } from '@vueuse/core'
import { getDubList } from '@/api/digital_human'
import { useCanvasStore } from '@/stores/canvas'
import DubItem from './dub-item.vue'
import empty_con from '@/assets/image/empty_con.png'
import dub from '@/assets/image/dub.png'
const canvasStore = useCanvasStore()
const state = reactive({
  keyword: '',
  index: 0
})

const {
  data: list,
  refresh: refreshList,
  pending
} = await useAsyncData(() => getDubList(state), { lazy: true })
const dubList = computed(() => {
  return list.value[state.index]?.list || []
})
const searchDebounce = useDebounceFn(() => {
  refreshList()
}, 1000)

const selectItem = (item: any) => {
  canvasStore.dub = item
}
</script>
