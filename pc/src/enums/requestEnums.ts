export enum ContentTypeEnum {
  EVENT_STREAM = 'text/event-stream',
  // json
  JSON = 'application/json',
  // form-data   上传资源（图片，视频）
  FORM_DATA = 'multipart/form-data'
}

export enum RequestMethodsEnum {
  GET = 'GET',
  POST = 'POST'
}

export enum RequestCodeEnum {
  SUCCESS = 1,
  FAIL = 0,
  LOGIN_EMPTY = -2,
  LOGIN_FAILURE = -1,
  OPEN_NEW_PAGE = 2,
  KEY_INVALID = 1200
}
