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
          <el-radio-group
            v-model="state.category_id"
            class="el-radio-group-margin"
            @change="refreshMusicList()"
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
    </div>

    <div class="flex-1 min-h-0">
      <el-scrollbar>
        <div class="p-main">
          <div v-if="musicList.length">
            <DubItem
              v-for="item in musicList"
              :active-id="canvasStore.music.id"
              :item-id="item.id"
              :key="item.id"
              class="mb-[15px]"
              :name="item.name"
              :pic="item.cover"
              :url="item.url"
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
import { getMusicCategory, getMusicList } from '@/api/digital_human'
import { useCanvasStore } from '@/stores/canvas'
import DubItem from './dub-item.vue'
import empty_con from '@/assets/image/empty_con.png'
const canvasStore = useCanvasStore()
const state = reactive({
  keyword: '',
  category_id: 0
})

const { data: category } = await useAsyncData(() => getMusicCategory(), {
  lazy: true
})
const {
  data: musicList,
  refresh: refreshMusicList,
  pending
} = await useAsyncData(() => getMusicList(state), {
  lazy: true
})

const searchDebounce = useDebounceFn(() => {
  refreshMusicList()
}, 1000)

const selectItem = (item: any) => {
  canvasStore.music = toRaw(item)
}
</script>
