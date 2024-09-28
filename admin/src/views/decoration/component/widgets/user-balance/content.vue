<template>
    <div class="mx-[10px] mt-[-10px] user-balance">
        <div class="bg-white rounded-lg text-center">
            <div class="flex p-[20px]">
                <div class="flex-1">
                    <div>-</div>
                    <div class="form-tips">电力值数量</div>
                </div>
                <div class="flex-1">
                    <div>-</div>
                    <div class="form-tips">智能体</div>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'
import type options from './options'

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
</script>

<style lang="scss" scoped>
.user-balance {
    background: linear-gradient(
            44.7deg,
            #eaffff 0%,
            #faf6ff 50%,
            #f2f3ff 63%,
            #eaffff 100%
    );
}
</style>
