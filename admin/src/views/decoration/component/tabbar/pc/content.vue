<template>
    <div class="pc-aside absolute top-0 bottom-0 left-0 w-[80px]">
        <ElScrollbar>
            <div class="h-full flex flex-col">
                <div class="mb-auto">
                    <div
                        class="nav"
                        :class="[selectActive === 'nav' ? 'nav_active' : 'nav_unactive']"
                        @click="selectNav"
                    >
                        <template v-for="(item, index) in nav" :key="index">
                            <div
                                v-if="item.is_show == '1'"
                                :to="item.link.path"
                                class="nav-item"
                                :class="{
                                    active: index == 0
                                }"
                            >
                                <decoration-img
                                    v-if="showNavIcon"
                                    width="22px"
                                    height="22px"
                                    :src="index == 0 ? item.selected : item.unselected"
                                    fit="cover"
                                >
                                    <template #error>
                                        <span />
                                    </template>
                                </decoration-img>

                                <span
                                    class="text-sm line-clamp-1 mt-[6px]"
                                >
                                    {{ item.name }}
                                </span>
                            </div>
                        </template>
                    </div>
                </div>
                <div>
                    <div
                        class="nav"
                        :class="[selectActive === 'menu' ? 'nav_active' : 'nav_unactive']"
                        @click="selectMenu"
                    >
                        <template v-for="(item, index) in menu" :key="index">
                            <div class="nav-item" v-if="item.is_show == '1'">
                                <decoration-img
                                    width="22px"
                                    height="22px"
                                    :src="item.unselected"
                                    fit="cover"
                                >
                                    <template #error>
                                        <span />
                                    </template>
                                </decoration-img>

                                <span
                                    v-if="item.showName"
                                    class="text-sm line-clamp-1 mt-[6px]"
                                >
                                    {{ item.name }}
                                </span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </ElScrollbar>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'
import DecorationImg from '../../decoration-img.vue'
import { useMenu } from './useMenu'

const props = defineProps({
    showNavIcon: {
        type: Boolean
    },
    nav: {
        type: Array as PropType<any[]>,
        default: () => []
    },
    menu: {
        type: Array as PropType<any[]>,
        default: () => []
    }
})

const { selectActive, selectNav, selectMenu } = useMenu()
</script>
<style lang="scss" scoped>
.pc-aside {

    :deep(.el-scrollbar__view) {
        height: 100%;
    }
    .nav_active {
        border: 1px solid var(--el-color-primary);
    }
    .nav_unactive {
        border: 1px dashed #e5e5e5;
    }
    .nav {
        cursor: pointer;
        .nav-item {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
            padding: 0 12px;
            margin: 25px 0;
            white-space: nowrap;
            &.active {
                background: linear-gradient(90deg, #70c3ec 0%, #426df7 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
        }
    }

    .menu-item {
        display: flex;
        align-items: center;
        height: 38px;
        line-height: 38px;
        padding: 0 12px;
        border-radius: 8px;
        cursor: pointer;
        margin: 3px 0;
        font-size: 13px;
    }
}
</style>
