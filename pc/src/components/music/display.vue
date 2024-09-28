<template>
    <div class="w-[360px] h-full ml-[16px] flex justify-center">
        <div class="h-full bg-page w-full rounded-[12px]">
            <ElScrollbar v-if="currentMusic.lyric">
                <div class="p-[40px] flex flex-col items-center">
                    <img
                        :src="currentMusic.image_url"
                        class="w-[200px] h-[200px] rounded-[12px]"
                    />
                    <div class="text-2xl font-bold mt-[30px]">
                        {{ currentMusic.title }}
                    </div>
                    <div
                        v-if="currentMusic.style_desc"
                        class="text-tx-secondary mt-[5px]"
                    >
                        风格：{{ currentMusic.style_desc }}
                    </div>
                    <div
                        class="whitespace-pre-wrap text-center mt-[20px] leading-7"
                    >
                        {{ currentMusic.lyric }}
                    </div>
                </div>
            </ElScrollbar>
            <div
                v-else
                class="h-full flex flex-col items-center justify-center"
            >
                <div class="text-tx-secondary">
                    <Icon :size="45" name="local-icon-music1" />
                </div>

                <div class="my-[10px] text-tx-secondary">
                    当前还没有选中音乐哦
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useMusicPlay } from './useMusicPlay'

const { currentMusic } = useMusicPlay()
</script>

<style lang="scss" scoped></style>
