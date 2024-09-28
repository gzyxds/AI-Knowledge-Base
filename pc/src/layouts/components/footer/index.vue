<template>
    <footer
        class="layout-footer flex justify-center text-center text-xs py-[8px] bg-transparent"
    >
        <div class="ml-2 text-tx-secondary">
            <template
                v-for="item in appStore.getCopyrightConfig"
                :key="item.key"
            >
                <a
                    v-if="item.key"
                    class="inline-flex items-center justify-center mx-2 hover:underline"
                    :href="item.value"
                    target="_blank"
                >
                    <img
                        v-if="item.icon"
                        :src="item.icon"
                        alt="备案号"
                        style="width: 20px; height: 20px"
                    />
                    <span class="ml-1">{{ item.key }}</span>
                </a>
            </template>
        </div>
    </footer>
</template>
<script lang="ts" setup>
import { useAppStore } from '@/stores/app'
import { PolicyAgreementEnum } from '@/enums/appEnums'

const appStore = useAppStore()
</script>

<style lang="scss" scoped></style>