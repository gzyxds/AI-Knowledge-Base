<template>
    <div>
        <NuxtLink to="/user/member">
            <div class="member-btn dark:!bg-[#333] dark:!text-tx-primary">
                <img
                    src="@/assets/image/icon_crown.png"
                    class="w-[13px] h-[12px] mr-1"
                    alt=""
                />
                <span v-if="userStore.userInfo?.package_is_overdue">
                    会员已到期
                </span>
                <span v-else>
                    {{ userStore.userInfo.package_name ? userStore.userInfo.package_name : '开通会员' }}
                </span>
            </div>
        </NuxtLink>
    </div>
</template>
<script setup lang="ts">
import {useUserStore} from '~/stores/user'

const userStore = useUserStore()
</script>

<style lang="scss" scoped>
.member-btn {
    display: flex;
    align-items: center;
    padding: 5px 8px;
    background: #fbf3e8;
    border-radius: 8px;
    color: #653f17;
    @apply cursor-pointer;
}
</style>
