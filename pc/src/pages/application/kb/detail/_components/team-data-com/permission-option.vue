<template>
    <div class="flex items-center p-4 hover:bg-page rounded-xl" @click="selectOption">
        <div class="flex-1">
            <div class="font-medium text-tx-primary">{{ label }}</div>
            <div class="text-tx-placeholder text-xs mt-2">{{ description }}</div>
        </div>
        <el-checkbox
            v-model="internalModelValue"
            :true-value="value"
            label=""
            size="large"
            class="ml-2"
        />
    </div>
</template>

<script setup lang="ts">
import { ref, watch, defineProps, defineEmits } from 'vue';
import { ElCheckbox } from "element-plus";

const props = defineProps({
    label: String,
    description: String,
    value: Number,
    modelValue: Number,
});

const emit = defineEmits(['change']);

const internalModelValue = ref(props.modelValue);

watch(() => props.modelValue, (newValue) => {
    internalModelValue.value = newValue;
});

const selectOption = () => {
    emit('change', props.value);
};
</script>

<style scoped>
/* Additional styles if necessary */
</style>