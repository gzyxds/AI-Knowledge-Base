<template>
    <div>
        <NuxtLayout name="default">
            <template v-if="config.status > 0">
                <SearchResult v-if="showSearchResult" />
                <SearchAsk v-else />
            </template>
            <div
                v-else
                class="h-full flex-1 flex p-4 justify-center items-center"
            >
                <el-result>
                    <template #icon>
                        <el-image
                            class="w-[150px] dark:opacity-60"
                            :src="emptyImg"
                        />
                    </template>
                    <template #title>
                        <div class="text-info">功能暂未开启</div>
                    </template>
                </el-result>
            </div>
        </NuxtLayout>
    </div>
</template>

<script setup lang="ts">
import SearchAsk from './_components/search-ask.vue'
import SearchResult from './_components/search-result/index.vue'
import { useSearch } from './useSearch'
import emptyImg from '@/assets/image/empty_con.png'
const { showSearchResult, config, getConfig } = useSearch()
await useAsyncData(() => getConfig())
definePageMeta({
    layout: false,
    showLogo: true,
    hiddenFooter: true
})
</script>

<style lang="scss" scoped></style>
