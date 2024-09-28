<template>
  <div class="example p-main rounded-lg sm:w-[360px] bg-white shadow">
    <div class="font-medium text-lg">你可以试试这样问</div>
    <div class="flex items-stretch">
      <div>
        <div
          v-for="(item, index) in dataRenter"
          :key="index"
          class="px-[15px] py-[11px] my-[10px] cursor-pointer bg-[#F5F9FF] rounded-md flex items-center hover:text-primary"
          @click="emit('click-item', item.name)"
        >
          <span class="line-clamp-1 flex-1 min-w-0">
            {{ item.name }}
          </span>
          <Icon class="!text-primary" name="el-icon-Position" />
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
const props = withDefaults(
  defineProps<{
    data: any[]
    bg: string
  }>(),
  {
    data: () => [],
    bg: '#fff'
  }
)
const emit = defineEmits<{
  (event: 'click-item', value: string): void
}>()

const dataRenter = computed(() =>
  props.data.filter(({ status }) => status === '1')
)
</script>

<style lang="scss" scoped>
.example {
  box-shadow: 0 3px 10px #ebeefd;
}
</style>
