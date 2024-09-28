<template>
  <popup ref="popRef" width="700px" :title="replyTitle">
    <div>
      <div class="whitespace-pre-wrap">
        {{ replyContent }}
      </div>
    </div>
  </popup>
</template>

<script setup lang="ts">
const popRef = shallowRef()
const replyTitle = ref('')
const replyContent = ref('')

const open = (reply: any, title: string) => {
  replyTitle.value = title
  popRef.value?.open()
  replyContent.value = reply
}

defineExpose({ open })
</script>

<style scoped lang="scss"></style>
