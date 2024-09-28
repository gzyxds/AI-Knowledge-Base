<template>
  <div class="p-main">
    <PlatformSelect v-if="!currentKey" @click-item="changeKey" />
    <ReleaseWeb v-if="currentKey === 'web'" :app-id="appId" @back="changeKey" />
    <ReleaseJs v-if="currentKey === 'js'" :app-id="appId" @back="changeKey" />
    <ReleaseOa v-if="currentKey === 'oa'" :app-id="appId" @back="changeKey" />
    <ReleaseApi
      v-if="currentKey === 'api'"
      :type="4"
      :app-id="appId"
      @back="changeKey"
    />
    <ReleaseApi
      v-if="currentKey === 'qwx'"
      :type="5"
      :app-id="appId"
      @back="changeKey"
    />
  </div>
</template>
<script setup lang="ts">
import ReleaseWeb from './web.vue'
import ReleaseApi from './api.vue'
import ReleaseJs from './js.vue'
import ReleaseOa from './oa.vue'
import PlatformSelect from './platform-select.vue'
const props = defineProps<{
  appId: string | number
}>()
const currentKey = ref()
const changeKey = (key = '') => {
  currentKey.value = key
}
</script>
