import { defineStore } from 'pinia'
import { fabric } from 'fabric'
import del_icon from '@/assets/image/close.png'
import { getAvatarDetail, putAvatarSava } from '@/api/digital_human'
import { uploadImage } from '@/api/app'
import FontFaceObserver from 'fontfaceobserver'
export const canvasSizeData = {
  1: {
    resolution: 1,
    label: '9:16',
    width: 253.125,
    height: 450,
    ratio: 450 / 1920
  },
  2: {
    resolution: 2,
    label: '16:9',
    width: 800,
    height: 450,
    ratio: 450 / 1080
  }
}
interface CanvasJson {
  version: string
  objects: fabric.Object[]
}
interface CanvasState {
  id: number | undefined
  isChangeData: boolean
  name: string
  workspace: null | HTMLDivElement
  canvas: null | fabric.Canvas
  defaultSize: SizeObj
  music: Partial<MusicObj>
  canvasJson: CanvasJson
  activeObject: fabric.Object | null
  dub: Partial<DubObj>
  voiceContent: VoiceContent
  cover: string
}

export interface DubObj {
  Desc: string
  Name: string
  Remark: string
  Tag: string
  Voice: string
  VoiceType: string
  VoiceUrl: string
}

export interface VoiceContent {
  text: string
  type: 1 | 2
  voice_url: string
  voice_name: string
}
export interface SizeObj {
  resolution: number
  label: string
  width: number
  height: number
  ratio: number
}

export interface MusicObj {
  cover: string
  id: number
  name: string
  url: string
}
export enum ImageTypes {
  BACKGROUND = 'background',
  AVATAR = 'avatar',
  PROSPECT = 'prospect',
  MAPS = 'maps'
}

export enum TextTypes {
  CAPTIONS = 'captions',
  TEXT = 'text'
}

export const ObjectTypesToIndex = {
  background: 0,
  avatar: 1
}

export const useCanvasStore = defineStore({
  id: 'canvas',
  state: (): CanvasState => {
    return {
      id: undefined,
      isChangeData: false,
      name: '未命名脚本',
      workspace: null,
      // 画布实例
      canvas: null,
      defaultSize: canvasSizeData[1],
      music: {},
      dub: {},
      canvasJson: {
        version: '',
        objects: []
      },
      activeObject: null,
      voiceContent: {
        text: '',
        voice_url: '',
        type: 1,
        voice_name: ''
      },
      cover: ''
    }
  },
  getters: {},
  actions: {
    setActiveObjectByType(type: string) {
      if (this.activeObject?.customType === type) return
      const currentObject = this.canvas
        ?.getObjects()
        ?.find(({ customType }) => customType === type)
      if (currentObject) {
        this.canvas?.setActiveObject(currentObject)
        this.canvas?.renderAll()
      }
    },
    setZoom(scale: number) {
      if (!this.canvas) {
        return
      }
      const width = this.defaultSize.width * scale
      const height = this.defaultSize.height * scale

      this.canvas.setWidth(width)
      this.canvas.setHeight(height)
      this.canvas.setZoom(scale)
    },
    changeSize(size: SizeObj) {
      if (!this.canvas) {
        return
      }
      this.defaultSize = size
      if (this.workspace!.clientWidth < this.defaultSize.width) {
        this.workspace!.style.height = `${
          (this.workspace!.clientWidth * this.defaultSize.height) /
          this.defaultSize.width
        }px`
      } else {
        this.workspace!.style.height = `${this.defaultSize.height}px`
      }
      const scale = this.workspace!.clientHeight / this.defaultSize.height
      this.setZoom(scale)
    },
    initObject() {
      if (!this.canvas) {
        return
      }
      const objects = this.canvas.getObjects()
      const center = this.getZoomCenter()

      objects?.forEach((item) => {
        switch (item.customType) {
          case ImageTypes.BACKGROUND: {
            item.set({
              scaleX: this.getScaleX(item.width!),
              scaleY: this.getScaleY(item.height!),
              left: center.left,
              top: center.top
            })

            break
          }
          case TextTypes.TEXT: {
            item.set({
              left: center.left,
              top: center.top,
              //@ts-ignore
              fontSize: this.calcFontSize(item.data?.fontSize)
            })
            break
          }
          case TextTypes.CAPTIONS: {
            item.set({
              top: (center.top / 4) * 7,
              left: center.left,
              //@ts-ignore
              fontSize: this.calcFontSize(item.data?.fontSize)
            })

            break
          }
          default: {
            item.set({
              left: center.left,
              top: center.top
            })
          }
        }
      })
    },
    initControl() {
      this.deleteControl()
    },
    deleteControl() {
      const canvas = this.canvas
      if (!canvas) {
        throw new Error('请先初始化canvas对象')
      }
      const delImg = document.createElement('img')
      delImg.src = del_icon
      const size = 34
      function renderDelIcon(
        ctx: CanvasRenderingContext2D,
        left: number,
        top: number,
        styleOverride: any,
        fabricObject: fabric.Object
      ) {
        ctx.save()
        ctx.translate(left, top)
        ctx.rotate(fabric.util.degreesToRadians(fabricObject.angle!))
        ctx.drawImage(delImg, -size / 2, -size / 2, size, size)
        ctx.restore()
      }

      // 删除选中元素
      function deleteObject(mouseEvent: MouseEvent, target: fabric.Transform) {
        if (target.action === 'rotate') return true
        const activeObject = canvas?.getActiveObjects()
        if (activeObject) {
          activeObject.map((item) => canvas?.remove(item))
          canvas?.requestRenderAll()
          canvas?.discardActiveObject()
        }
        return true
      }

      // 删除图标
      fabric.Textbox.prototype.controls.del = new fabric.Control({
        x: 0.5,
        y: -0.5,
        offsetY: -size / 4,
        offsetX: size / 4,
        cursorStyle: 'pointer',
        mouseUpHandler: deleteObject,
        render: renderDelIcon
      })
      fabric.Object.prototype.controls.del = new fabric.Control({
        x: 0.5,
        y: -0.5,
        offsetY: size / 2,
        offsetX: -size / 2,
        cursorStyle: 'pointer',
        mouseUpHandler: deleteObject,
        render: renderDelIcon
      })
    },
    getScaleX(width: number) {
      return this.getWidth() / width
    },
    getScaleY(height: number) {
      return this.getHeight() / height
    },
    getCoverScale(width: number, height: number) {
      const scale =
        width / height < this.defaultSize.width / this.defaultSize.height
          ? this.getScaleX(width)
          : this.getScaleY(height)
      return scale
    },
    getContainScale(width: number, height: number) {
      const scale =
        width / height < this.defaultSize.width / this.defaultSize.height
          ? this.getScaleY(height)
          : this.getScaleX(width)
      return scale
    },
    initLoadFont() {
      const loadTask = this.canvasJson.objects
        ?.filter((item) => ['textbox'].includes(item.type!))
        .map((item) => {
          return new FontFaceObserver((item as any).fontFamily).load(
            null,
            100 * 1000
          )
        })
      return Promise.all(loadTask)
    },
    async initCanvas(id: string, workspace: HTMLDivElement) {
      const { tabsState } = useDesignTabs()
      this.workspace = workspace
      // 修改默认配置
      fabric.Object.prototype.set({
        borderColor: '#15E9FF',
        cornerColor: '#15E9FF',
        cornerStrokeColor: 'white',
        borderOpacityWhenMoving: 1,
        borderScaleFactor: 1,
        cornerSize: 10,
        cornerStyle: 'circle',
        rotatingPointOffset: 4,
        transparentCorners: false
      })

      await this.initData()
      const canvas = new fabric.Canvas(id, {
        width: this.defaultSize.width,
        height: this.defaultSize.height
      })
      console.log(canvas)
      // 注意此处的canvas不能被代理，不然会出现奇怪的bug
      this.canvas = markRaw(canvas)
      //指明对象被选定的时候，是否留在当前的栈位置上。如果设置为false，对象会被移至最顶端，并被渲染为选择组的一部分。
      this.canvas.preserveObjectStacking = true
      this.canvas.selection = false
      this.initControl()
      this.initEvent()
      await this.initLoadFont()
      await this.setCanvasJson(this.canvasJson)
      this.setActiveObjectByType(tabsState.value.current)

      const scale = workspace.clientHeight / this.defaultSize.height
      this.setZoom(scale)
      setTimeout(() => {
        this.isChangeData = false
      })
    },
    initEvent() {
      if (!this.canvas) {
        return
      }

      this.canvas.on('after:render', () => {
        const json = this.getCanvasJson()
        if (json) {
          this.canvasJson = json
        }
      })
      this.canvas.on('selection:created', () => {
        this.changeActiveObject()
      })
      this.canvas.on('selection:updated', () => {
        this.changeActiveObject()
      })
      this.canvas.on('selection:cleared', () => {
        this.changeActiveObject()
      })
    },
    changeActiveObject() {
      const { changeTabs } = useDesignTabs()

      const obj = this.canvas?.getActiveObject()
      if (obj) {
        this.activeObject = markRaw(obj)
        if (obj.customType) {
          changeTabs(obj.customType)
        }
      } else {
        this.activeObject = null
      }
    },
    setActiveObject(id: string) {
      if (!this.canvas) {
        throw new Error('请先初始化canvas对象')
      }
      const info = this.canvas.getObjects().find((item) => item.id === id)
      if (info) {
        this.canvas.setActiveObject(info)
        this.canvas.renderAll()
      }
    },
    delObject(id: string) {
      if (!this.canvas) {
        throw new Error('请先初始化canvas对象')
      }
      const info = this.canvas.getObjects().find((item) => item.id === id)
      if (info) {
        this.canvas.remove(info)
        this.canvas.renderAll()
      }
    },
    getCanvasJson() {
      if (this.canvas) {
        return this.canvas.toJSON([
          'hasControls',
          'hasBorders',
          'scaleX',
          'scaleY',
          'originX',
          'originY',
          'angle',
          'top',
          'left',
          'crossOrigin',
          'data',
          'selectable',
          'id',
          'customType',
          'lockScalingY',
          'lockScalingX',
          'lockUniScaling',
          'lockMovementX',
          'lockMovementY',
          'type',
          '_controlsVisibility',
          'name',
          'editable'
        ])
      }
    },
    setCanvasJson(json: any) {
      return new Promise<void>((resolve, reject) => {
        if (this.canvas) {
          this.canvas.loadFromJSON(json, () => {
            this.canvas?.renderAll()
            resolve()
          })
        } else {
          reject()
        }
      })
    },
    getZoomCenter() {
      if (!this.canvas) {
        throw new Error('请先初始化canvas对象')
      }
      const center = this.canvas.getCenter()
      for (const key in center) {
        center[key as keyof typeof center] /= this.canvas.getZoom()
      }
      return center
    },
    getTransformX() {
      if (!this.canvas) {
        throw new Error('请先初始化canvas对象')
      }
      return this.canvas!.viewportTransform![4] / this.canvas.getZoom()
    },
    getWidth() {
      if (!this.canvas) {
        throw new Error('请先初始化canvas对象')
      }
      return this.canvas.getWidth() / this.canvas.getZoom()
    },
    getHeight() {
      if (!this.canvas) {
        throw new Error('请先初始化canvas对象')
      }
      return this.canvas.getHeight() / this.canvas.getZoom()
    },
    getTransformY() {
      if (!this.canvas) {
        throw new Error('请先初始化canvas对象')
      }
      return this.canvas!.viewportTransform![5] / this.canvas.getZoom()
    },
    async addText(
      text: string,
      type: TextTypes = TextTypes.TEXT,
      data: Partial<fabric.ITextboxOptions> = {}
    ) {
      if (!this.canvas) {
        throw new Error('请先初始化canvas对象')
      }
      const center = this.getZoomCenter()

      const { fontFamily, fill, stroke, fontSize } = data
      if (fontFamily) {
        feedback.loading('正在加载字体中，请稍等...')
        try {
          await new FontFaceObserver(fontFamily).load(null, 100 * 1000)
        } catch (error) {
          console.log(error)
          feedback.msgError('字体加载失败，请重试')
        } finally {
          feedback.closeLoading()
        }
      }

      const textOptions = {
        name: '文本',
        id: uniqueId(),
        customType: type,
        left: center.left,
        top: center.top,
        fontSize: this.calcFontSize(fontSize!),
        selectable: true,
        hasControls: true,
        hasBorders: true,
        fontWeight: 400,
        fontFamily: fontFamily,
        fill: fill,
        stroke: stroke,
        strokeWidth: 1,
        data: data,
        lockScalingY: false,
        lockScalingX: false,
        lockUniScaling: false,
        textBackgroundColor: '',
        textAlign: 'left',
        originX: 'center',
        originY: 'center',
        centeredScaling: true
      } as fabric.ITextboxOptions
      const textBox = new fabric.Textbox(text, textOptions)
      textBox.setControlsVisibility({
        bl: false,
        br: false,
        mb: false,
        ml: true,
        mr: true,
        mt: false,
        tl: false,
        tr: false,
        mtr: false
      })

      switch (type) {
        case TextTypes.CAPTIONS: {
          const objects = this.canvas.getObjects()
          const currentObject = objects.find(
            (item) => item.customType === TextTypes.CAPTIONS
          )
          this.canvas.remove(currentObject!)
          textBox.set({
            name: '字幕',
            top: (center.top / 4) * 7,
            editable: false,
            lockMovementX: true
          })
          break
        }
      }
      this.canvas.add(textBox)
      this.canvas?.setActiveObject(textBox)
    },

    addImage(url: string, type: ImageTypes, data: Record<string, any> = {}) {
      if (!this.canvas) {
        throw new Error('请先初始化canvas对象')
      }
      const imgOptions = {
        id: uniqueId(),
        customType: type,
        data,
        left: this.getWidth() / 2 - this.getTransformX(),
        top: this.getHeight() / 2 - this.getTransformY(),
        originX: 'center',
        originY: 'center',
        centeredScaling: true,
        crossOrigin: ''
      } as Partial<fabric.IImageOptions>

      fabric.Image.fromURL(
        url,
        (image) => {
          switch (type) {
            case ImageTypes.BACKGROUND: {
              image.set({
                name: '背景',
                scaleX: this.getScaleX(image.width!),
                scaleY: this.getScaleY(image.height!),
                lockMovementX: true,
                lockMovementY: true,
                lockScalingX: true,
                lockScalingY: true,
                hasBorders: false
              })
              image.setControlsVisibility({
                bl: false,
                br: false,
                mb: false,
                ml: false,
                mr: false,
                mt: false,
                tl: false,
                tr: false,
                mtr: false
              })
              break
            }
            case ImageTypes.AVATAR: {
              const scale =
                this.getContainScale(image.width!, image.height!) - 0.02
              image.set({
                name: '形象',
                scaleX: scale,
                scaleY: scale,
                hasBorders: true
              })
              image.setControlsVisibility({
                mb: false,
                ml: false,
                mr: false,
                mt: false,
                mtr: false
              })
              break
            }
            case ImageTypes.MAPS: {
              let scale = this.getContainScale(image.width!, image.height!) / 3
              if (scale > 1) {
                // 保持图片失真
                scale = 1
              }
              image.set({
                name: '贴图',
                scaleX: scale,
                scaleY: scale
              })
              image.setControlsVisibility({
                mb: false,
                ml: false,
                mr: false,
                mt: false,
                mtr: false
              })
              break
            }
            case ImageTypes.PROSPECT: {
              let scale = this.getContainScale(image.width!, image.height!) / 2
              if (scale > 1) {
                // 保持图片失真
                scale = 1
              }
              image.set({
                name: '前景',
                scaleX: scale,
                scaleY: scale
              })
              image.setControlsVisibility({
                mb: false,
                ml: false,
                mr: false,
                mt: false,
                mtr: false
              })
            }
          }

          if (type === ImageTypes.BACKGROUND || type === ImageTypes.AVATAR) {
            // 插入的规则：背景图必须在最底层，形象在第二层，如果没有背景，形象在最底层
            const objects = this.canvas?.getObjects()
            const hasBg = objects?.[0]?.customType === ImageTypes.BACKGROUND
            const currentObjects = objects?.find(
              (item) => item.customType === type
            )
            if (currentObjects) {
              this.canvas?.remove(currentObjects)
            }
            this.canvas?.add(image)
            if (type === ImageTypes.BACKGROUND || !hasBg) {
              this.canvas?.sendToBack(image)
            } else {
              this.canvas?.moveTo(image, 1)
            }
          } else {
            this.canvas?.add(image)
          }
          this.canvas?.setActiveObject(image)
        },
        imgOptions
      )
    },
    calcFontSize(size: number) {
      return this.defaultSize.ratio * size
    },
    transformData() {
      const data: Record<string, any> = {
        id: this.id,
        name: this.name,
        resolution: this.defaultSize.resolution,
        canvas: toRaw(this.canvasJson),
        music: toRaw(this.music),
        dub: toRaw(this.dub),
        voice_content: toRaw(this.voiceContent),
        cover_picture_url: this.cover,
        texts: [],
        maps: [],
        prospect: [],
        captions: {
          status: 0
        },
        background: {},
        avatar: {}
      }
      this.canvasJson.objects.forEach((item, index) => {
        const rWidth = item.width! * item.scaleX!
        const rHeight = item.height! * item.scaleY!
        const width = rWidth / this.getWidth()
        const height = rHeight / this.getHeight()
        const X = (item.left! - rWidth / 2) / this.getWidth()
        const Y = (item.top! - rHeight / 2) / this.getHeight()
        switch (item.customType) {
          case 'avatar': {
            data.avatar = {
              AvatarId: item.data?.avatar_id,
              Height: height,
              Width: width,
              X,
              Y,
              zIndex: index
            }
            break
          }
          case 'background': {
            data.background = {
              ...toRaw(item.data),
              zIndex: index
            }

            break
          }
          case 'captions': {
            data.captions = {
              status: item.data?.status,
              Font: item.data?.fontFamily,
              FontColor: item.data?.fill,
              FontSize: item.data?.fontSize,
              Outline: 1,
              OutlineColour: item.data?.stroke,
              X: item.left! / this.getWidth(),
              Y: item.top! / this.getHeight(),
              Alignment: 'Center',
              zIndex: index
            }
            break
          }
          case 'text': {
            const { server_key = '', type = '' } = item.data?.effect || {}
            data.texts.push({
              AaiMotionInEffect: type == 'in' ? server_key : '',
              AaiMotionLoopEffect: type == 'loop' ? server_key : '',
              AaiMotionOutEffect: type == 'out' ? server_key : '',
              Font: item.data?.fontFamily,
              FontColor: item.data?.fill,
              FontSize: item.data?.fontSize,
              Outline: 1,
              OutlineColour: item.data?.stroke,
              X: X,
              Y: Y,
              Height: height,
              //@ts-ignore
              Content: item.text,
              zIndex: index
            })
            break
          }

          case 'maps':
          case 'prospect': {
            data[item.customType]?.push({
              X: X,
              Y: Y,
              Height: height,
              Width: width,
              ...item.data,
              zIndex: index
            })
          }
        }
      })
      return data
    },
    async savaOrComposite(action = 1) {
      const data = this.transformData()
      const res = await putAvatarSava({
        ...data,
        action
      })
      this.isChangeData = false
      this.id = res.id
    },
    async putImgCover() {
      if (!this.canvas) {
        throw new Error('请先初始化canvas对象')
      }
      const url = this.canvas.toDataURL({
        format: 'png',
        top: 0,
        left: 0,
        width: this.canvas.width,
        height: this.canvas.height
      })
      const file = dataURLtoFile(url, 'cover.png')
      const res = await uploadImage({
        file
      })
      this.cover = res.uri
    },
    async initData() {
      const route = useRoute()

      const id = route.query.id
      if (!id) return
      try {
        const res = await getAvatarDetail({
          id: Number(id)
        })
        const {
          music = {},
          dub = {},
          canvas = {},
          voice_content = this.voiceContent,
          resolution = 3
        } = JSON.parse(res.params)
        this.id = res.id
        this.music = music
        this.dub = dub
        this.name = res.name
        this.voiceContent = voice_content
        this.defaultSize = (canvasSizeData as any)[resolution] || {}
        this.canvasJson = canvas
      } catch (error) {}
    }
  }
})
