const ERR_MSG_FAIL = 'chooseFile:fail'
interface BaseOptions {
    type: 'file' | 'image' | 'video'
}
type ChooseFileOptions = UniApp.ChooseFileOptions
type ChooseImageOptions = UniApp.ChooseImageOptions
type ChooseVideoOptions = UniApp.ChooseVideoOptions
type ChooseOptions = BaseOptions &
    (ChooseFileOptions | ChooseImageOptions | ChooseVideoOptions)

export interface ChooseResult {
    tempFilePaths: string[] | string
    tempFiles: any[]
    errMsg?: string
}

export interface FileData {
    name: string
    uuid: number
    extname: string
    fileType: string
    url: string
    path: string
    size: string
    progress: number
    status: 'ready' | 'success' | 'error'
    errMsg: string
}

function chooseImage(opts: ChooseImageOptions) {
    const {
        count,
        sizeType = ['original', 'compressed'],
        sourceType,
        extension
    } = opts
    return new Promise<ChooseResult>((resolve, reject) => {
        uni.chooseImage({
            count,
            sizeType,
            sourceType,
            extension,
            success(res) {
                resolve(normalizeFileRes(res as ChooseResult, 'image'))
            },
            fail(res) {
                reject({
                    errMsg: res.errMsg.replace('chooseImage:fail', ERR_MSG_FAIL)
                })
            }
        })
    })
}

function chooseVideo(opts: ChooseVideoOptions) {
    const { camera, compressed, maxDuration, sourceType, extension } = opts
    return new Promise<ChooseResult>((resolve, reject) => {
        uni.chooseVideo({
            camera,
            compressed,
            maxDuration,
            sourceType,
            extension,
            success(res) {
                const { tempFilePath, duration, size, height, width } = res
                resolve(
                    normalizeFileRes(
                        {
                            errMsg: 'chooseVideo:ok',
                            tempFilePaths: [tempFilePath],
                            tempFiles: [
                                {
                                    name:
                                        (res.tempFile && res.tempFile.name) ||
                                        '',
                                    path: tempFilePath,
                                    size,
                                    type:
                                        (res.tempFile && res.tempFile.type) ||
                                        '',
                                    width,
                                    height,
                                    duration,
                                    fileType: 'video'
                                }
                            ]
                        },
                        'video'
                    )
                )
            },
            fail(res) {
                reject({
                    errMsg: res.errMsg.replace('chooseVideo:fail', ERR_MSG_FAIL)
                })
            }
        })
    })
}

function chooseAll(opts: ChooseFileOptions) {
    const { count, extension } = opts
    return new Promise<ChooseResult>((resolve, reject) => {
        let chooseFile = uni.chooseFile
        if (
            typeof wx !== 'undefined' &&
            typeof wx.chooseMessageFile === 'function'
        ) {
            chooseFile = wx.chooseMessageFile
        }
        if (typeof chooseFile !== 'function') {
            return reject({
                errMsg:
                    ERR_MSG_FAIL +
                    ' 请指定 type 类型，该平台仅支持选择 image 或 video。'
            })
        }
        chooseFile({
            type: 'all',
            count,
            extension,
            success(res) {
                resolve(normalizeFileRes(res as ChooseResult))
            },
            fail(res) {
                reject({
                    errMsg: res.errMsg.replace('chooseFile:fail', ERR_MSG_FAIL)
                })
            }
        })
    })
}

function normalizeFileRes(res: ChooseResult, fileType?: BaseOptions['type']) {
    res.tempFiles.forEach((item) => {
        if (!item.name) {
            item.name = item.path.substring(item.path.lastIndexOf('/') + 1)
        }
        if (fileType) {
            item.fileType = fileType
        }
    })
    if (!res.tempFilePaths) {
        res.tempFilePaths = res.tempFiles.map((file) => file.path)
    }
    return res
}

function chooseFile(
    opts: ChooseOptions = {
        type: 'file'
    }
): Promise<ChooseResult> {
    if (opts.type === 'image') {
        return chooseImage(opts as ChooseImageOptions)
    } else if (opts.type === 'video') {
        return chooseVideo(opts as ChooseVideoOptions)
    }
    return chooseAll(opts as ChooseFileOptions)
}

/**
 * @description 获取文件扩展名和文件名
 * @param name
 * @returns
 */
const getFileExt = (name: string) => {
    const lastLen = name.lastIndexOf('.')
    const len = name.length
    const fileName = name.substring(0, lastLen)
    const ext = name.substring(lastLen + 1, len)
    return {
        name: fileName,
        ext
    }
}
/**
 * @description 路径中获取文件名称
 * @param name
 * @returns
 */
const getFileName = (path: string) => {
    const lastLen = path.lastIndexOf('.')
    const lastPath = path.lastIndexOf('/')
    // 不是文件
    if (lastLen === -1) return path
    return path.substring(lastPath + 1)
}

const normalizeFileData = (files: any) => {
    const fileFullName = getFileExt(files.name)
    const extname = fileFullName.ext.toLowerCase()
    const filedata: FileData = {
        name: files.name,
        uuid: files.uuid,
        extname: extname || '',
        fileType: files.fileType,
        path: files.path,
        url: files.path,
        size: files.size,
        progress: 0,
        status: 'ready',
        errMsg: ''
    }
    return filedata
}

// 检验扩展名是否正确
const getFilesByExtname = (res: ChooseResult, extname: string[] = []) => {
    const filePaths: any[] = []
    const files: any[] = []
    if (!extname.length) {
        return {
            filePaths: res.tempFilePaths,
            files: res.tempFiles
        }
    }
    res.tempFiles.forEach((v) => {
        const fileFullName = getFileExt(v.name)
        const ext = fileFullName.ext.toLowerCase()
        if (extname.indexOf(ext) !== -1) {
            files.push(v)
            filePaths.push(v.path)
        }
    })
    if (files.length !== res.tempFiles.length) {
        uni.showToast({
            title: `当前选择了${res.tempFiles.length}个文件 ，${
                res.tempFiles.length - files.length
            } 个文件格式不正确`,
            icon: 'none',
            duration: 5000
        })
    }
    return {
        filePaths,
        files
    }
}

export {
    chooseFile,
    getFileExt,
    getFilesByExtname,
    normalizeFileData,
    getFileName
}
