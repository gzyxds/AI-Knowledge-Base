<template>
    <popup width="700px" title="查看回复" ref="popRef">
        <div
            v-for="(item, index) in replyContent"
            :key="index"
            class="mb-[20px]"
            :class="{
                ' pt-[15px] border-t border-solid border-br-light': index > 0
            }"
        >
            <Markdown :content="item" />
        </div>
    </popup>
</template>

<script setup lang="ts">
import { ref, shallowRef } from 'vue'

const popRef = shallowRef()
const replyContent = ref('')

const open = (reply: any) => {
    popRef.value?.open()
    replyContent.value = reply
}

defineExpose({ open })
</script>

<style scoped lang="scss"></style>
