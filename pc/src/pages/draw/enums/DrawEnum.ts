export enum DrawModeEnum {
    SD = 'sd',
    MJ_GOAPI = 'mj_goapi',
    DALLE3 = 'dalle3'
}

export enum DrawTypeEnum {
    txt2img = 'txt2img',
    img2img = 'img2img',
    SCALE2D = 'scale2d'
}

export const DrawResultTypeEnum = {
    1: '文生图',
    2: '图生图',
    3: '选中放大',
    4: '选中变换'
}
