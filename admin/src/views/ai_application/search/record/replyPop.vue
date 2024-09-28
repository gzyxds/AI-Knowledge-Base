<template>
    <popup width="700px" title="查看回复" ref="popRef">
        <div class="mb-[20px]">
            <Markdown :content="replyContent" />
        </div>
    </popup>
</template>

<script setup lang="ts">
import popup from '@/components/popup/index.vue'

const popRef = shallowRef()
const replyContent = ref('')

const open = (reply: any) => {
    popRef.value?.open()
    replyContent.value = reply
}

defineExpose({ open })
</script>

<style scoped lang="scss"></style>
