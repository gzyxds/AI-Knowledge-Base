<template>
    <view>
        <uni-file-picker
            v-model="fileList"
            limit="50"
            file-mediatype="all"
            @progress="onProgress"
            @select="selectFile"
            mode="list"
            :del-icon="true"
            :list-styles="{ display: 'none' }"
        >
            <slot />
        </uni-file-picker>
    </view>
</template>

<script lang="ts" setup>
import { ref } from 'vue'

const emits = defineEmits(['importFile'])

const props = withDefaults(
    defineProps<{
        fileExtname?: any[] | string
    }>(),
    {
        fileExtname: ''
    }
)
const fileList = ref([])
const selectFile = (e) => {
    emits('importFile', e)
    fileList.value = []
}
</script>
<style lang="scss" scoped>
::v-deep uni-view.uni-file-picker__lists {
    display: none !important;
}
</style>
