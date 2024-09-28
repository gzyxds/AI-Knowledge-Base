<template>
    <div class="task-center" :style="styles">
        <div class="bg-[#f7cb64] text-btn-text px-[15px] pt-[15px] pb-[40px] mb-[-25px]">
            剩余条数
            <span class="text-[24px] font-medium">999</span>
            条
        </div>
        <div class="px-[15px] pb-[10px]">
            <div class="daily-tasks">
                <div class="tasks-title">
                    <div class="font-medium text-xl">每日任务</div>
                    <div class="ml-[7px] text-muted text-sm">免费获得条数</div>
                </div>
                <div class="tasks-content">
                    <div
                        class="tasks-item p-[10px] flex items-center"
                        v-for="(item, index) in centerData"
                        :key="index"
                    >
                        <DecorationImg width="60" height="60" :src="item.image" />
                        <div class="flex-1 min-w-0 ml-[10px]">
                            <div class="text-lg font-medium">
                                {{ item?.customName || item.name }}
                                (<span v-html="typeMap[item.type].num"></span>)
                            </div>
                            <div class="mt-[3px] text-xs text-muted text-justify">
                                <span v-html="typeMap[item.type].desc"></span>
                            </div>
                        </div>
                        <div class="flex-none">
                            <el-button
                                style="--el-color-primary: #f6c459"
                                type="primary"
                                round
                                size="small"
                                class="w-[60px]"
                            >
                                {{ typeMap[item.type].btn_text }}
                            </el-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'
import type options from './options'
import DecorationImg from '../../decoration-img.vue'
type OptionsType = ReturnType<typeof options>
const props = defineProps({
    content: {
        type: Object as PropType<OptionsType['content']>,
        default: () => ({})
    },
    styles: {
        type: Object as PropType<OptionsType['styles']>,
        default: () => ({})
    }
})

const typeMap = {
    1: { num: '0/1', btn_text: '签到', desc: '获得<span class="text-error">1</span>电力值' },
    2: { num: '0/10', btn_text: '去分享', desc: '邀请1人，获得<span class="text-error"> 10 </span>电力值' },
    3: { num: '0/3', btn_text: '去分享', desc: '分享1次，获得<span class="text-error"> 10 </span>电力值' },
    4: { num: '0/4', btn_text: '去完成', desc: '分享1次，获得<span class="text-error"> 10 </span>电力值' },
    5: { num: '0/3', btn_text: '去完成', desc: '分享1次，获得<span class="text-error"> 100 </span>电力值' },
    6: { num: '0/3', btn_text: '去完成', desc: '分享1次，获得<span class="text-error"> 10 </span>电力值' },
    7: { num: '0/3', btn_text: '去完成', desc: '分享1次，获得<span class="text-error"> 10 </span>电力值' },
}

const centerData = computed(() => props.content.data.filter((item) => item.show))
</script>

<style lang="scss" scoped>
.task-center {
    .daily-tasks {
        background: linear-gradient(180deg, #fcf0d1 0%, #ffffff 100px), #fff no-repeat;
        //background-size: 100% 42px;
        border-radius: 7px;
    }
    .tasks-title {
        padding: 0 10px;
        height: 44px;
        display: flex;
        align-items: center;
    }
    .tasks-content {
        padding-bottom: 10px;
        .tasks-item {
            &:not(:last-of-type) {
                border-bottom: 1px solid #e5e5e5;
                margin-bottom: 5px;
            }
        }
    }
}
</style>
