<template>
    <div class="flex flex-col h-full bg-body rounded-lg">
        <div class="px-4 pt-4">
            <div
                class="bg-page p-[5px] rounded-[10px]"
                :style="{
                    width: `${100 * worksTypes.length}px`
                }"
            >
                <el-segmented
                    style="--el-border-radius-base: 10px"
                    v-model="currentType"
                    class="!bg-[transparent] h-[34px]"
                    :block="true"
                    :options="worksTypes"
                />
            </div>
        </div>
        <div class="flex-1 min-h-0">
            <Music v-if="currentType === 'music'" />
            <Draw v-if="currentType === 'draw'" />
            <Video v-if="currentType === 'video'" />
        </div>
    </div>
</template>

<script setup lang="ts">
import Music from './_components/music.vue'
import Draw from './_components/draw.vue'
import Video from './_components/video.vue'
const router = useRouter()
const route = useRoute()

const worksTypes = [
    {
        label: 'AI绘画',
        value: 'draw'
    },
    {
        label: 'AI音乐',
        value: 'music'
    },
    {
        label: 'AI视频',
        value: 'video'
    }
]
const currentType = computed({
    get() {
        const select = worksTypes.find(
            (item) => item.value === route.query.type
        )
        return select?.value || 'draw'
    },
    set(value) {
        router.replace({
            path: '',
            query: {
                type: value
            }
        })
    }
})
</script>

<style scoped lang="scss"></style>
