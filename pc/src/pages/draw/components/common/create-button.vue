<template>
    <div class="absolute bottom-0 left-0 bg-body p-4 w-full z-10">
        <el-button
            size="large"
            type="primary"
            class="w-full"
            :disabled="disabled"
            @click="create"
            :loading="createLoading"
        >
            <div v-if="createLoading">正在请求中</div>
            <div v-else>
                <span class="text-base font-bold">立即生成</span>
                <span v-if="config.is_member" class="text-sm ml-2"
                    >会员免费</span
                >
                <span
                    v-if="config.power != 0 && !config.is_member && userStore.isLogin"
                    class="text-sm ml-2"
                    >消耗{{ config.power || '--' }}{{ appStore.getTokenUnit }}</span
                >
            </div>
        </el-button>
    </div>
</template>

<script lang="ts" setup>
import {
    config,
    createLoading,
    formData,
    createTask,
    checkUserLogin
} from '../../hooks/useDrawEffect'
import { useUserStore } from '~/stores/user'
import { useAppStore } from '~/stores/app'

const emit = defineEmits(['create'])

defineProps({
    disabled: {
        type: Boolean,
        default: false
    }
})

const appStore = useAppStore()
const userStore = useUserStore()

const create = () => {
    if (checkUserLogin()) return
    createTask(formData.value)
}
</script>

<style lang="scss" scoped></style>
