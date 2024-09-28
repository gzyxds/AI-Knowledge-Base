<template>
    <div class="p-[20px] bg-body rounded-[12px] flex flex-col h-full">
        <div class="flex flex-none">
            <div class="p-[8px] flex bg-page rounded-[10px] font-medium">
                <div
                    :class="{ selectType: selectType === item.type }"
                    class="px-[30px] py-[10px] cursor-pointer"
                    v-for="(item, index) in typeList"
                    :key="index"
                    @click="tabsChange(item.type)"
                >
                    <span>{{ item.name }}</span>
                </div>
            </div>
        </div>

        <component v-if="componentShow" :is="currentComponent" />
    </div>
</template>

<script setup lang="ts">
import Recharge from './_components/recharge.vue'
import Member from './_components/member.vue'

const route = useRoute()
const selectType = ref<string>('member')
const currentComponent = ref(Recharge)
const componentShow = ref<boolean>(true)

const typeList = ref([
    {
        name: '会员开通记录',
        type: 'member',
    },
    {
        name: '充值记录',
        type: 'recharge',
    },
])

watch(() => route.query.time, async () => {
    componentShow.value = false
    await nextTick()
    componentShow.value = true
})

const tabsChange = (type: string) => {
    selectType.value = type
    currentComponent.value = type === 'recharge' ? Recharge : Member
}

onMounted(() => {
    tabsChange(route.query.type as string || 'member') // Default to 'member'
})
</script>

<style scoped lang="scss">
.selectType {
    color: white;
    border-radius: 8px;
    @apply bg-primary;
}
</style>