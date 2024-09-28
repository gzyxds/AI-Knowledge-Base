<template>
    <div class="p-main h-full flex flex-col">
        <div class="flex">
            <div class="flex bg-page p-[5px] rounded-[10px]">
                <div
                    class="leading-8 w-[120px] text-center rounded-[10px] cursor-pointer"
                    :class="{
            'bg-primary text-white': currentKey == item.key
          }"
                    v-for="item in tabLists"
                    :key="item.key"
                    @click="currentKey = item.key"
                >
                    {{ item.name }}
                </div>
            </div>
        </div>

        <div class="flex-1 min-h-0" v-if="currentTab">
            <component :is="currentTab.component" :app-id="appId"/>
        </div>
    </div>
</template>
<script lang="ts" setup>
import Data from './data.vue'
import Record from './record.vue'

const props = defineProps<{
    appId: string | number
}>()

const route = useRoute()
const tabLists = reactive([
    {
        name: '对话数据',
        key: 'data',
        component: markRaw(Data)
    },
    {
        name: '对话记录',
        key: 'record',
        component: markRaw(Record)
    }
])
const currentKey = ref('data')
const currentTab = computed(() => {
    return tabLists.find((item) => item.key == currentKey.value)
})

onMounted(() => {
    if (route.query.dialogue) {
        currentKey.value = 'record'
    }
})
</script>
