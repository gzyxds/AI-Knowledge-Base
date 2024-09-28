import type {
  $Fetch,
  FetchOptions,
  FetchResponse,
  FileParams,
  RequestOptions
} from 'ofetch'
import { $fetch } from 'ofetch'
import { merge } from 'lodash-es'
import { isFunction } from '../validate'
import { objectToQuery } from '../util'
import { Sse } from './sse'
import { ContentTypeEnum, RequestMethodsEnum } from '@/enums/requestEnums'
export interface UserFetchOptions extends Partial<FetchOptions> {
  url: string
}

export class Request {
  private requestOptions: RequestOptions
  private fetchInstance: $Fetch
  constructor(private fetchOptions: FetchOptions) {
    this.fetchInstance = $fetch.create(fetchOptions)
    this.requestOptions = fetchOptions.requestOptions
  }

  getInstance() {
    return this.fetchInstance
  }
  /**
   * @description get请求
   */
  get(
    fetchOptions: UserFetchOptions,
    requestOptions?: Partial<RequestOptions>
  ) {
    return this.request(
      { ...fetchOptions, method: RequestMethodsEnum.GET },
      requestOptions
    )
  }

  /**
   * @description eventStream请求，无法使用$fetch
   */
  sse(
    fetchOptions: UserFetchOptions,
    requestOptions?: Partial<RequestOptions>
  ) {
    let mergeOptions = merge({}, this.fetchOptions, fetchOptions)
    mergeOptions.requestOptions = merge({}, this.requestOptions, requestOptions)
    const {
      requestInterceptorsHook,
      responseInterceptorsHook,
      responseInterceptorsCatchHook
    } = this.requestOptions
    if (requestInterceptorsHook && isFunction(requestInterceptorsHook)) {
      mergeOptions = requestInterceptorsHook(mergeOptions)
    }
    mergeOptions.url = `${mergeOptions.baseURL}${mergeOptions.url}`

    if (
      mergeOptions.method?.toUpperCase() === RequestMethodsEnum.GET &&
      mergeOptions.params
    ) {
      mergeOptions.url = `${mergeOptions.url}?${objectToQuery(
        mergeOptions.params!
      )}`
    }
    if (mergeOptions.method?.toUpperCase() === RequestMethodsEnum.POST) {
      mergeOptions.body = JSON.stringify(mergeOptions.body)
    }
    mergeOptions.headers = {
      accept: ContentTypeEnum.EVENT_STREAM,
      'Content-Type': ContentTypeEnum.JSON,
      ...mergeOptions.headers
    }

    const sseInstance = new Sse(mergeOptions.url, mergeOptions as RequestInit)
    sseInstance.addEventListener('error', (ev) => {
      if (ev.errorType === 'responseError') {
        responseInterceptorsHook?.(
          {
            ...sseInstance.response!,
            _data: {
              ...ev.data,
              msg: ev.data?.message
            },
            sse: true
          } as any,
          mergeOptions
        )
      } else {
        responseInterceptorsCatchHook?.(ev)
      }
    })
    sseInstance.connect()

    return sseInstance
  }

  /**
   * @description post请求
   */
  post(
    fetchOptions: UserFetchOptions,
    requestOptions?: Partial<RequestOptions>
  ) {
    return this.request(
      { ...fetchOptions, method: RequestMethodsEnum.POST },
      requestOptions
    )
  }
  /**
   * @description: 文件上传
   */
  // fetch暂时无法实现上传进度
  uploadFile(
    config: UserFetchOptions,
    params: FileParams,
    options?: Partial<RequestOptions>
  ) {
    const formData = new FormData()
    const customFilename = params.name || 'file'
    formData.append(customFilename, params.file)
    if (params.data) {
      Object.keys(params.data).forEach((key) => {
        const value = params.data![key]
        if (isObject(value)) {
          formData.append(key, JSON.stringify(value))
        } else {
          formData.append(key, value)
        }
      })
    }
    return this.post(
      {
        ...config,
        body: formData
      },
      options
    )
  }
  /**
   * @description 请求函数
   */
  request(
    fetchOptions: UserFetchOptions,
    requestOptions?: Partial<RequestOptions>
  ): Promise<any> {
    let mergeOptions = merge({}, this.fetchOptions, fetchOptions)
    mergeOptions.requestOptions = merge({}, this.requestOptions, requestOptions)
    const {
      requestInterceptorsHook,
      responseInterceptorsHook,
      responseInterceptorsCatchHook
    } = this.requestOptions
    if (requestInterceptorsHook && isFunction(requestInterceptorsHook)) {
      mergeOptions = requestInterceptorsHook(mergeOptions)
    }
    return new Promise((resolve, reject) => {
      return this.fetchInstance
        .raw(mergeOptions.url, mergeOptions)
        .then(async (response: FetchResponse<any>) => {
          if (
            responseInterceptorsHook &&
            isFunction(responseInterceptorsHook)
          ) {
            try {
              response = await responseInterceptorsHook(response, mergeOptions)

              resolve(response)
            } catch (error) {
              reject(error)
            }
            return
          }
          resolve(response)
        })
        .catch((err) => {
          if (
            responseInterceptorsCatchHook &&
            isFunction(responseInterceptorsCatchHook)
          ) {
            reject(responseInterceptorsCatchHook(err))
            return
          }
          reject(err)
        })
    })
  }
}
