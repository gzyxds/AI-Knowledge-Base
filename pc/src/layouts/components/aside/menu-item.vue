<template>
    <div>
        <app-link :to="`${path}${queryStr ? `?${queryStr}` : ''}`">
            <el-menu-item :index="path">
        <span class="mb-[6px]" v-if="isShowIcon">
          <img
              v-if="isActive && item.selected"
              class="menu-item-icon"
              :src="getImageUrl(item.selected)"
          />
          <img
              v-else-if="item.unselected"
              class="menu-item-icon"
              :src="getImageUrl(item.unselected)"
          />
        </span>

                <template #title>
                    <span class="text-sm" v-if="item?.showName || showName">{{ item.name }}</span>
                </template>
            </el-menu-item>
        </app-link>
    </div>
</template>

<script lang="ts" setup>
import {getNormalPath, objectToQuery} from '@/utils/util'
import {isExternal} from '@/utils/validate'
import {useAppStore} from '@/stores/app'

interface Props {
    item: any
    path: string
    showName?: boolean
    isShowIcon: number | boolean
    isActive: boolean
}

const props = defineProps<Props>()
const {getImageUrl} = useAppStore()
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
    height: auto;

    .menu-item-icon {
        width: 22px;
        height: 22px;
        text-align: center;
        vertical-align: middle;
    }
}
</style>
