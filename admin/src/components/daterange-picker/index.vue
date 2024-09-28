<template>
    <el-date-picker
        v-model="content"
        type="datetimerange"
        range-separator="-"
        start-placeholder="开始时间"
        end-placeholder="结束时间"
        :value-format="valueFormat"
        clearable
    ></el-date-picker>
</template>

<script lang="ts" setup>
import { withDefaults, computed } from 'vue'

/* Props S */
const props = withDefaults(
    defineProps<{
        valueFormat: string
        second: boolean
        startTime?: string | number
        endTime?: string | number
    }>(),
    {
        valueFormat: 'YYYY-MM-DD HH:mm:ss',
        second: false,
        startTime: '',
        endTime: ''
    }
)
const emit = defineEmits(['update:startTime', 'update:endTime'])

const content = computed<any>({
    get: () => {
        if (props.second) {
            return [props.startTime as number * 1000, props.endTime as number * 1000]
        }
        return [props.startTime, props.endTime]
    },
    set: (value: Event | any) => {
        if (value === null) {
            emit('update:startTime', '')
            emit('update:endTime', '')
        } else {
            if (props.second) {
                emit('update:startTime', value[0] / 1000)
                emit('update:endTime', value[1] / 1000)
                return
            }
            emit('update:startTime', value[0])
            emit('update:endTime', value[1])
        }
    }
})
</script>
