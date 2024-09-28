import type { FetchOptions } from 'ofetch'
import { merge } from 'lodash-es'
import { Request } from './request'
import { RequestCodeEnum, RequestMethodsEnum } from '@/enums/requestEnums'
import feedback from '@/utils/feedback'
import { useUserStore } from '@/stores/user'
import { baseUrl, urlPrefix, version } from '@/config'
import { client } from '@/utils/client'

export function createRequest(opt?: Partial<FetchOptions>) {
  const userStore = useUserStore()
  const defaultOptions: FetchOptions = {
    url: '',
    // 基础接口地址
    baseURL: baseUrl,
    //请求头
    headers: {
      version,
      terminal: client as any
    },
    retry: 0,

    requestOptions: {
      apiPrefix: urlPrefix,
      isTransformResponse: true,
      isReturnDefaultResponse: false,
      withToken: true,
      isParamsToData: true,
      requestInterceptorsHook(options) {
        const { apiPrefix, isParamsToData, withToken } = options.requestOptions
        // 拼接请求前缀
        if (apiPrefix) {
          options.url = `${apiPrefix}${options.url}`
        }
        const params = options.params || {}
        options.method = options.method || RequestMethodsEnum.GET
        // POST请求下如果无data，则将params视为data
        if (
          isParamsToData &&
          !Reflect.has(options, 'body') &&
          options.method?.toUpperCase() === RequestMethodsEnum.POST
        ) {
          options.body = params
          options.params = {}
        }
        const headers = options.headers || {}
        if (withToken) {
          const token = userStore.token
          //@ts-ignore
          headers['token'] = token
        }
        options.headers = headers
        return options
      },
      async responseInterceptorsHook(response, options) {
        // sse底层弹出报错
        if ((response as any).sse) {
          const { msg, code } = response._data

          switch (code) {
            case 10006:
            case RequestCodeEnum.FAIL:
              msg && feedback.msgError(msg)
              break
          }
          return
        }
        const { isTransformResponse, isReturnDefaultResponse } =
          options.requestOptions
        //返回默认响应，当需要获取响应头及其他数据时可使用
        if (isReturnDefaultResponse) {
          return response
        }
        // 是否需要对数据进行处理
        if (!isTransformResponse) {
          return response._data
        }
        const { code, data, show, msg } = response._data
        switch (code) {
          case RequestCodeEnum.SUCCESS:
            if (show) {
              msg && feedback.msgSuccess(msg)
            }
            return data
          case RequestCodeEnum.FAIL:
          case RequestCodeEnum.KEY_INVALID:
            if (show) {
              msg && feedback.msgError(msg)
            }
            return Promise.reject(msg)
          case RequestCodeEnum.LOGIN_EMPTY:
            const router = useRouter()
            userStore.logout()
            router.push('/')
            return
          case RequestCodeEnum.LOGIN_FAILURE:
            userStore.logout()
            if (options.method?.toUpperCase() === RequestMethodsEnum.POST) {
              userStore.toggleShowLogin(true)
            }

            return Promise.reject(data)
          default:
            return data
        }
      },
      responseInterceptorsCatchHook(err) {
        return err
      }
    }
  }
  return new Request(
    // 深度合并
    merge(defaultOptions, opt || {})
  )
}
