<template>
    <div>
        <NuxtLayout name="default">
            <div class="p-main">
                <div class="flex h-full flex-col pt-[10px] max-w-[1200px] mx-auto" v-if="tabLists.length">
                    <div class="tab-lists">
                        <div class="flex mx-[-10px]">
                            <div class="tab-item" v-for="(item, index) in tabLists" :key="index" :class="{
                                'is-active': currentType == item.type
                            }" @click="tabChange(item.type)">
                                <span class="w-full">{{ item.name }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 min-h-0">
                        <component v-if="currentItem" :is="currentItem.component" />
                    </div>
                </div>
                <div class="w-full h-full bg-white rounded-[12px] flex items-center justify-center" v-else>
                    <div class="text-xl">功能未开启!</div>
                </div>
            </div>
        </NuxtLayout>
    </div>
</template>
<script setup lang="ts">
import { useAppStore } from '~/stores/app'
import Recharge from './_components/recharge.vue'
const appStore = useAppStore()
const router = useRouter()
const route = useRoute()

const tabLists = computed(() => {
    const tabs = [
        {
            name: '充值中心',
            type: 'recharge',
            show: true,
            component: markRaw(Recharge)
        }
    ]
    return tabs.filter((item) => !!item.show)
})
const type = route.query.type as string
const currentType = ref(type)
const tabChange = (type: string) => {
    currentType.value = type
    router.replace({
        path: '',
        query: {
            type: type
        }
    })
}
const currentItem = computed(() => {
    return tabLists.value.find((item) => item.type === currentType.value)
})
watch(
    tabLists,
    (value) => {
        if (!currentItem.value && value.length) {
            const [first] = value
            currentType.value = first.type
        }
    },
    {
        immediate: true
    }
)

watch(
    () => route.query.type,
    (value) => {
        currentType.value = value as string
    }
)


definePageMeta({
    auth: true,
    layout: false
})
</script>
<style lang="scss" scoped>
.tab-lists {
    background-color: #f2f2f2;
    border-radius: 20px 20px 0 0;
    overflow: hidden;

    .tab-item {
        flex: 1;
        margin: 0 10px;
        display: flex;
        line-height: 50px;
        text-align: center;
        border-radius: 20px 20px 0 0;
        cursor: pointer;
        @apply text-2xl;

        &.is-active {
            background: linear-gradient(90deg, #ffcb58 0%, #f7630e 100%);
            @apply text-white;
        }
    }
}
</style>
