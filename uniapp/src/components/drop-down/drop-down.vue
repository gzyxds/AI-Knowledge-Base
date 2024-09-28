<template>
    <view
        v-if="showMask && showDropdown"
        class="dropdown-mask"
        @click="showDropdown = !showDropdown"
    ></view>
    <view class="dropdown">
        <view @click="showDropdown = !showDropdown">
            <slot></slot>
        </view>
        <view
            class="dropdown-menu"
            v-show="showDropdown"
            :class="menuClass"
            @click="showDropdown = !showDropdown"
        >
            <!-- 下拉菜单的内容 -->
            <slot name="menu"></slot>
        </view>
    </view>
</template>

<script lang="ts" setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
    mode: String, // 默认向下展开
    show: Boolean,
    showMask: {
        type: Boolean,
        default: true
    }
})
const emit = defineEmits<{
    (event: 'update:show', value: boolean): void
}>()
const showDropdown = ref<boolean>(false)

watch(
    () => props.show,
    (value) => {
        showDropdown.value = value
    },
    {
        immediate: true,
        flush: 'post'
    }
)

watch(showDropdown, (value) => {
    emit('update:show', value)
})

const menuClass = computed(() => {
    return {
        'dropdown-menu-up': props.mode === 'up',
        'dropdown-menu-right': props.mode === 'right',
        'dropdown-menu-down': props.mode === 'down',
        'dropdown-menu-left': props.mode === 'left'
    }
})
</script>

<style scoped>
.dropdown {
    position: relative;
    flex: 1;
    width: auto;
}

.dropdown-mask {
    position: absolute;
    z-index: 3;
    right: 0;
    left: 0;
    top: 0;
    bottom: 0;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    width: auto;
    height: auto;
    white-space: pre;
    border-radius: 12rpx;
    background-color: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    z-index: 9999;
}

.dropdown-menu-up {
    width: 100%;
    inset: auto auto 130% 0%;
    /*transform: translateX(-50%);*/
}

.dropdown-menu-right {
    top: 0;
    left: 100%;
    transform: translateX(5rpx);
}

.dropdown-menu-down {
    top: 100%;
    left: 0;
    transform: translateY(10rpx);
    /* inset: 50% auto 0% 0%;
    transform: translateY(50%); */
}

.dropdown-menu-left {
    inset: auto auto 50% 130%;
    transform: translateY(-50%);
}
</style>
