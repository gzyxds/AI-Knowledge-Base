<template>
    <div class="app-center">
        <div class="header">
            <div class="flex items-center">
                <img
                    src="../../../image/app_center_robot.png"
                >
                <span class="title">{{ content.mobile_title }}</span>
            </div>
            <div class="flex items-center search-container">
                <input
                    v-model="searchQuery"
                    class="search flex-1"
                    placeholder="输入您想搜索的应用"
                    placeholderClass="search-placeholder"
                />
                <div
                    class="flex justify-center items-center w-[40px] h-[40px]"
                >
                    <Icon name="el-icon-Search"></Icon>
                </div>
            </div>
        </div>
        <div class="main">
            <div class="grid grid-cols-2" style="gap: 12px;">
                <div
                    class="flex items-center p-[10px] bg-white rounded-lg"
                    v-for="(item, index) in showList"
                    :key="index"
                >
                    <div class="flex-none">
                        <DecorationImg :src="item.image" width="44" height="44" border-radius="18"/>
                    </div>
                    <div class="ml-[10px] py-[10px]">
                        <div class="font-medium text-tx-primary text-lg line-clamp-1">{{ item.title }}</div>
                        <span class="text-tx-secondary text-xs mt-1 line-clamp-1">{{ item.desc }}</span>
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
const searchQuery = ref('')

const showList = computed(() => {
    return props.content.data.filter((item: any) => item.is_show == 1) || []
})
</script>

<style lang="scss" scoped>
.app-center {
    height: 100%;
    min-height: 615px;
    background: linear-gradient(180deg, #E0EBFD 0%, rgba(224, 235, 254, 0) 100%);

    .header {
        padding: 0 22px;
        padding-top: 15px;
        background-image: url("../../../image/app_center_bg.png");
        background-repeat: no-repeat;
        background-size: 100% auto;

        img {
            width: 93px;
            height: 110px;
        }

        span {
            font-family: Source Han Sans;
            font-size: 18px;
            font-weight: 500;
            margin-left: 10px;
            margin-top: 10px;
        }

        .search-container {
            position: relative;
            z-index: 10;
            overflow: hidden;
            margin-top: 10px;
            border-radius: 10px;
            background-color: #ffffff;
        }

        .search {
            padding: 10px 15px;
        }

        .search-placeholder {
            font-size: 13px;
            font-weight: normal;
            color: #999999;
        }
    }

    .main {
        padding: 0 12px;
        padding-top: 30px;
    }
}
</style>
