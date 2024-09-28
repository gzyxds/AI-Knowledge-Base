import mosaic from '@/assets/image/draw/mosaic.png';

export class DrawingTool {
    private ctx: CanvasRenderingContext2D | null = null;
    private canvas: HTMLCanvasElement | null = null;
    private stepList: ImageData[] = [];
    private stage: any = null;
    private mosaicImage: HTMLImageElement;

    constructor() {
        this.mosaicImage = new Image();
        this.mosaicImage.src = mosaic;
    }

    init(stage: any) {
        this.ctx = stage.ctx;
        this.canvas = stage.canvas;
        this.stepList = stage.stepList;
        this.stage = stage;
    }

    saveCurrentState() {
        if (this.stage.stepIndex < this.stepList.length - 1) {
            this.stepList.splice(this.stage.stepIndex + 1);
        }

        this.stepList.push(
          this.ctx?.getImageData(0, 0, this.canvas!.width, this.canvas!.height)!
    );
        this.stage.stepIndex++;
    }

    reapplyState() {
        if (this.stage.stepIndex < 0) return;
        this.ctx?.clearRect(0, 0, this.canvas!.width, this.canvas!.height);
        this.ctx?.putImageData(this.stepList[this.stage.stepIndex], 0, 0);
    }

    drawLasso(points: number[], stroke: string, strokeWidth = 1.5, close = false) {
        this.reapplyState();
        const len = points.length;

        this.ctx?.beginPath();
        this.ctx?.moveTo(points[0], points[1]);

        for (let i = 2; i < len - 3; i += 4) {
            this.ctx?.bezierCurveTo(
              points[i], points[i + 1], points[i + 2], points[i + 3], points[i + 4], points[i + 5]
            );
        }

        if (close) {
            this.ctx?.closePath();
        }

        this.ctx!.strokeStyle = stroke;
        this.ctx!.lineWidth = strokeWidth;
        this.ctx?.stroke();

        this.ctx!.fillStyle = this.ctx?.createPattern(this.mosaicImage, 'repeat')!;
        this.ctx?.fill();
    }

    drawRect({ x, y, w, h, color }: { x: number; y: number; w: number; h: number; color: string }) {
        this.reapplyState();
        this.ctx!.strokeStyle = color;
        this.ctx!.lineWidth = 2
        this.ctx!.rect(x, y, w, h);
        this.ctx!.stroke();
        this.ctx!.beginPath()

        this.ctx!.fillStyle = this.ctx?.createPattern(this.mosaicImage, 'repeat')!;
        this.ctx?.fillRect(x, y, w, h);
    }
}
