/**
 * @description 获取请求域名
 */
export function getApiUrl() {
  return useRuntimeConfig().public.apiUrl
}

/**
 * @description 获取环境
 */
export function isDev() {
  console.log(useRuntimeConfig())
  return useRuntimeConfig()
}
