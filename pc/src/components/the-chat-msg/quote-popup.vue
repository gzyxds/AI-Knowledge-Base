<template>
    <ElDialog
        :title="`知识库引用(${quotes.length}条)`"
        :model-value="true"
        width="700px"
        @close="emit('close')"
    >
        <div class="h-[65vh]">
            <ElScrollbar>
                <div
                    v-for="(item, index) in quotes"
                    :key="index"
                    class="py-[6px] px-[10px] border border-solid border-br-light rounded mb-[10px]"
                >
                    <div v-if="item.question" class="text-muted text-sm whitespace-pre-wrap">
                        {{ item.question }}
<!--                        <markdown :content="item.question" />-->
                    </div>
                    <div v-if="item.answer" class="text-muted text-sm whitespace-pre-wrap">
                        {{ item.answer }}
                    </div>
                </div>
            </ElScrollbar>
        </div>
    </ElDialog>
</template>
<script setup lang="ts">
const props = withDefaults(
    defineProps<{
        quotes: any[]
    }>(),
    {
        quotes: () => []
    }
)

const emit = defineEmits(['close'])
</script>
