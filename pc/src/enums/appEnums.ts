//菜单主题类型
export enum ThemeEnum {
  LIGHT = 'light',
  DARK = 'dark'
}

// 菜单类型
export enum MenuEnum {
  CATALOGUE = 'M',
  MENU = 'C',
  BUTTON = 'A'
}

// 屏幕
export enum ScreenEnum {
  SM = 640,
  MD = 768,
  LG = 1024,
  XL = 1280,
  '2XL' = 1536
}

export enum SMSEnum {
  LOGIN = 'YZMDL',
  BIND_MOBILE = 'BDSJHM',
  CHANGE_MOBILE = 'BGSJHM',
  FIND_PASSWORD = 'ZHDLMM',
  REGISTER = 'YZMZC'
}

export enum PolicyAgreementEnum {
  SERVICE = 'service',
  PRIVACY = 'privacy',
  PAY = 'pay',
  DISTRIBUTION = 'distribution'
}

/* 消息类型 */
export const EMsg = {
  TEXT: 1,
  IMAGE: 2,
  GOODS: 3
}

export enum LoginPopupTypeEnum {
  LOGIN = 0,
  FORGOT_PWD_MOBILE = 1,
  FORGOT_PWD_MAILBOX = 2,
  REGISTER = 3,
  BIND_MOBILE = 4,
  BIND_WEIXIN = 5,
}

// 客户端
export enum ClientEnum {
  WEIXIN_OA = 2, // 微信-公众号
  H5 = 3, // H5
  PC = 4 //PC
}

export enum DatabaseEnum {
  Document = 1,
  QAndA = 2
}

export const DatabaseMap = {
  [DatabaseEnum.Document]: '文档型',
  [DatabaseEnum.QAndA]: '问答型'
}

export enum ShareSquareEnum {
  DRAW = 1,
  MUSIC = 2,
  VIDEO = 3
}