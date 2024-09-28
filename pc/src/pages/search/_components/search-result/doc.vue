<template>
    <div>
        <div>
            <a
                class="bg-page p-4 mb-[12px] rounded-[12px] block hover:text-primary"
                :key="item.seeMoreUrl"
                v-for="(item, index) in currentResult"
                :href="item.seeMoreUrl"
                target="_blank"
            >
                <div class="line-clamp-2">
                    <span class="text-primary">【{{ item.index }}】</span>
                    <span class="font-bold text-sm">{{ item.title }}</span>
                </div>
                <div class="text-xs text-tx-regular line-clamp-3">
                    {{ item.snippet }}
                </div>
                <div class="flex justify-end items-center mt-2">
                    <el-image
                        :src="item.image"
                        alt=""
                        class="w-[12px] h-[12px]"
                        fit="contain"
                    >
                        <template #error>
                            <span class="flex text-primary">
                                <Icon :size="12" name="el-icon-Link" />
                            </span>
                        </template>
                    </el-image>
                    <div class="text-xs text-tx-secondary ml-1">
                        {{ item.showName }}
                    </div>
                </div>
            </a>
        </div>
        <el-pagination
            size="small"
            background
            hide-on-single-page
            v-model:page-size="pager.size"
            v-model:current-page="pager.page"
            layout="pager"
            :total="result.search.length"
            class="py-4"
        />
    </div>
</template>

<script setup lang="ts">
import { useSearch } from '../../useSearch'
const { options, result } = useSearch()

const pager = reactive({
    size: 5,
    page: 1
})

const currentResult = computed(() => {
    return result.value.search.slice(
        (pager.page - 1) * pager.size,
        pager.page * pager.size
    )
})
</script>
