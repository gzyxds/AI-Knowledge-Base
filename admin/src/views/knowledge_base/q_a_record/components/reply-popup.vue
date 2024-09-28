<template>
    <popup width="700px" title="查看回复" ref="popRef">
        <div>
            <div class="whitespace-pre-wrap">{{ replyContent }}</div>
        </div>
    </popup>
</template>

<script setup lang="ts">
const popRef = shallowRef()
const replyContent = ref('')

const open = (reply: any) => {
    popRef.value?.open()
    replyContent.value = reply
}

defineExpose({ open })
</script>

<style scoped lang="scss"></style>
