import { ref } from 'vue'

const selectActive = ref('nav')

export const useMenu = () => {
    const selectNav = () => {
        selectActive.value = 'nav'
    }

    const selectMenu = () => {
        selectActive.value = 'menu'
    }

    onUnmounted(() => {
        selectActive.value = 'nav'
    })

    return {
        selectActive,

        selectNav,
        selectMenu
    }
}