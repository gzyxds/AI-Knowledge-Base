<template>
  <div>
    <app-link :to="`${path}${queryStr ? `?${queryStr}` : ''}`">
      <el-menu-item :index="path">
        <!-- {{ item }} -->
        <img
          v-if="isActive && item.selected && isShowIcon"
          class="menu-item-icon"
          :src="getImageUrl(item.selected)"
        />
        <img
          v-else-if="item.unselected && isShowIcon"
          class="menu-item-icon"
          :src="getImageUrl(item.unselected)"
        />
        <template #title>
          <span>{{ item.name }}</span>
        </template>
      </el-menu-item>
    </app-link>
    <!-- <el-sub-menu v-else :index="path">
      <template #title>
        <Icon
          v-if="item.icon"
          class="menu-item-icon"
          :size="16"
          :name="item.icon"
        />
        <span>{{ item.name }}</span>
      </template>
      <menu-item
        v-for="child in item.children"
        :key="resolvePath(child.path)"
        :item="child"
        :path="resolvePath(child.path)"
      />
    </el-sub-menu> -->
  </div>
</template>

<script lang="ts" setup>
import { getNormalPath, objectToQuery } from '@/utils/util'
import { isExternal } from '@/utils/validate'
import { useAppStore } from '@/stores/app'

interface Props {
  item: any
  path: string
  isShowIcon: number | boolean
  isActive: boolean
}

const props = defineProps<Props>()
const { getImageUrl } = useAppStore()
// const hasShowChild = computed(() => {
//   const children: any[] = props.item.children ?? []
//   return !!children.filter((item) => !item.hidden).length
// })

const resolvePath = (path: string) => {
  if (isExternal(path)) {
    return path
  }
  const newPath = getNormalPath(`${props.path}/${path}`)
  return newPath
}
const queryStr = computed<string>(() => {
  const query = props.item.link.query as string
  try {
    const queryObj = JSON.parse(query)
    return objectToQuery(queryObj)
  } catch (error) {
    return query
  }
})
</script>
<style lang="scss" scoped>
.el-menu-item,
.el-sub-menu__title {
  @apply text-lg;
  .menu-item-icon {
    margin-right: 8px;
    width: 22px;
    text-align: center;
    vertical-align: middle;
  }
}
</style>
