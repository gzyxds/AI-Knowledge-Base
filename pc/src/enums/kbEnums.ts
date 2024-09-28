export enum DatabaseEnum {
  Document = 1,
  QAndA = 2
}

export const DatabaseMap = {
  [DatabaseEnum.Document]: '文档型',
  [DatabaseEnum.QAndA]: '问答型'
}
