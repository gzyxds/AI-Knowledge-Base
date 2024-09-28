<template>
    <div class="flex items-center p-[13px]">
        <div class="flex items-center w-[200px] h-[55px] cursor-pointer" @click="emit('title', currentMusic)">
            <template v-if="currentMusic.title">
                <img
                    :src="currentMusic.image_url"
                    class="w-[55px] h-full rounded-[8px]"
                />
                <div class="flex-1 min-w-0 ml-[15px]">
                    <div class="font-bold line-clamp-1">
                        {{ currentMusic.title }}
                    </div>
                    <div
                        v-if="currentMusic.style_desc"
                        class="line-clamp-1 text-tx-secondary text-sm"
                    >
                        {{ currentMusic.style_desc }}
                    </div>
                </div>
            </template>
        </div>
        <div class="mx-[20px] flex items-center">
            <div class="text-tx-regular">
                <ElButton
                    link
                    :disabled="currentId <= 0 || currentMusic.index <= 0"
                    @click="prevOrNext(-1)"
                >
                    <template #icon>
                        <Icon name="local-icon-up" :size="24" />
                    </template>
                </ElButton>
            </div>

            <div class="mx-[20px]">
                <ElButton
                    type="primary"
                    circle
                    @click="togglePlay"
                    :disabled="currentId <= 0"
                >
                    <template #icon>
                        <Icon
                            :name="`local-icon-${playing ? 'pause1' : 'play'}`"
                            :size="14"
                        />
                    </template>
                </ElButton>
            </div>
            <div class="text-tx-regular">
                <ElButton
                    link
                    :disabled="
                        currentId <= 0 ||
                        currentMusic.index >= musicList.length - 1
                    "
                    @click="prevOrNext(1)"
                >
                    <template #icon>
                        <Icon name="local-icon-down" :size="24" />
                    </template>
                </ElButton>
            </div>
        </div>
        <div class="flex items-center text-tx-secondary mx-[30px]">
            <div class="w-[50px]">{{ currentTimeTrans }}</div>
            <div class="mx-[20px] w-[250px]">
                <el-slider
                    :model-value="currentTime"
                    :disabled="!currentMusic.audio_url"
                    size="small"
                    :show-tooltip="false"
                    :min="0"
                    :max="duration"
                    @input="setCurrentTime"
                />
            </div>
            <div class="w-[50px]">{{ durationTrans }}</div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useMusicPlay } from './useMusicPlay'
const emit = defineEmits(['title'])

const {
    playing,
    duration,
    musicList,
    currentId,
    currentMusic,
    currentTime,
    currentTimeTrans,
    setCurrentTime,
    durationTrans,
    prevOrNext,
    togglePlay
} = useMusicPlay()
</script>

<style lang="scss" scoped>
.el-slider {
    --el-slider-height: 4px;
    --el-slider-button-size: 12px;
}
</style>
