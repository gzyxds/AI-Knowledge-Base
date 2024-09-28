<template>
    <div
        class="l-textarea"
        :data-error="error"
        :class="{
            error: error,
            focus: isFocus
        }"
        :style="customStyle"
    >
        <textarea
            class="l-textarea__inner"
            :placeholder="placeholder"
            :rows="rows"
            :value="textAreaValue"
            :maxlength="maxlength"
            :style="contentStyle"
            @focus="onFocus"
            @blur="isFocus = false"
            @input="getTextAreaValue"
        />
        <div v-if="showWordLimit" class="l-textarea-length-maxlength">
            <span>{{ lengthText }}</span> / <span>{{ maxlength }}</span>
        </div>
        <slot name="length-suffix"></slot>
    </div>
</template>

<script setup>
import { watch, ref, nextTick } from 'vue'

const props = defineProps({
    placeholder: String,
    maxlength: String,
    error: String,
    rows: String,
    customStyle: Object,
    contentStyle: Object,
    showWordLimit: Boolean,
    modelValue: String
})

const emit = defineEmits(['update:modelValue', 'focus'])
const textAreaValue = ref(props.modelValue)
const isFocus = ref(false)
const lengthText = ref(props.modelValue?.length || 0)

const getTextAreaValue = (e) => {
    const event = e || window.event
    const target = event.srcElement || event.taget
    emit('update:modelValue', target.value)
}

const onFocus = () => {
    isFocus.value = true
    emit('focus')
}

watch(
    () => props.modelValue,
    (val) => {
        textAreaValue.value = val
        lengthText.value = val.length
    }
)
</script>

<style lang="scss" scoped>
.l-textarea {
    width: 100%;
    position: relative;
    border-radius: 12px;
    box-sizing: border-box;
    overflow: hidden;
    transition: border 0.3s;
    line-height: 1.5;
    border: 1px solid transparent;

    @apply bg-page text-tx-regular;
}

.focus {
    border: 1px solid var(--el-color-primary);
}

.l-textarea-length-maxlength {
    position: absolute;
    bottom: 10px;
    right: 10px;
    font-size: 14px;
    color: #909499;
    @apply bg-page;
}

.l-textarea__inner {
    width: 100%;
    padding: 10px 15px;
    box-sizing: border-box;
    transition: border 0.3s;
    outline: none;
    /** 禁止textarea拉伸 */
    resize: none;
    @apply bg-page;
}
</style>
