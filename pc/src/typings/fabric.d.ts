import 'fabric'
declare module 'fabric' {
  namespace fabric {
    interface Object {
      id?: string
      customType?: string
      data?: Record<string, any>
      index?: number
    }
    interface IImageOptions {
      index?: number
    }
  }
}
