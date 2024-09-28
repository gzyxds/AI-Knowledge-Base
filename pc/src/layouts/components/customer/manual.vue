<template>
    <el-popover
        placement="left"
        width="auto"
        trigger="hover"
        :show-arrow="false"
        transition="custom-popover"
    >
        <template #reference>
            <slot />
        </template>
        <div class="text-center">
            <el-image class="w-[150px] h-[150px]" :src="customerData.qr_code" />
            <div
                v-if="customerData.title?.status == 1"
                class="font-medium text-tx-primary mt-2"
            >
                {{ customerData.title?.value }}
            </div>
            <div class="mt-2" v-if="customerData.service_time?.status == 1">
                服务时间：{{ customerData.service_time?.value }}
            </div>
            <div class="mt-2" v-if="customerData.phone?.status == 1">
                联系电话：{{ customerData.phone?.value }}
            </div>
        </div>
    </el-popover>
</template>

<script setup lang="ts">
import { useAppStore } from '@/stores/app'

const appStore = useAppStore()

const customerData = computed(() => appStore.getManualKf)
</script>

<style scoped lang="scss"></style>