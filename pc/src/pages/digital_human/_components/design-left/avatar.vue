<template>
  <div class="avatar-select h-full flex flex-col">
    <div class="px-main">
      <!-- <el-input v-model="state.keyword" size="large" placeholder="搜索形象">
        <template #prepend>
          <el-button>
            <template #icon>
              <Icon name="el-icon-Search" />
            </template>
          </el-button>
        </template>
      </el-input> -->
      <div class="mt-[5px]">
        <el-tabs v-model="state.currentTab">
          <el-tab-pane label="2D形象" name="2d" />
        </el-tabs>
      </div>
    </div>

    <div class="flex-1 min-h-0">
      <el-scrollbar>
        <div class="px-main pb-main">
          <div v-if="lists.length" class="flex flex-wrap mx-[-7px]">
            <div
              v-for="item in lists"
              :key="item.type"
              class="w-[50%]"
              @click="insertAvatar(item)"
            >
              <div class="px-[7px] mb-[14px]">
                <div
                  class="border border-solid border-br-light rounded-md p-[10px] cursor-pointer"
                  :class="{
                    '!border-primary':
                      currentAvatar?.avatar_id == item.avatar_id
                  }"
                >
                  <div>
                    <div class="pic-wrap h-0 pt-[110%] relative">
                      <div class="absolute inset-0 bg-[#f4f6ff] rounded-lg">
                        <ElImage
                          :src="item.cover_url"
                          class="w-full h-full"
                          fit="contain"
                          lazy
                        />
                      </div>
                    </div>
                    <div class="mt-[10px] line-clamp-1 text-center">
                      {{ item.name }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <ElEmpty :image="empty_con" v-else description="暂无数据～" />
        </div>
      </el-scrollbar>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { useCanvasStore, ImageTypes } from '@/stores/canvas'
import { getAvatarList } from '@/api/digital_human'
import empty_con from '@/assets/image/empty_con.png'
const canvasStore = useCanvasStore()
const insertAvatar = (item: any) => {
  canvasStore.addImage(item.cover_url, ImageTypes.AVATAR, item)
}

const currentAvatar = computed(() => {
  const object = canvasStore.canvasJson.objects?.find(
    (item) => item.customType === ImageTypes.AVATAR
  )
  if (object?.data) {
    return object.data
  }
})
const state = reactive({
  keyword: '',
  currentTab: '2d'
})

const { data: lists } = await useAsyncData(() => getAvatarList(), {
  lazy: true
})
</script>
