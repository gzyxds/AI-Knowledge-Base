<template>
    <div>
        <NuxtLayout name="default">
            <div class="flex p-4 h-full">
                <ControlPanel
                    ref="controlPanelRef"
                    @update="descUpdate"
                    @history="handleShowHistory"
                    @refresh="refresh"
                />
                <div
                    class="flex-1 min-w-0 h-full ml-4 bg-body rounded-[12px] relative"
                >
                    <div class="h-full flex" v-if="!showHistory">
                        <div class="h-full flex-1 min-w-0 p-[15px]">
                            <MindMapPreview
                                ref="mindMapPreviewRef"
                                v-if="descInput.length"
                            />
                            <EmptyView v-else />
                        </div>
                        <div
                            class="border-l-[1px] border-solid border-br-light w-[300px]"
                        >
                            <History
                                ref="historyRef"
                                :currentId="selectId"
                                @view="handlePreview"
                                @history="handleShowHistory"
                            />
                        </div>
                    </div>
                    <MindMapHistory
                        v-else
                        @view="handlePreview"
                        @history="handleShowHistory"
                    />
                </div>
            </div>
        </NuxtLayout>
    </div>
</template>

<script setup lang="ts">
import ControlPanel from './component/control-panel.vue'
import MindMapPreview from './component/mind-map-preview.vue'
import EmptyView from './component/empty-view.vue'
import MindMapHistory from './component/history-all.vue'
import History from './component/history.vue'
import { useUserStore } from '~/stores/user'

const descInput = ref('')
const showHistory = ref(false)
const mindMapPreviewRef = shallowRef<InstanceType<typeof MindMapPreview>>()
const controlPanelRef = shallowRef<InstanceType<typeof ControlPanel>>()
const userStore = useUserStore()
const handleShowHistory = async () => {
    if (!userStore.isLogin) return userStore.toggleShowLogin()
    showHistory.value = !showHistory.value
    if (!showHistory.value) {
        await nextTick()
        mindMapPreviewRef.value?.renderMarkMap(descInput.value)
    }
}
const selectId = ref(-1)
const descUpdate = async (value: string) => {
    descInput.value = value
    showHistory.value = false
    await nextTick()
    mindMapPreviewRef.value?.renderMarkMap(value)
}
const historyRef = shallowRef()
const refresh = () => {
    selectId.value = -1
    historyRef.value?.refresh()
}
const handlePreview = async ({ id, text }: any) => {
    selectId.value = id
    showHistory.value = false
    controlPanelRef.value?.changDescInput(text)
    await nextTick()
    mindMapPreviewRef.value?.renderMarkMap(text)
}
definePageMeta({
    layout: false,
    hiddenFooter: true
})
</script>
