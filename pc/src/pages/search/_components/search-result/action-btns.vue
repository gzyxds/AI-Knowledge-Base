<template>
    <div class="flex flex-wrap items-center">
        <ElButton link @click="launchSearch()">
            <template #icon>
                <Icon name="el-icon-RefreshLeft" />
            </template>
            重写
        </ElButton>
        <ElButton link type="primary" @click="copyResult">
            <template #icon>
                <Icon name="el-icon-DocumentCopy" />
            </template>
            复制
        </ElButton>
    </div>
</template>
<script setup lang="ts">
import { useSearch } from '../../useSearch'
import { copy } from '@/utils/util'
const { launchSearch, result } = useSearch()
const copyResult = () => {
    const text = result.value.data.reduce((prev, item) => {
        if (['markdown', 'expand_query'].includes(item.type)) {
            prev += item.content + '\n'
        }
        return prev
    }, '')
    copy(text)
}
</script>

<style lang="scss" scoped></style>
