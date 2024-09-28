import type { RouterConfig } from '@nuxt/schema'
// https://router.vuejs.org/api/interfaces/routeroptions.html
const PAGE_COMPONENTS_DIR_NAME = '_components'
export default <RouterConfig>{
  routes: (_routes) => {
    return _routes.filter(
      (route) => !route.path.includes(PAGE_COMPONENTS_DIR_NAME)
    )
  }
}
