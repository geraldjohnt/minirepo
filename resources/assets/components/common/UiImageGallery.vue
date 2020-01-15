<template> 
    <div class="ui-image-gallery inner-content" id="close-btn" v-if="imageList.length">
         <!-- MINIMIZE DOCUMENT AS A DEFAULT -->
        <!-- <div v-if="documentMinViewDisplay"> -->

            <!-- new added html for document v-if="documentMinViewDisplay === true" -->
            <div v-el:document-layout class="docuLayout docBox" v-draggable v-onclickfront >
                <div class="DocumentWrapper">
                    <div class="ui-close-button"  @click="closeDoc">
                        <!-- <button class="ui-fab ui-fab-mini color-primary" @click="toggleSalesShare"> -->
                        <button class="ui-fab ui-fab-mini color-primary">
                            <i class="ui-icon material-icons ui-fab-icon close">close</i>
                            <div class="ui-ripple-ink"></div>
                        </button>
                    </div>
                    <div class="document-content" >
                        <div class="inner-document" >
                            <div class="imgMini" id="boxImage" v-el:minbox-image >
                                <section id="canvasCont" class="canvasContainer" style="position: relative;" >
                                  <!--   <canvas v-bind:style.sync="minCanvasStyle" v-el:minboard-background ></canvas>
                                    <canvas v-bind:style.sync="minCanvasStyle" v-el:minboard-drawing ></canvas> -->
                                </section>
                                <img v-el:minboard-image :src="currentImage.path"  class="imgResponsive minimize"  v-bind:style="{ width: '100% ' }" id="docPage" v-on:mousemove="docCoord" v-on:mouseleave="docLeave"\>
                            </div>
                             <div v-if="!!clients">
                                <div v-for="client in clients">
                                    <!-- <span class="pointerDoc" v-bind:style.sync="(client.clientpointer.doctouch) ? client.pointerDoc : {left:'-50px', top:'-50px'} " id="pointerDoc{{client.id}}" elem="{{client.id}}" v-el:pointerDoc{{client.id}} style="z-index:1000;"> -->
                                    <span class="pointerDoc" v-bind:style.sync="(client.clientpointer.doctouch) ?  client.pointerDoc : {display:'none'} " id="pointerDoc{{client.id}}" elem="{{client.id}}" v-el:pointerDoc{{client.id}} style="z-index:1000;">
                                        <ui-icon icon="pan_tool"></ui-icon>
                                    </span>
                                </div>  
                            </div>
                            <div v-else>
                                <span class="pointerDoc" v-bind:style.sync="(clientPointer.doctouch) ?  clientPointer : {display:'none'}" v-el:pointerDoc style="z-index:100;">
                                    <ui-icon icon="pan_tool"></ui-icon>
                                </span>
                            </div>
                        </div> 
                        <div class="document-footer">
                            <span class="docu-text" v-if="mobileViewDisplay == docFromClient" v-onclickfront v-el:doc-text> 
                                <ui-button
                                    type="flat" color="secondary" icon="assignment" tooltip="資料メモを表示" @click="toggleAnnotation"
                                    tooltip-position="bottom center"
                            >資料メモを表示 
                                </ui-button>
                            </span>
                            
                            <span class="docu-download" v-if="mobileViewDisplay == docFromClient && document.allow_download == 1"> 
                                <ui-button
                                    type="flat" color="secondary" icon="file_download" tooltip="資料を渡す" @click="toggleDocShare"
                                    tooltip-position="bottom center"
                            >資料を渡す 
                                </ui-button>
                            </span>
                            <span class="docu-pagenate"> 
                                <a href="#">{{ ctrImage + 1 }}</a>/<a href="#">{{ imageList.length }}</a>
                            </span>
                            <span class="docu-arrow_left" @click="prevPage()" v-if="ctrImage > 0"> 
                                <button class="btn-al">
                                    <i class="ui-icon material-icons ui-button-icon save"></i>
                                </button>
                            </span>
                            <span class="docu-arrow_right" @click="nextPage()" v-if="imageList.length-1 > ctrImage"> 
                                <button class="btn-ar">
                                    <i class="ui-icon material-icons ui-button-icon save"></i>
                                </button>
                            </span>
                            <span class="docu-zoom" @click="zoom"> 
                                <button class="btn-zoom">
                                    <i class="ui-icon material-icons ui-button-icon save"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ANNOTATION -->
            <!-- <div v-draggable v-if="show.annotation == minimizeDefaultView" v-onclickfront class="annotation annotationBox onTop" v-el:file-annotation>
                <div class="ui-close-button">
                    <button class="ui-fab ui-fab-mini color-primary" @click="toggleAnno">
                        <i class="ui-icon material-icons ui-fab-icon close">close</i>
                        <div class="ui-ripple-ink"></div>
                    </button>
                </div>
                <h3 class="title">
                    資料メモ  
                </h3>
                <span class="docu_text-pagenate"> 
                    <a href="#">{{ ctrImage + 1 }}</a>/<a href="#">{{ imageList.length }}</a>
                </span>
                <pre>{{ annotation }}</pre>
            </div>  -->
            <!-- END ANNOTATION --> 
           <!-- END OF MINIMIZED DOC --> 

    <!-- Commented @click="minimizeDoc"  v-if="documentMinViewDisplay === false" -->
        <div v-el:maximize-board class="gallery-con col-md-12" >  
            <div class="image-item custom-container" style="text-align: center;">
                <div class="box maxDocumentPage" :pointerfromParentTop.sync="1" id="boxImage" v-onclickfront v-el:box-image>
                    <!-- Close Button -->
                    <div class="fill" style="height:inherit; padding-bottom: 50px">
                        <div class="ui-close-button btn-close-doc" style="z-index: 52 !important" v-on:click="closeDoc">
                            <button class="ui-fab ui-fab-mini color-primary">
                                <i class="ui-icon material-icons ui-fab-icon close">close</i>
                                <div class="ui-ripple-ink"></div>
                            </button>
                        </div>
                   <!-- <section id="canvasCont" class="canvasContainer" style="position: relative;" >
                        <canvas v-bind:style.sync="canvasStyle" v-el:board-background ></canvas>
                        <canvas v-bind:style.sync="canvasStyle" v-el:board-drawing></canvas>
                        <canvas v-el:tempo-canvas></canvas>
                    </section> -->
                        <img v-el:image-board :src="currentImage.path"  class="imgResponsive maximize"  v-bind:style="{ width:'auto ' }" id="docPage" v-on:mousemove="docCoord" v-on:mouseleave="docLeave"\>
                        <div v-if="!!clients">
                            <div v-for="client in clients">
                                <!-- <span class="pointerDoc" v-bind:style.sync="(client.clientpointer.doctouch) ?  client.pointerDoc : {left:'-50px', top:'-50px'} " id="pointerDoc{{client.id}}" elem="{{client.id}}" v-el:pointerDoc{{client.id}} style="z-index:1000;"> -->
                                <span class="pointerDoc" v-bind:style.sync="(client.clientpointer.doctouch) ?  client.pointerDoc : {display:'none'} " id="pointerDoc{{client.id}}" elem="{{client.id}}" v-el:pointerDoc{{client.id}} style="z-index:1000;">
                                    <ui-icon icon="pan_tool"></ui-icon>
                                </span>
                            </div>  
                        </div>
                        <div v-else>
                            <span class="pointerDoc clientPointerDoc" v-bind:style.sync="(clientPointer.doctouch) ?  clientPointer : {display:'none'}" v-el:pointerDoc style="z-index:100;">
                                <ui-icon icon="pan_tool"></ui-icon>
                            </span>
                        </div>
                        <!-- End of Client Pointers -->
                    </div>
               <!--     <div class="canvas-toolbar" v-el:canvas-toolbar>
                        <span class="toolbar-item" @click="toggleMarker" v-el:marker-tool>
                            <i class="material-icons">brush</i>
                        </span>
                        <span class="toolbar-item" v-el:color-tool @click="toggleColor">
                            <i class="material-icons">color_lens</i>
                        </span>
                        <ui-popover :trigger="$els.colorTool" class="color-container" v-el:color-popover dropdown-position="bottom center"> 
                            <div class="color-wheel">
                                <div class="color-item" style="background-color: rgb(213,0,0)" @click="paintColor(0, $event)"></div>
                                <div class="color-item" style="background-color: rgb(255,109,0)" @click="paintColor(1, $event)"></div>
                                <div class="color-item" style="background-color: rgb(255,214,0)" @click="paintColor(2, $event)"></div>
                                <div class="color-item" style="background-color: rgb(0,200,83)" @click="paintColor(3, $event)"></div>
                            </div>
                            <div class="color-wheel">
                                <div class="color-item" style="background-color: rgb(41,98,255)" @click="paintColor(4, $event)"></div>
                                <div class="color-item" style="background-color: rgb(48,79,254)" @click="paintColor(5, $event)"></div>
                                <div class="color-item" style="background-color: rgb(197,17,98)" @click="paintColor(6, $event)"></div>
                                <div class="color-item color-active" style="background-color: rgb(33,33,33)" @click="paintColor(7, $event)"></div>
                            </div>
                        </ui-popover>
                        <span class="toolbar-item" v-el:paint-size @click="togglePaintSize">
                            <i class="material-icons">notes</i>
                        </span>
                        <ui-popover :trigger="$els.paintSize" class="paintsize-container" v-el:color-popover dropdown-position="bottom center">
                             <ui-slider :value.sync="lineWidth" showMarker="true" step="0.1"></ui-slider>
                        </ui-popover>
                         <span class="toolbar-item" @click="toggleEraser" v-el:eraser-tool>
                            <i class="material-icons">crop_portrait</i>
                        </span>
                        <span class="toolbar-item" @click="toggleDownload" v-if="minimizeDefaultView">
                            <i class="material-icons">save_alt</i>
                        </span>
                    </div>   -->
                    
                    <!-- Dakooo Version -->
                        <div class="footer-doc-toolbar" style="">
                        <div class="footer-iconleft" v-if="minimizeDefaultView == docFromClient">
                            <span class="footer-doc-text"> 
                                <ui-button
                                    type="flat" color="secondary" icon="assignment" tooltip="資料メモを表示" @click="toggleAnnotation"
                                    tooltip-position="bottom center"
                            >資料メモを表示 
                                </ui-button>
                            </span>
                            
                            <span class="footer-doc-download" v-if="document.allow_download == 1"> 
                                <ui-button
                                    type="flat" color="secondary" icon="file_download" tooltip="資料を渡す" @click="toggleDocShare"
                                    tooltip-position="bottom center"
                            >資料を渡す 
                                </ui-button>
                            </span>
                        </div> 
                              
                        <span class="footer-doc-pagenate"> 
                            <a href="#">{{ ctrImage + 1 }}</a>/<a href="#">{{ imageList.length }}</a>
                        </span>
                        
                        <div class="footer-iconright">
                            <span class="footer-doc-arrow_left" @click="prevPage()" v-if="ctrImage > 0"> 
                                <button class="btn-al">
                                    <i class="ui-icon material-icons ui-button-icon save"></i>
                                </button>
                            </span>
                            <span class="footer-doc-arrow_right" @click="nextPage()" v-if="imageList.length-1 > ctrImage"> 
                                <button class="btn-ar">
                                    <i class="ui-icon material-icons ui-button-icon save"></i>
                                </button>
                            </span>
                            <span class="footer-doc-zoom" v-if="minimizeDefaultView" @click="zoom"> 
                                <button class="btn-zoom">
                                    <i class="ui-icon material-icons ui-button-icon save"></i>
                                </button>
                            </span>
                        </div>                             
                    </div>
                   <!--  <img v-el:image-board :src="currentImage.path" v-onclickfront class="imgResponsive"  v-bind:style="{ width:'auto !important' }"
                    id="docPage" v-on:mousemove="docCoord" v-on:mouseleave="docLeave"\> -->

                   
                </div> 
                
                <div class="image-details" v-if="currentImage && withDetails">
                    <span class="title">Title: {{ currentImage.title }}</span>
                    <span class="desc">Description: {{ currentImage.description }}</span>
                </div>
            </div>
        </div>

        <!-- ANNOTATION -->
        <div v-draggable v-if="minimizeDefaultView == docFromClient" v-bind:class="[annotationToggle ? '' : 'hideAnnotation' ]" v-onclickfront class="annotation annotationBox onTop" v-el:file-annotation>
            <div class="ui-close-button">
                <button class="ui-fab ui-fab-mini color-primary" @click="toggleAnnotation">
                    <i class="ui-icon material-icons ui-fab-icon close">close</i>
                    <div class="ui-ripple-ink"></div>
                </button>
            </div>
            <h3 class="title">
                資料メモ  
            </h3>
            <span class="docu_text-pagenate"> 
                <a href="#">{{ ctrImage + 1 }}</a>/<a href="#">{{ imageList.length }}</a>
            </span>
            <textarea class="ui-annotation-textarea" readonly>{{ annotation }}</textarea>
        </div>
        <!-- END ANNOTATION --> 
        
        
        <!-- <div class="gallery-controls">
            <span v-onclickfront class="lb-btn prevPage" @click="prevPage()" v-if="ctrImage > 0"><ui-icon icon="keyboard_arrow_left"></span>
            <span v-onclickfront class="lb-btn nextPage" @click="nextPage()" v-if="imageList.length-1 > ctrImage"><ui-icon icon="keyboard_arrow_right"></span>
        </ui-icon> -->
        
    </div>
</template> 
<script>
import { UiPopover  } from 'keen-ui';
import {APP_URL} from '../../js/config.js';
import browserDetect from '../../js/browser_detect.js';
import OnclickToFrontDirective from '../../directives/OnclickToFrontDirective.js';
import DraggableDirective from '../../directives/DraggableDirective.js';
import Cookie from 'js-cookie';
export default {
    name: 'ui-image-gallery',
    data() {
        return {
            eventPrefix: 'ui-image-gallery:',
            static_images: {
                avatar: `${APP_URL}/image/avatar_flat.png`
            },
            currentImage: {},
        ctrImage: 0,
        docCoordinates: {
            color: '',
            size: 0,
            isMobile: false,
            isReleased: false,
            isDragging: false,
            paint: false,
            eraser: false,
            top: 0,
            left: 0,
            doctouch: false,
            onScreenHeight: 0,
            onScreenWidth: 0,
            percentageHeight: 0,
            percentageWidth: 0,
        },
        clientPainter: {
            clickX: [],
            clickY: [],
            clickDrag: []
        },
        pointerfromParentLeft: 0,
        pointerfromParentTop: 0,
        boxImageCoords: {
            width: 0,
            height: 0
        },
        canvasBoard: '',
        canvasBackground: '',
        canvasStyle: {
            zIndex:'-1',
            position: 'absolute',
            height: 0,
            width: 0
        },
        minCanvasBoard: '',
        minCanvasBackground: '',
        minCanvasStyle: {
            zIndex:'-1',
            position: 'absolute',
            height: 0,
            width: 0
        },
        clickX: [],
        clickY: [],
        clickDrag: [],
        minClickX: [],
        minClickY: [],
        minClickDrag: [],
        paint: false,
        strokeStyle: "rgb(33,33,33)",
        lineJoin: "round",
        lineWidth:3,
        eraser: false,
        thirdBoard: '',
        isDragging: false,
        tempCanvas: '',
        drawBoardTemp: '',
        mindrawBoardTemp: '',
        canvasDrawingsPerPage: [],
        currPage: 1,
        widthInterval: '',
        isResized: false,
        isLoaded: false,
        isReleased: false,
        isPaintToggled: false,
        isPaintTool: false,
        isEraserTool: false,
        isColorTool: false,
        colorWheel: [
            'rgb(213,0,0)','rgb(255,109,0)',
            'rgb(255,214,0)','rgb(0,200,83)',
            'rgb(41,98,255)','rgb(48,79,254)',
            'rgb(197,17,98)','rgb(33,33,33)'
        ],
        currentColor: '',
        show: {
            annotation: true,
            Mindocument: true
        },
        currPage: 1,
        canvasIsSet: false,
        checkSizes:'',
        documentMinViewDisplay: true,
        mobileViewDisplay: true,
        annotationToggle: true,
        boxImageSizeNotLeft: false,
        minimizeOrMaximize: true,
        initialLoad: false,
        parsedDateTime: '',
        boxEls: [],
        }
    },
    props: {
        imageList: {
            type: Array,
            default: function() {
                return []
            }
        },
        withDetails: {
            type: Boolean,
            default: false
        },
        clientPointer: {
            isDragging: false,
            paint: false,
            eraser: false,
            isReleased: false,
            top: 0,
            left: 0,
            opacity: 1,
            position: 'absolute',   
        },
        multiPointer: {},
        clients: [],
        annotation: '',
        document: {},
        isMaximized: false,
        hideFullscreen: {
            zIndex: '-2'
        },
        minimizeDefaultView: true,
        docFromClient: true,
        boxElement: [],
        isStaff: false,
    },
    watch: {
        imageList: function (value) {
            // console.log('watch imagelist')
            document.querySelector('.gallery-con .box').style.maxWidth = 'none';
            this.$dispatch(this.eventPrefix+'loaded', this.currentImage.id)
            this.ctrImage = 0;
            this.currPage = 1;
            this.documentMinViewDisplay = true
            this.updateCurrentImage()
            // this.updateCanvas()
            // this.addCanvasListeners()
            this.getCoords()
            this.mobileAdjustment();
            // if (this.initialLoad) { 
            //     this.dispatchEvent('', this.mouseEvent("move", 1, 50, 1, 50))
            //     this.callDocument()
            // } 
            let btn = (document.getElementById("close-btn")) ? document.getElementById("close-btn").style.display = "block": '';
        },
        boxElement: function(value){
            this.boxEls = value;
            console.log("FROM STAFF BOX")

        },
        boxEls: function(value){
            this.$dispatch(this.eventPrefix + 'BoxElement', value);
        },
    },
    directives: {
        draggable: DraggableDirective,
        onclickfront: OnclickToFrontDirective
    },
    components: {
        UiPopover
    },
    methods: {
        checkBrowserUsage() {
            let browser = browserDetect.specs().browser;
            if (browser.name === 'Firefox') {
                if (document.querySelector('.gallery-con .box')) {
                    console.log(this.$els.imageBoard.clientWidth+' ------------>')
                    if (!!this.$els.imageBoard.clientWidth) {
                        document.querySelector('.gallery-con .box').style.maxWidth = this.$els.imageBoard.clientWidth+'px';
                        this.callDocument();   
                        if (!this.minimizeDefaultView){
                            this.showFullscreenDoc();
                        }
                        clearInterval(this.checkSizes);
                    }
                }
            }else{
                if (!this.minimizeDefaultView){
                    this.showFullscreenDoc();
                }
                clearInterval(this.checkSizes);
            }
        },
        setOnTop(){
            if (document.querySelector('.annotationBox') && this.annotationToggle){
                document.querySelector('.annotationBox').classList.add('onTop');
            }
        },
        toggleAnnotation(){
            
            this.annotationToggle = (!this.annotationToggle) ? true : false;
            
            if (this.annotationToggle) {
                document.querySelector('.annotationBox').classList.add('onTop');
                // console.log(this);
            } else {
                document.querySelector('.annotationBox').classList.remove('onTop');
            }
            let documentImage = document.getElementById("docPage")
            if(documentImage.classList[1] == "onTop"){
                documentImage.classList.remove("onTop");
            }
        },
        showToolbar(){
            // console.log('showToolbar')
            this.$els.canvasToolbar.style.zIndex = "1"
        },
        hideToolbar(){
            // console.log('hideToolbar')
            this.$els.canvasToolbar.style.zIndex = "-2"
        },
        showMinDoc(){
            // console.log('showMinDoc')
            this.$els.documentLayout.style.zIndex = "40"
            document.querySelector('.docuLayout').classList.add('onTop');
            document.querySelector('.docuLayout').classList.remove('hideDocument');
            this.callDocument()
            // document.querySelector('.ui-image-gallery .minimize').classList.add('onTop');
        },
        hideMinDoc(){
            // document.querySelector('.ui-image-gallery .docuLayout').classList.remove('onTop');
             // alert("CHANGE MIN")
            document.querySelector('.docuLayout').classList.remove('onTop');
            document.querySelector('.docuLayout').classList.add('hideDocument');
            this.$els.documentLayout.style.zIndex = "-2 !important";
            // document.querySelector('.docuLayout').classList.add('hide');
        },
        hideFullscreenDoc(){
            // console.log('hideFullscreenDoc')
            // document.querySelector('.ui-image-gallery .gallery-con .image-item .box .fill .imgResponsive').classList.remove('onTop');
            // document.querySelector('.ui-image-gallery .gallery-con .box').classList.remove('onTop');
            // alert("CHANGE")
            document.querySelector('.gallery-con .box').classList.remove('onTop');
            document.querySelector('.gallery-con .box').classList.add('hideDocument');
            this.$els.boxImage.style.zIndex = "-2 !important";
        },
        showFullscreenDoc(){
            // console.log('showFullscreenDoc')
            document.querySelector('.gallery-con .box').classList.add('onTop');
            document.querySelector('.gallery-con .box').classList.remove('hideDocument');
            this.$els.boxImage.style.zIndex = "40"
            this.callDocument()
        },
        touch(){
            // document.querySelector('.ui-image-gallery .docuLayout').style.zIndex = '49'
        },
        togglePaintSize(){
            let colors = document.querySelector('.color-item')
            let vm = this
            for (var i = 0; i < colors.length; i++) {
              colors[i].addEventListener("click", function() {
                vm.currentColor = document.querySelector(".color-active");
                vm.currentColor[0].className = vm.currentColor[0].className.replace(" color-active", "");
                this.className += " color-active";
              });
            }
        },
        paintColor(index, event){
            
            // Remove previous active color
            let prevcolor =  document.getElementsByClassName("color-active")
            if(prevcolor.length){
                prevcolor[0].classList.remove("color-active")
            }

            // Adding new color
            let selectedcolor = event.target
            selectedcolor.classList.add("color-active")
            
            this.strokeStyle = this.colorWheel[index]
            this.$els.colorTool.style.background = '#eee'
            this.isColorTool = false
            this.$els.boxImage.click()
        },
        newClientPainting(element){
            // console.log(element)
            // console.log('clickX: ' + element.clickX)
            // console.log('clickY: ' + element.clickY)
            // console.log('clickDrag: ' + element.clickDrag)
              this.canvasBoard.strokeStyle = element.color;
              this.canvasBoard.lineJoin = this.lineJoin;
              this.canvasBoard.lineWidth = element.size;
              
              for (let i = 0; i < element.clickX.length; i += 1) {
                             
                // Set the drawing path
                this.canvasBoard.beginPath();
                
                // If dragging then draw a line between the two points
                if (element.clickDrag[i] && i) {
                    this.canvasBoard.moveTo(element.clickX[i - 1], element.clickY[i - 1]);
                } else {
                    // The x position is moved over one pixel so a circle even if not dragging
                    this.canvasBoard.moveTo(element.clickX[i] - 1 , element.clickY[i]);
                }
                    this.canvasBoard.lineTo(element.clickX[i], element.clickY[i]);
                
                // Set the drawing color
                if (element.isErasing) {
                    // console.log('erasing: ' + element.isErasing)
                    this.canvasBoard.globalCompositeOperation = "destination-out"; // To erase instead of draw over with white
                } else {
                    this.canvasBoard.globalCompositeOperation = "source-over";  
                }

                this.canvasBoard.lineCap = "round";
                this.canvasBoard.lineJoin = "round";
                this.canvasBoard.lineWidth =  element.size;
                this.canvasBoard.stroke();

            }
                this.canvasBoard.closePath();
                this.canvasBoard.restore();
                this.canvasBoard.globalAlpha = 1;

        },
        minDocDraw(){
            // console.log('minDocDraw')
            // console.log(element)
            // console.log('clickX: ' + element.clickX)
            // console.log('clickY: ' + element.clickY)
            // console.log('clickDrag: ' + element.clickDrag)
              this.minCanvasBoard.strokeStyle = this.strokeStyle;
              this.minCanvasBoard.lineJoin = this.lineJoin;
              this.minCanvasBoard.lineWidth = this.lineWidth;
              
              for (let i = 0; i < this.minClickX.length; i += 1) {
                             
                // Set the drawing path
                this.minCanvasBoard.beginPath();
                
                // If dragging then draw a line between the two points
                if (this.minClickDrag[i] && i) {
                    this.minCanvasBoard.moveTo(this.minClickX[i - 1], this.minClickY[i - 1]);
                } else {
                    // The x position is moved over one pixel so a circle even if not dragging
                    this.minCanvasBoard.moveTo(this.minClickX[i] - 1 , this.minClickY[i]);
                }
                    this.minCanvasBoard.lineTo(this.minClickX[i], this.minClickY[i]);
                
                // Set the drawing color
                if (this.eraser) {
                    // console.log('erasing: ' + this.eraser)
                    this.minCanvasBoard.globalCompositeOperation = "destination-out"; // To erase instead of draw over with white
                } else {
                    this.minCanvasBoard.globalCompositeOperation = "source-over";  
                }

                this.minCanvasBoard.lineCap = "round";
                this.minCanvasBoard.lineJoin = "round";
                this.minCanvasBoard.lineWidth =  this.lineWidth;
                this.minCanvasBoard.stroke();

            }
                this.minCanvasBoard.closePath();
                this.minCanvasBoard.restore();
                this.minCanvasBoard.globalAlpha = 1;

        },
        recoverPageDrawing(){
            // console.log('recoverPageDraw Function')
            let currPage = this.ctrImage + 1
            // console.log('recovering Current page: ' + currPage)
            let recoverdraw = this.canvasDrawingsPerPage.filter((item) => item.page == currPage)
            // console.log('recoverdraw')
            // console.log(recoverdraw)
            if(recoverdraw.length != 0){
                // console.log('recovering Current page: ' + currPage)
                this.canvasBoard.drawImage(recoverdraw[0].drawing,0,0,this.canvasBoard.canvas.width,this.canvasBoard.canvas.height)
            }
        },
        saveCurrentDrawing(){
            // console.log('saveCurrentDrawing Function')
            let drawboardImg = this.canvasBoard.canvas.toDataURL("image/png");
            let drawingTemp = new Image()
            drawingTemp.src = drawboardImg;

            this.tempCanvas.drawImage(this.canvasBackground.canvas, 0, 0,this.canvasBoard.canvas.width,this.canvasBoard.canvas.height)
            this.tempCanvas.drawImage(this.canvasBoard.canvas, 0, 0,this.canvasBoard.canvas.width,this.canvasBoard.canvas.height)
            let isAdded = this.canvasDrawingsPerPage.findIndex(cont => cont.page === this.currPage)
            if(isAdded < 0){
                // console.log('not on list, saving new page: ' + this.currPage)
                let content  = {page: this.currPage, drawing: drawingTemp, bg: this.tempCanvas.canvas.toDataURL("image/png")}
                this.canvasDrawingsPerPage.push(content)
            }else{
                // console.log('already on list, saving new page: '  + this.currPage)
                this.canvasDrawingsPerPage = this.canvasDrawingsPerPage.filter((item) => item.page !== this.currPage)
                let content  = {page: this.currPage, drawing: drawingTemp, bg: this.tempCanvas.canvas.toDataURL("image/png")}
                this.canvasDrawingsPerPage.push(content)
            }
        },
        toggleDownload(){
            // console.log('toggling Download')
            let myCurrent = new Image()
            myCurrent.src = this.currentImage.path
       
            this.tempCanvas.drawImage(myCurrent, 0, 0,this.canvasBoard.canvas.width,this.canvasBoard.canvas.height)
            this.tempCanvas.drawImage(this.canvasBoard.canvas, 0, 0,this.canvasBoard.canvas.width,this.canvasBoard.canvas.height)
            var link = document.createElement('a');
            link.download = 'filename.png';
            link.href = this.tempCanvas.canvas.toDataURL("image/png");
            link.click();
        },
        toggleEraser(){
            if( this.isPaintTool ){ this.paintToolOff() }

            if( this.isEraserTool ){
                this.eraserToolOff()
            }else{
                this.eraserToolOn()
            }
        },
        toggleMarker(){

            if( this.isEraserTool ){ this.eraserToolOff() }

            if( this.isPaintTool ){
                this.paintToolOff()
            }else{
                this.paintToolOn()
            }
        },
        toggleColor(){
            if(this.isColorTool){
                this.$els.colorTool.style.background = '#eee'
                this.isColorTool = false
            }else{
                this.$els.colorTool.style.background = 'rgb(255, 152, 0)'
                this.isColorTool = true
            }
        },
        paintToolOn(){
            // console.log('paintToolOn')
            this.$els.markerTool.style.background = 'rgb(255, 152, 0)'
            this.isPaintToggled = true
            this.isPaintTool = true
        },
        paintToolOff(){
            // console.log('paintToolOff')
            this.$els.markerTool.style.background = '#eee'
            this.isPaintToggled = false
            this.isPaintTool = false
        },
        eraserToolOn(){
            // console.log('eraserToolOn')
            this.$els.eraserTool.style.background = 'rgb(255, 152, 0)'
            this.eraser = true
            this.isPaintToggled = true
            this.isEraserTool = true
        },
        eraserToolOff(){
            // console.log('eraserToolOff')
            this.$els.eraserTool.style.background = '#eee'
            this.eraser = false
            this.isPaintToggled = false
            this.isEraserTool = false
        },
        toggleRestore(){
            // console.log('toggling Restore')
            this.canvasBoard.drawImage(this.drawBoardTemp,0,0,this.canvasBoard.canvas.width,this.canvasBoard.canvas.height)
        },
        redraw(){
        //   console.log('redraw')
          
          this.canvasBoard.strokeStyle = this.strokeStyle;
          this.canvasBoard.lineJoin = this.lineJoin;
          this.canvasBoard.lineWidth = this.lineWidth;
        
        for (let i = 0; i < this.clickX.length; i += 1) {
                         
            // Set the drawing path
            this.canvasBoard.beginPath();
            // If dragging then draw a line between the two points
            if (this.clickDrag[i] && i) {
                this.canvasBoard.moveTo(this.clickX[i - 1], this.clickY[i - 1]);
            } else {
                // The x position is moved over one pixel so a circle even if not dragging
                this.canvasBoard.moveTo(this.clickX[i] - 1, this.clickY[i]);
            }
                this.canvasBoard.lineTo(this.clickX[i], this.clickY[i]);
            
            // Set the drawing color
            if (this.eraser) {
                // console.log('user erasing')
                this.canvasBoard.globalCompositeOperation = "destination-out"; // To erase instead of draw over with white
            } else {
                this.canvasBoard.globalCompositeOperation = "source-over";  
            }

            this.canvasBoard.lineCap = "round";
            this.canvasBoard.lineJoin = "round";
            this.canvasBoard.lineWidth = this.lineWidth;
            this.canvasBoard.lineWidth = this.lineWidth;
            this.canvasBoard.stroke();

        }
            this.canvasBoard.closePath();
            this.canvasBoard.restore();
            this.canvasBoard.globalAlpha = 1;
        },

        addClick(x,y,dragging){
            this.clickX.push(x);
            this.clickY.push(y);
            this.clickDrag.push(dragging);
        },
        pressFunction(e,me){
            // console.log('pressFunction')
            // console.log(e)
            let mouseX = (e.changedTouches ? e.changedTouches[0].pageX : e.pageX) - me.offsetLeft
            let mouseY = (e.changedTouches ? e.changedTouches[0].pageY : e.pageY) - me.offsetTop
            this.clickDrag = []
            this.paint = true;
            this.addClick(mouseX, mouseY,false);
            this.redraw();
        },
        dragFunction(e,me){
            let mouseX = (e.changedTouches ? e.changedTouches[0].pageX : e.pageX) - me.offsetLeft
            let mouseY = (e.changedTouches ? e.changedTouches[0].pageY : e.pageY) - me.offsetTop
            
            if(this.paint){
                this.addClick(mouseX, mouseY,true);
                this.redraw();
            }                   
            e.preventDefault();
        },
        releaseFunction(){
            this.paint = false
            this.clickDrag = []
            this.clickX = []
            this.clickY = []
            // this.redraw();

            // For MinDoc
            this.minClickDrag = []
            this.minClickX = []
            this.minClickY = []
            this.isReleased = true
            this.$dispatch(this.eventPrefix + 'releasePaint', this.docCoordinates.doctouch)
        },
        cancelFunction(){
            this.paint = false;
        },
        removeCanvasListeners(){
            this.canvasBoard.canvas.removeEventListener('mousedown')
            this.canvasBoard.canvas.removeEventListener('mousemove')
            this.canvasBoard.canvas.removeEventListener('mouseup')
            this.canvasBoard.canvas.removeEventListener('mouseout')

            this.canvasBoard.canvas.removeEventListener('touchstart')
            this.canvasBoard.canvas.removeEventListener('touchmove')
            this.canvasBoard.canvas.removeEventListener('touchend')
            this.canvasBoard.canvas.removeEventListener('touchcancel')
        },
        mouseDownEvent(e){
            // console.log('mousedown')

            if(e.changedTouches){
                this.docCoordinates.isMobile = true
            }

            if(this.isPaintToggled){
                this.paint = true
                this.isReleased = false
                this.isDragging = false
                this.docCoord(e)
            }
        },
        mouseMoveEvent(e){
            // console.log('mouseMoveEvent')
            this.isDragging = true;
            this.docCoord(e)
        },
        mouseUpEvent(e){
            this.releaseFunction()
        },
        mouseOutEvent(e){
            this.docLeave()
            this.cancelFunction()
        },
        addCanvasListeners(){
            // console.log('adding canvas Listeners')
             let vm = this
            this.canvasBoard.canvas.addEventListener("mousedown", function(e){
                // console.log('mousedown')
                if(vm.isPaintToggled){
                    vm.paint = true
                    vm.isReleased = false
                    vm.isDragging = false
                    vm.docCoord(e)
                }
                // let me = this
                // vm.pressFunction(e,me)
            }, false);
            this.canvasBoard.canvas.addEventListener("mousemove", function(e){
                vm.isDragging = true;
                vm.docCoord(e)
                let me = this
                // vm.dragFunction(e,me)
            }, false);
            this.canvasBoard.canvas.addEventListener("mouseup", function(e){
                vm.releaseFunction()
            }, false);
            this.canvasBoard.canvas.addEventListener("mouseout", function(e){
                vm.docLeave()
                vm.cancelFunction()
            }, false);
            // Add touch event listeners to this.canvasBoard.canvas element
            this.canvasBoard.canvas.addEventListener("touchstart", function(e){
                // console.log('touchstart')
                vm.docCoordinates.isMobile = true
                if(vm.isPaintToggled){
                    vm.paint = true
                    vm.isReleased = false
                    vm.isDragging = false
                    vm.docCoord(e)
                }
                // vm.pressFunction(e,me)
            }, false);
            this.canvasBoard.canvas.addEventListener("touchmove", function(e){
                // console.log('dragging')
                let me = this
                vm.isDragging = true
                 vm.docCoord(e)
                // vm.dragFunction(e,me)
            }, false);
            this.canvasBoard.canvas.addEventListener("touchend", function(e){
                // console.log('touchend')
                vm.isReleased = true
                vm.releaseFunction()
            }, false);
            this.canvasBoard.canvas.addEventListener("touchcancel", function(e){
                // console.log('touch cancel')
                 vm.releaseFunction()
                vm.cancelFunction()
            }, false);
  
            this.canvasIsSet = true
        },
        resizeCanvas(resize){
            // console.log('resizeCanvas')
            if(this.$els.imageBoard.clientWidth != 0){
                
                // For Staff Only
                // if( this.minimizeDefaultView && this.$els.minboardImage.clientWidth != 0){
                //     console.log('Mindoc Resize')
                //     // let mindrawboardImg = this.minCanvasBoard.canvas.toDataURL("image/png");
                //     // let mindrawingTemp = new Image()
                //     // mindrawingTemp.src = mindrawboardImg
                //     // this.mindrawBoardTemp = mindrawingTemp
                //     // mindrawingTemp = []
                //     console.log(this)
                //     console.log('clientWidth: ' + this.$els.minboardImage.clientWidth )
                //     console.log('clientHeight: ' + this.$els.minboardImage.clientHeight )

                //     this.minCanvasBoard.canvas.width = this.$els.minboardImage.clientWidth
                //     this.minCanvasBoard.canvas.height = this.$els.minboardImage.clientHeight
                //     this.minCanvasBackground.canvas.width = this.$els.minboardImage.clientWidth
                //     this.minCanvasBackground.canvas.height = this.$els.minboardImage.clientHeight

                //     this.minCanvasStyle.height = this.$els.minboardImage.clientHeight + 'px'
                //     this.minCanvasStyle.width = this.$els.minboardImage.clientWidth + 'px'

                //     this.minCanvasBoard.drawImage(this.minCanvasBoard.canvas,0,0,this.minCanvasBoard.canvas.width,this.minCanvasBoard.canvas.height)
                // }else{
                //     console.log('no minboard image yet')
                // }

                // Saving the current canvas Drawing 
                var drawboardImg = this.canvasBoard.canvas.toDataURL("image/png");
                let drawingTemp = new Image()
                drawingTemp.src = drawboardImg;
                this.drawBoardTemp = drawingTemp
                drawingTemp = []

                this.canvasBoard.canvas.width = this.$els.imageBoard.clientWidth
                this.canvasBoard.canvas.height = this.$els.imageBoard.clientHeight
                this.canvasBackground.canvas.width = this.$els.imageBoard.clientWidth
                this.canvasBackground.canvas.height = this.$els.imageBoard.clientHeight

                this.tempCanvas.canvas.width = this.$els.imageBoard.clientWidth
                this.tempCanvas.canvas.height = this.$els.imageBoard.clientHeight
                this.tempCanvas.canvas.style.width = this.$els.imageBoard.clientWidth + 'px'
                this.tempCanvas.canvas.style.height = this.$els.imageBoard.clientHeight + 'px'
                
                this.canvasBoard.canvas.style.width = this.$els.imageBoard.clientWidth + 'px'
                this.canvasBoard.canvas.style.height = this.$els.imageBoard.clientHeight + 'px'
                this.canvasBackground.canvas.style.width = this.$els.imageBoard.clientWidth + 'px'
                this.canvasBackground.canvas.style.height = this.$els.imageBoard.clientHeight + 'px'
                this.canvasStyle.zIndex = "51"
                this.minCanvasStyle.zIndex = "51"
                this.canvasStyle.height = this.$els.imageBoard.clientHeight + 'px'
                this.canvasStyle.width = this.$els.imageBoard.clientWidth + 'px'
                
                
                let myCurrent = new Image()
                myCurrent.src = this.imageList[this.ctrImage].path
                
                let imageBoardWidth = this.canvasBackground.canvas.width
                let imageBoardHeight = this.canvasBackground.canvas.height

                this.canvasBackground.drawImage(myCurrent,0,0,imageBoardWidth,imageBoardHeight)
                this.minCanvasBackground.drawImage(myCurrent,0,0,this.minCanvasBackground.canvas.width,this.minCanvasBackground.canvas.height)
                if(this.isResized){
                    let vm = this
                    setTimeout(function () {
                        vm.toggleRestore()
                    }, 1000);
                    this.isResized = false
                }else{
                    this.recoverPageDrawing()
                }

                this.isLoaded = true
            }
        },
        updateCanvas(){
            // console.log('updating canvas')
            let vm = this
            // console.log(this)
            this.canvasBoard = this.$els.boardDrawing.getContext("2d");
            this.canvasBackground = this.$els.boardBackground.getContext("2d")
            this.tempCanvas = this.$els.tempoCanvas.getContext("2d")
            
            if(this.minimizeDefaultView){
                this.minCanvasBoard = this.$els.minboardDrawing.getContext("2d");
                this.minCanvasBackground = this.$els.minboardBackground.getContext("2d")
            }
            

            setTimeout(function(){
                vm.resizeCanvas()
            }, 1000)
        },
        closeDoc (){
            let hitBtnClose = document.getElementById("close-btn");
            hitBtnClose.style.display = "none"
            // if(this.permission){
            //     document.querySelector('.anno-file').style.display = 'none';
            // }
            // if(this.minimizeDefaultView){
            //     console.log('closing Dooc with permission')
            //     this.toggleAnno()
            //     this.hideFullscreenDoc()
            // }
            
            this.$dispatch(this.eventPrefix + 'closeDoc', true);
            this.$dispatch(this.eventPrefix + 'hideBoxesduringMaximized', false,false)
            this.show.annotation = true
            
            this.canvasDrawingsPerPage = []
            // if(!this.permission){
            //     this.imageList = []
            // }
        },
        getCoords () {// Get the Div of an image for width and height passing to the ClientConnect for pointers
            let vm = this
            vm.checkSizes = setInterval( function() {
                let box = document.getElementById('boxImage') 
                if(box != undefined){
                    if (box.clientWidth != null && box.clientHeight != null){
                        vm.boxImageCoords.height = box.clientHeight
                        vm.boxImageCoords.width = box.clientWidth
                        vm.$dispatch(vm.eventPrefix + 'getCoords', vm.boxImageCoords)
                        let annotation = (document.querySelector('.annotationBox') != null) ? document.querySelector('.annotationBox') : null;
                        if (annotation != null){
                            // annotation.classList.add('onTop');
                            annotation.style.top = '';
                            annotation.style.left = '';
                            annotation.style.height = '';
                            annotation.style.display = '';
                        }
                        vm.callDocument();
                        vm.checkBrowserUsage();
                        // clearInterval(vm.checkSizes);
                    }
                }    
            },1000)
        },
        callDocument() {
            
            // console.log(this.documentMinViewDisplay)
            let test = (this.documentMinViewDisplay) ? this.$els.minboardImage : this.$els.imageBoard ;

            // console.log(test)
            let evt = this.mouseEvent("move", 1, 50, 1, 50);
            
            // console.log(this.dispatchEvent(test, evt));
            let eventForDoc = this.dispatchEvent(test, evt)
            console.log(eventForDoc.target.clientHeight+" == "+eventForDoc.target.clientWidth)
            this.docCoord(eventForDoc)
        },
        mouseEvent(type, sx, sy, cx, cy) {
            let evt
            let e = {
                bubbles: true,
                cancelable: type != 'mousemove',
                view: window,
                detail: 0,
                screenX: sx,
                screenY: sy,
                clientX: cx,
                clientY: cy,
                ctrlKey: false,
                altKey: false,
                shiftKey: false,
                metaKey: false,
                button: 0,
                relatedTarget: undefined,
            }
            if (typeof document.createEvent == 'function') {
                evt = document.createEvent('MouseEvents')
                evt.initMouseEvent(
                    type,
                    e.bubbles,
                    e.cancelable,
                    e.view,
                    e.detail,
                    e.screenX,
                    e.screenY,
                    e.clientX,
                    e.clientY,
                    e.ctrlKey,
                    e.altKey,
                    e.shiftKey,
                    e.metaKey,
                    e.button,
                    document.body.parentNode
                )
            } else if (document.createEventObject) {
            evt = document.createEventObject()
            for (prop in e) {
                evt[prop] = e[prop]
            }
            evt.button =
                {
                0: 1,
                1: 4,
                2: 2,
                }[evt.button] || evt.button
            }
            return evt
        },
        dispatchEvent(el, evt) {
            if (el.dispatchEvent) {
                el.dispatchEvent(evt)
            } else if (el.fireEvent) {
                el.fireEvent('on' + type, evt)
            }
            return evt
        },
        docCoord(event,evt_trigger){ // Trigger this line when updating the pointer cursor coordinates
            // console.log(event)
            // console.log(event.target.clientHeight+" == "+event.target.clientWidth)
            let box = document.getElementById('boxImage')
            let boxImgW = 0 
            let boxImgH = 0

            if( this.documentMinViewDisplay ){
                // console.log('using Mindoc Box')
                boxImgW = this.$els.minboardImage.clientWidth
                boxImgH = this.$els.minboardImage.clientHeight   
            }else{
                // console.log('using Maxdoc Box')
                boxImgW = this.$els.imageBoard.clientWidth
                boxImgH = this.$els.imageBoard.clientHeight   
            }

            // console.log('event Target docCoord')
            let off = event.target.getBoundingClientRect();
            // console.log(event)
            
            this.docCoordinates.top = (event.changedTouches ? event.changedTouches[0].clientY : event.clientY) - off.top
            this.docCoordinates.left =  (event.changedTouches ? event.changedTouches[0].clientX : event.clientX)  - off.left

            this.docCoordinates.doctouch = true
            
            if(!!box.clientWidth && !!box.clientHeight){
                // console.log('ImageGallery box Client height: ' + boxImgH+ ' width: ' + boxImgW)
                // this.docCoordinates.onScreenHeight = box.clientHeight
                // this.docCoordinates.onScreenWidth = box.clientWidth
                // this.docCoordinates.onScreenHeight = boxImgH
                // this.docCoordinates.onScreenWidth = boxImgW
                this.docCoordinates.onScreenHeight = boxImgH;
                this.docCoordinates.onScreenWidth = boxImgW;
            }
            
            this.docCoordinates.percentageHeight =  (0 >= this.docCoordinates.top || this.docCoordinates.onScreenHeight == 0) ? 0 : parseFloat(this.docCoordinates.top / this.docCoordinates.onScreenHeight)
            this.docCoordinates.percentageWidth  =  (0 >= this.docCoordinates.left || this.docCoordinates.onScreenWidth == 0) ? 0 : parseFloat(this.docCoordinates.left / this.docCoordinates.onScreenWidth)
            
            this.docCoordinates.paint = this.paint
            this.docCoordinates.eraser = this.eraser
            this.docCoordinates.isDragging = this.isDragging
            this.docCoordinates.isReleased = this.isReleased
            this.docCoordinates.color = this.strokeStyle
            this.docCoordinates.size  = this.lineWidth
            
            if(this.paint){
                // console.log('painting : ' + this.paint)
                this.addClick( this.docCoordinates.left, this.docCoordinates.top ,(this.isDragging) ? true:false);
                this.redraw();

                if( this.minimizeDefaultView  ){
                    // Draw for Minimized Canvas
                    let top = ( this.$els.minboxImage.clientHeight * this.docCoordinates.percentageHeight )
                    let left = ( this.$els.minboxImage.clientWidth * this.docCoordinates.percentageWidth )

                    this.minClickY.push(top)
                    this.minClickX.push(left)
                    this.minClickDrag.push(this.isDragging)

                    this.minDocDraw()
                }
            }
        
            this.initialLoad = true
            this.$dispatch(this.eventPrefix+'docCoord', this.docCoordinates, false)
            event.preventDefault();
        },
        docLeave(){
            this.docCoordinates.doctouch = false
            
            if(typeof evt_trigger == 'undefined') {
                this.$dispatch(this.eventPrefix+'docCoord', this.docCoordinates)
            }
            this.releaseFunction()
            // let c = (document.querySelector('.btn-close-doc') != null) ? document.querySelector('.btn-close-doc').style.zIndex = 47 : '';
        },
        updateCurrentImage() {
            if(typeof this.imageList[this.ctrImage] != 'undefined') {
                // console.log('updating current image on page: ' + this.currPage)
                
                // For Staff Minimized Doc
                // if(this.minimizeDefaultView){
                //     let isAdded = this.canvasDrawingsPerPage.filter((item) => item.page == this.currPage)

                //     if( isAdded.length != 0 ){
                //     console.log('using canvas image')
                //         let ob = { annotation: this.imageList[this.ctrImage].annotation, id: this.imageList[this.ctrImage].id, path: isAdded[0].bg }
                //         this.currentImage = ob
                //     }else{
                //         console.log('using image list')
                //         this.currentImage = this.imageList[this.ctrImage]
                //     }
                // }else{
                    // For Clients
                    // console.log('using image list')
                    this.currentImage = this.imageList[this.ctrImage]
                // }
            }
        },
        nextPage(evt_trigger) {
            if(this.imageList.length-1 > this.ctrImage) {
                // console.log('isMaximized: ' + this.isMaximized)
                // console.log('nextPage')
                this.ctrImage ++

                // // For Staff Minimized Doc
                // if( this.minimizeDefaultView ){
                //     console.log('only staff allowed')
                //     if( this.isMaximized ){
                //         this.saveCurrentDrawing()
                //     }
                // }else{
                //     if( this.isMaximized ){
                //         this.saveCurrentDrawing()
                //     }
                // }

                // let vm = this
                // setTimeout(function(){
                //     vm.currPage ++
                    // console.log('next Page, Current Page: ' + this.currPage)
                    this.updateCurrentImage()
                //     vm.updateCanvas()
                // }, 1000)
           
                // console.log('current ctrImage: ' + this.ctrImage + ' current page: ' + this.currPage)
            }
            if(typeof evt_trigger == 'undefined') {
                this.$dispatch(this.eventPrefix+'next-page', this.currentImage.id)
            } else {
                if (this.isStaff) { this.annotation = this.imageList[this.currentImage.id].annotation; }
            }
        },
        prevPage(evt_trigger) {
            if(this.ctrImage > 0) {
                // console.log('prevPage')
                // console.log('isMaximized: ' + this.isMaximized)
                this.ctrImage --
                
                // For Staff Minimized Doc
                // if( this.minimizeDefaultView ){
                //     if( this.isMaximized ){
                //         this.saveCurrentDrawing()
                //     }
                // }else{
                //     if( this.isMaximized ){
                //         this.saveCurrentDrawing()
                //     }
                // }

                // let vm = this
                // setTimeout(function(){
                //     vm.currPage --
                    this.updateCurrentImage()
                //     vm.updateCanvas()
                // }, 1000)
                // this.ctrImage --
                // let isAdded = this.canvasDrawingsPerPage.findIndex(cont => cont.page === this.currPage)
                // console.log('isMaximized: ' + this.isMaximized)
                // if( isAdded >= 0 || this.isMaximized ){
                //     console.log('saving and updating image')
                //     if(this.isMaximized){
                //         this.saveCurrentDrawing()
                //         this.currPage --
                //           let vm = this
                //         setTimeout(function(){
                //             vm.updateCurrentImage()
                //             vm.updateCanvas()
                //         },1000)
                //     }else{
                //         this.currPage --
                //         this.updateCurrentImage()
                //     }
                // }else{
                //     console.log('updatingcurrentimage only')
                //     this.currPage --
                //     this.updateCurrentImage()
                // }
                // console.log('current ctrImage: ' + this.ctrImage + ' current page: ' + this.currPage)
            }
            if(typeof evt_trigger == 'undefined') {
                this.$dispatch(this.eventPrefix+'prev-page', this.currentImage.id)
            } else {
                if (this.isStaff) { this.annotation = this.imageList[this.currentImage.id].annotation; }
            }
        },
        refreshCtr() {
            this.ctrImage = 0
            let hitBtnClose = document.getElementById("close-btn");
            if(!!hitBtnClose){
                // hitBtnClose.style.display = "unset" 
            }
        },
        handleResize(){
            // console.log('handleResize')
            let box = document.getElementById('boxImage');
            // console.log('boxImage')
            // console.log(box)
            if(box){
                this.boxImageCoords.width = box.clientWidth
                this.boxImageCoords.height = box.clientHeight
                this.isResized = true
                clearInterval(this.widthInterval)
                // this.updateCanvas()
                
                // this.canvasBackground.width = '492px'
                // this.canvasBackground.height = this.$els.imageBoard.clientHeight
                // this.canvasStyle.height = this.$els.imageBoard.clientHeight
                // this.canvasStyle.width = '492px'
                // console.log('ImageBoard clientWidth')
                // console.log(this.$els.imageBoard.clientWidth)
                // console.log('ImageBoard clientHeight')
                // console.log(this.$els.imageBoard.clientHeight)
            } else { return false; }
            
            this.$dispatch(this.eventPrefix + 'getCoords', this.boxImageCoords)
            this.mobileAdjustment()
        },
       
        zoom(){
            this.currPage = this.ctrImage + 1
            this.show.Mindocument = (!this.show.Mindocument) ? true : false;
            this.mobileAdjustment()
            this.minimizeDoc()
            let annotation = document.querySelector('.annotationBox')
            if (annotation != null){
                annotation.style.top = '';
                annotation.style.left = '';
                annotation.style.height = '';
                annotation.style.display = '';
            }
            let documentImage = document.getElementById("docPage")
            if(documentImage.classList.value == "onTop"){
                documentImage.classList.remove("onTop");
            }
            if(!this.show.Mindocument){
                this.documentMinViewDisplay = false
                this.showFullscreenDoc()
                // this.handleResize()
                this.hideMinDoc()
                this.$dispatch(this.eventPrefix + 'Maximized')
                if (window.innerWidth > 1367) {
                    if (annotation != null) {
                        annotation.style.top = '383px';
                        annotation.style.left = '168px';
                        annotation.style.height = '250px';
                        annotation.style.display = '';
                    }

                    if(this.minimizeDefaultView) {
                        this.documentSetIntoLeft() 
                    }
                }
                // this.callDocument()
            }else{
                this.documentMinViewDisplay = true
                // this.saveCurrentDrawing()
                // this.paintToolOff()
                this.hideFullscreenDoc()
                this.showMinDoc()
                if (window.innerWidth > 1367) {
                    if (annotation != null) {
                        // annotation.classList.add('onTop');
                        annotation.style.top = '';
                        annotation.style.left = '';
                        annotation.style.height = '';
                        annotation.style.display = '';
                    }
                }
                // let vm = this
                // setTimeout(function(){
                //     vm.updateCurrentImage()
                //     vm.showMinDoc()
                //     vm.$dispatch(vm.eventPrefix + 'Minimized')
                // }, 1000)
                this.$dispatch(this.eventPrefix + 'Minimized')
                // this.callDocument()
            }
            this.$dispatch(this.eventPrefix + 'hideBoxesduringMaximized', !this.show.Mindocument,this.boxImageSizeNotLeft)
            
        },
        documentSetIntoLeft(){
            let vm=this
            let boxImage = document.querySelector('.ui-image-gallery .gallery-con .image-item .box');
            if(boxImage != null){
                // let findImgDoc = setInterval( function() { 
                    if(!!boxImage.clientWidth) {
                        if (boxImage.clientWidth > 500 && boxImage.clientWidth < 897) {
                            if(boxImage.clientWidth != 1024 || boxImage.clientWidth != 1366 ||
                                boxImage.clientWidth != 812 || boxImage.clientWidth != 768 ||
                                boxImage.clientWidth != 736 || boxImage.clientWidth != 568 ){
                                // boxImage.style.left = '20%';
                                vm.boxImageSizeNotLeft = false
                            }
                        } else {
                            boxImage.style.left = '';
                            vm.boxImageSizeNotLeft = true
                        }
                        // clearInterval(findImgDoc);
                    }
                // }, 1000);
            }    
        },
        toggleDocShare(){
            let link = this.getDocLink(this.document)
            let size = this.document.size
            this.$dispatch('docmodalshare:sharedoc', '', link ,size);
        },
        getDocLink(doc) {
            let dateHolder = new Date();
            dateHolder.setMinutes(dateHolder.getMinutes() + 1);
            this.parsedDateTime = [Cookie.get('peerId'), dateHolder.getTime()].join('-');
            return `${APP_URL}/${this.parsedDateTime}/document/${doc.document_id}/download/${doc.file_name}`
            //return `${APP_URL}/document/${doc.document_id}/download/${doc.file_name}
        },
        minimizeDoc() {
            // console.log('minimize Doc')
            // let x = (document.querySelector('.annotationBox') != null) ? document.querySelector('.annotationBox').classList.add('onTop') : '';
            // let y = (document.querySelector('.btn-close-doc') != null) ? document.querySelector('.btn-close-doc').style.zIndex = 51 : '';
            // let z = (document.querySelector('.ui-image-gallery .gallery-con .image-item .footer-doc-toolbar') != undefined) ? document.querySelector('.ui-image-gallery .gallery-con .image-item .footer-doc-toolbar').classList.add('onTop') : '';
        },
        removeClassOnTop(){
            // console.log('removing class on Top')
            // document.querySelector('#close-btn .gallery-con .image-item .box .footer-doc-toolbar').classList.remove('onTop') : '';
        },
        mobileAdjustment(){

            let fileViewer = document.querySelector(".fileViewer");
            
            if(window.innerWidth <= 567){
                this.removeClassOnTop()
                this.mobileViewDisplay = this.minimizeDefaultView
                this.documentMinViewDisplay = true
                this.hideFullscreenDoc()
                this.showMinDoc()


                if(fileViewer.hasAttribute("style")){
                    fileViewer.removeAttribute("style")
                    fileViewer.setAttribute("style","margin-bottom:50px !important;");
                }else{
                    fileViewer.setAttribute("style","margin-bottom:50px !important;");
                }

            } else if (window.innerWidth <= 812 && window.innerWidth >= 568) {
                this.removeClassOnTop()
                if(window.innerWidth != 768) {
                    this.mobileViewDisplay = false
                    this.documentMinViewDisplay = this.mobileViewDisplay
                    this.showFullscreenDoc()
                    this.hideMinDoc()

                    if(fileViewer.hasAttribute("style")){
                        fileViewer.removeAttribute("style")
                        fileViewer.setAttribute("style","margin-bottom:47px !important;");
                    }else{
                        fileViewer.setAttribute("style","margin-bottom:47px !important;");
                    }

                    // let documentImage = document.getElementById("docPage")
                    // if(documentImage != undefined){
                    //     if(documentImage.classList.value == "onTop"){
                    //         documentImage.classList.remove("onTop");
                    //     }
                    // }    
                } else {
                    this.documentMinViewDisplay = this.show.Mindocument == this.minimizeDefaultView
                    this.mobileViewDisplay = this.minimizeDefaultView
                    // let documentImage = document.getElementById("docPage")
                    // if(documentImage != undefined){
                    //     if(documentImage.classList.value == "onTop"){
                    //         documentImage.classList.remove("onTop");                        
                    //     }
                    // }
                    if(this.minimizeDefaultView) {
                        this.$dispatch(this.eventPrefix + 'hideBoxesduringMaximized', false,false)
                    }    
                }
            } else {
                this.removeClassOnTop()
                this.documentMinViewDisplay = this.show.Mindocument == this.minimizeDefaultView
                this.mobileViewDisplay = this.minimizeDefaultView
                if(this.minimizeDefaultView) {
                    this.documentMinViewDisplay = true;
                    this.showMinDoc()
                    this.hideFullscreenDoc()
                    this.$dispatch(this.eventPrefix + 'hideBoxesduringMaximized', false,false)
                } else {
                    this.showFullscreenDoc()
                    // this.hideFullscreenDoc()
                    this.hideMinDoc()
                }
            }
        },
        // this.$dispatch(this.eventPrefix + 'hideBoxesduringMaximized', false,false)
    },
    events: {
        'closed'(){
            this.$els.colorTool.style.background = '#eee'
            this.isColorTool = false
        }
    },
    ready: function () {
        window.addEventListener('resize', this.handleResize)
        this.handleResize()
        let vm = this
        window.addEventListener('touchend', function(e){
            if(e.target.localName != 'canvas'){
                vm.docCoordinates.doctouch = false
                vm.releaseFunction()
            }
        })

        // this.client loop

        // for {
        //     let element = 'pointer'+client.id
        //     document.getElementById(element.toString());
        // }
    },
    beforeDestroy: function () {
        window.removeEventListener('resize', this.handleResize)
    },
}
</script>
<style lang="sass">
.inner-content{
     .box{
        height: inherit;
        display: inline-block;
        position: relative;
        .pointerDoc{
            position: absolute;
            z-index: 100;
            padding: 10px;
            border-radius: 50%;
            background: rgba(255, 238, 114, 0.5);
            opacity: 0;
        }
        .inner-box{
            z-index: 1111;
            position: absolute;
            background: red;
            height: 50px;
            width: 50px;
        }
    }
}
.ui-image-gallery {
    height: 100%;
    position: relative;
    .gallery-con {
        list-style: none;
        margin: 0;
        padding: 0;
        height: inherit;
        .image-item {
            position: relative;
            height: inherit;
            .box {
                z-index: 45;
            }
            img.imgResponsive {
                z-index: 40;
                position: relative;
                top: 50%;
                left: 50%;
                -webkit-transform: translate(-50%, -50%);
                -moz-transform: translate(-50%, -50%);
                -ms-transform: translate(-50%, -50%);
                -o-transform: translate(-50%, -50%);
                transform: translate(-50%, -50%);
                max-height: 100%;
            }
            @media screen and (min-width : 768px){
                img.imgResponsive{
                    
                }
            }
        }
    }       
    .gallery-controls {
        position: absolute;
        width: 100%;
        top: 0;
        span.lb-btn {
            z-index: 40 !important;
            border-radius: 25px;
            background-color: #fff;
            cursor: pointer;
            box-shadow: 0px 2.4px 14px -1px #1a1a1a;
            position: fixed;
            top: 50%;
            left: 15px;
            -webkit-transform: translate(0, -50%);
            transform: translate(0, -50%);
            .ui-icon {
                font-size: 50px;
                color: #07348e;
            }
            &.nextPage {
                right: 15px;
                left: auto;
            } 
        }
    }
}
.custom-container {
  margin-right: auto;
  margin-left: auto;
  padding-left: 15px;
  padding-right: 15px;
}
.image-item{
    #boxImage{
        margin: 0 auto !important;
        // width: max-content;
        // width: -moz-available;
        #docPage{
            display: block !important;
            width: auto;
            height: 100% !important;
        }
    }
}
.image-item{
    .box{
        .docBtn{
            position: absolute;
            padding: 5px;
            border-radius: 100%;
            background: #080707;
            color: #fff;
            z-index: 40;
            top: 10px;
            right: 10px;
        }
        .ui-close-button {
            position: absolute;
            top: 10px;
            right: 10px;
            .ui-fab-mini {
                width: 30px;
                height: 30px;
                i {
                    font-size: 22px;
                    position: absolute;
                    top: 5px;
                }
                span {
                    font-family: 'NotoSans-Medium';
                    bottom: 10px;
                    position: absolute;
                    font-size: 12px;
                }
            }
        }
    }
}
@media (max-width: 767px){
    .image-item{
        div.box{
            #docPage{
            }
        }
    }
}
@media (min-width: 768px){
    .image-item{
        div.box{
            #docPage{
            }
        }
    }
}   
.docuLayout .document-footer .docu-download { 
    .ui-button .ui-button-content {
        .ui-button-text { font-weight: bold; }
    }
    .ui-button:hover{
        background: #4e76c8 radial-gradient(circle, transparent 2%, #4e76c8 2%) center/15000%;
            .ui-button-content {
                filter: brightness(0) invert(2);
        }
    }
}
.docuLayout .document-footer .docu-text { 
    .ui-button .ui-button-content {
        .ui-button-text { font-weight: bold; }
    }
    .ui-button:hover{
        background: #4e76c8 radial-gradient(circle, transparent 2%, #4e76c8 2%) center/15000%;
            .ui-button-content {
                filter: brightness(0) invert(2);
        }
    }
}
.docuLayout .document-footer .docu-arrow_left .btn-al:hover {
    background: #4e76c8 radial-gradient(circle, transparent 2%, #4e76c8 2%) center/15000%;
    .ui-icon {
        filter: brightness(0) invert(2);
    }
} 

.docuLayout .document-footer .docu-arrow_right .btn-ar:hover {
    background: #4e76c8 radial-gradient(circle, transparent 2%, #4e76c8 2%) center/15000%;
    .ui-icon {
        filter: brightness(0) invert(2);
    }
} 


.footer-doc-toolbar {
    .footer-iconleft {
        .footer-doc-text {
            .ui-button .ui-button-content {
                padding: 9px;
                .ui-button-text { font-weight: bold; }
            }
            .ui-button:hover {
                background: #4e76c8 radial-gradient(circle, transparent 2%, #4e76c8 2%) center/15000%;
                .ui-button-content {
                    filter: brightness(0) invert(2);
                }
            }
        }
        .footer-doc-download {
            .ui-button .ui-button-content {
                padding: 9px;
                .ui-button-text { font-weight: bold; }
            }
            .ui-button:hover {
                background: #4e76c8 radial-gradient(circle, transparent 2%, #4e76c8 2%) center/15000%;
                .ui-button-content {
                    filter: brightness(0) invert(2);
                }
            }
        }
    }
    .footer-iconright .footer-doc-arrow_left .btn-al:hover {
        background: #4e76c8 radial-gradient(circle, transparent 2%, #4e76c8 2%) center/15000%;
        .ui-icon {
            filter: brightness(0) invert(2);
        }
    }
    .footer-iconright .footer-doc-arrow_right .btn-ar:hover {
        background: #4e76c8 radial-gradient(circle, transparent 2%, #4e76c8 2%) center/15000%;
        .ui-icon {
            filter: brightness(0) invert(2);
        }
    }
}



</style>

<style lang="scss" scoped>
    .hideDocument {
        z-index: -2 !important;
    }
    // .image-item{ .box{ opacity: 0 } }
    .pointerDoc{
        padding: 10px;
        border-radius: 50%;
    }
    .hideAnnotation {
        z-index: -3 !important;
    }
    .onTop {
        z-index:50 !important;
    }
    .custom-container {
        margin-right: auto !important;
        margin-left: auto !important;
        padding-left: 15px;
        padding-right: 15px;
    }
    .proto .peerCon .protoConnectContainer .wrapper .row:not(.middle-xs):not(.center-xs) .docuLayout .document-footer .docu-download .ui-button-icon {
        background: url(../image/toolbar-sprite.png) no-repeat scroll 5px 0 transparent;
        background-position-x: -587px;
        background-position-y: -14px;
        font-size: 0;
        width: 20px;
        margin-right: 10px;
        height: 18px;     
    }
    .proto .peerCon .protoConnectContainer .wrapper .row:not(.middle-xs):not(.center-xs) .docuLayout .document-footer .docu-text .ui-button-icon {
        background: url(../image/toolbar-sprite.png) no-repeat scroll 5px 0 transparent;
        background-position-x: -535px;
        background-position-y: -58px;
        font-size: 0;
        width: 20px;
        margin-right: 10px;
        height: 18px;
    }
    .proto .peerCon .protoConnectContainer .wrapper .row:not(.middle-xs):not(.center-xs) .docuLayout .document-footer .docu-arrow_left .ui-button-icon {
        background: url(../image/toolbar-sprite.png) no-repeat scroll 5px 0 transparent;
        background-position-x: -94px;
        background-position-y: -59px;
        font-size: 0;
        width: 20px;
        height: 18px;
        margin:0;
    }
    .docu-arrow_left .btn-al{
        border-radius: 50%;
        border: none;
        background: #fff;
        margin: 10p;
        padding: 9px;
        position: absolute;
        top: 7px;
        right: 130px;
        cursor: pointer;
    }
     .proto .peerCon .protoConnectContainer .wrapper .row:not(.middle-xs):not(.center-xs) .docuLayout .document-footer .docu-arrow_right .ui-button-icon {
        background: url(../image/toolbar-sprite.png) no-repeat scroll 5px 0 transparent;
        background-position-x: -142px;
        background-position-y: -59px;
        font-size: 0;
        width: 20px;
        height: 18px;
        margin:0;
    }
    .docu-arrow_right .btn-ar{
        border-radius: 50%;
        border: none;
        background: #fff;
        padding: 9px;
        position: absolute;
        top: 7px;
        right: 70px;
        cursor: pointer;
    }
     .proto .peerCon .protoConnectContainer .wrapper .row:not(.middle-xs):not(.center-xs) .docuLayout .document-footer .docu-zoom .ui-button-icon {
        background: url(../image/square-zoom-icon.png) no-repeat transparent;
        background-size: contain;
        font-size: 0;
        width: 21px;
        height: 21px;
        margin: 0;
        -webkit-filter: contrast(4) invert(1);
        filter: contrast(4) invert(1);
        -webkit-filter: brightness(0) invert(1);
        filter: brightness(0) invert(2);
    }
     .proto .peerCon .protoConnectContainer .wrapper .row:not(.middle-xs):not(.center-xs) .docuLayout .document-footer .docu-zoom:hover {
        right: 13px;
        .ui-button-icon {
            width: 23px;
            height: 23px;
        }
    }
    .docu-zoom {
        .btn-zoom{
            border: none;
            background: transparent;
            padding: 6px;
            position: absolute;
            top: 7px;
            right: 15px;
            cursor: pointer;
        }
    }
    .docu-pagenate{
        font-size: 18px;
        color: #fff;
        font-weight: bold;
        font-family: calibri;
        position: absolute;
        top: 16px;
        right: 190px;
        a{
            color:#fff;
            padding: 7px;
        }
    }
    .docu_text-pagenate{
        font-size: 15px;
        color: #3b5998;
        font-weight: bold;
        font-family: calibri;
        position: absolute;
        top: 20px;
        left: 90px;
        a{
            color:#3b5998;
            padding: 2px;
        }
    }
    textarea.ui-annotation-textarea {
        overflow: hidden scroll;
        margin: 12px 1px 12px 1px;
        width: 100%;
        height: 90%;
        font-family: NotoSans-Regular;
        display: block;
        font-weight: 400;
        line-height: 1.33em;
        letter-spacing: 0.01em;
        color: rgb(26, 26, 26);
        font-size: 12px;
        border: 0px;
        cursor: default;
        resize: none;
    }
    textarea.ui-annotation-textarea::-webkit-scrollbar {
        width: 1rem;
    }
    textarea.ui-annotation-textarea::-webkit-scrollbar-thumb {
        background-color: #0d275f;
        outline: 1px solid #0d275f;
    }
    textarea.ui-annotation-textarea::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
    }
    .proto .peerCon .protoConnectContainer .wrapper .row:not(.middle-xs):not(.center-xs) .docuLayout .document-footer .ui-button-text {
        font-family: 'NotoSans-Bold';
    }

    // Document CSS
    .docBox{
        top: 65px ;
        left: 560px ;
    }
    .annotation{
        top: 65px ;
        left: 181px ;
    }
    .docuLayout{
        background: #fff;
        width: 580px;
        height: 350px;
        z-index: 46;
        -ms-flex-positive: 1;
        flex-grow: 1;
        position: absolute;
        right: 50px;
        display: block;
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.2), 0 2px 2px rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.12);
        .ui-close-button {
            position: absolute;
            right: -19px;
            top: -19px;
        }
        .DocumentWrapper{
            height: 100%;
        }
        .document-content{
            height:100%;
            .inner-document{
                height: 100%;
                width: 100%;
                // padding: 20px;
                overflow: auto;
                border-bottom: none;
                background: #ffffff;
                font-size: 14px;
                resize: none;
                p{
                    font-size: 35px;
                    color: #333333;
                    font-weight: bold;
                    position: absolute;
                    top: 120px;
                    left: 185px;
                }
            }
        }
        .document-footer{
            bottom: -50px;
            position: absolute;
            background: #3B5998;
            width: 100%;
            height: 50px;
            .ui-button {
                margin: 5px;
                background: #ffffff;
                color: #3B5998;
            }
        }
    }
    .docuLayout.hide{
        display:none;
    }


    .footer-toolbar-icons {
        display: inline-block;
        width: 32%;
    }




   
    .footer-doc-toolbar {
        background: #3B5998;
        height: 50px;
        position: relative;
        z-index: 51;
        margin-top: -50px;
        .footer-iconleft {
            float: left;
        }
        .footer-iconright {
            float: right;
        }
        .ui-button {
            margin: 10px 0px 0px 7px;
            background: #ffffff;
            color: #3B5998;
            font-size: 10px;
            padding: 0px;
            height: 33px;
        }
        .footer-doc-pagenate{
            font-size: 18px;
            color: #fff;
            font-weight: bold;
            font-family: calibri;
            position: absolute;
            top: 16px;
            right: 149px;
            a{
                color:#fff;
                padding: 7px;
            }
        }
        .footer-doc-arrow_left{
             .ui-button-icon {
                background: url(../image/toolbar-sprite.png) no-repeat scroll 5px 0 transparent;
                background-position-x: -94px;
                background-position-y: -59px;
                font-size: 0;
                width: 20px;
                height: 18px;
                margin:0; 
            }
            .btn-al {
                border-radius: 50%;
                border: none;
                background: #fff;
                padding: 6px;
                position: absolute;
                top: 9px;
                right: 95px;
                cursor: pointer;
            }
        }
        .footer-doc-arrow_right {
            .ui-button-icon {
                background: url(../image/toolbar-sprite.png) no-repeat scroll 5px 0 transparent;
                background-position-x: -142px;
                background-position-y: -59px;
                font-size: 0;
                width: 20px;
                height: 18px;
                margin:0;
            }
            .btn-ar{
                border-radius: 50%;
                border: none;
                background: #fff;
                padding: 6px;
                position: absolute;
                top: 9px;
                right: 55px;
                cursor: pointer;
            }
        }

        .footer-doc-zoom {
            .ui-button-icon {
                background: url(../image/square-zoom-icon.png) no-repeat transparent;
                background-size: contain;
                font-size: 0;
                width: 20px;
                height: 20px;
                margin: 0;
            }
            .ui-button-icon:hover {
                width: 22px;
                height: 22px;
            }
            .btn-zoom{
                border: none;
                padding: 5px;
                position: absolute;
                background: transparent;
                top: 10px;
                right: 12px;
                cursor: pointer;
                -webkit-filter: contrast(4) invert(1);
                filter: contrast(4) invert(1);
                -webkit-filter: brightness(0) invert(1);
                filter: brightness(0) invert(2);
            }
            .btn-zoom:hover { right:11px; }
        }
        
    }
    // =======Document Media Queries======
    @media (max-width: 767px) and (orientation: landscape) {
       
        // .footer-doc-toolbar { margin-top: 0px;}
        .ui-image-gallery {
            display: block;
            .gallery-con{
                height: 100%;
                .image-item #boxImage {
                overflow: scroll;
                height: 100%;
                width: -webkit-fill-available;
                .fill {
                    height: unset !important;
                    #docPage {
                        width: 100% !important;
                    }
                    img.imgResponsive {
                        -webkit-transform: translate(-50%, 0%);
                        -moz-transform: translate(-50%, 0%);
                        -ms-transform: translate(-50%, 0%);
                        -o-transform: translate(-50%, 0%);
                        transform: translate(-50%, 0%);
                        
                    }
                }
            }}
            .footer-doc-toolbar .footer-doc-zoom .btn-zoom {
                display: none;
            }
            .annotation {
                top: 18.8px;
                left: 14.6px;
            }
        }
    }
    @media (max-width: 767px) and (orientation: portrait)   { 
        .gallery-con{
            height:-webkit-fill-available;
            .image-item #boxImage {
            width:-webkit-fill-available;
        }}
        .docuLayout{
            width: 100%;
            top: 191.4px;
            left: 0px;
            height: 200px;
            .document-content .inner-document p{
                font-size: 35px;
                color: #333333;
                font-weight: bold;
                position: absolute;
                top: 120px;
                left: 120px;
            }
             .document-footer .ui-button { // For Document Text and Download Button
                margin: 13px 0px 10px 7px;
                font-size: 10px;
                padding: 3px 4px;  
            }
            .ui-close-button{
                right: 2px;
                top: 3px;
            }
            .docu-arrow_right .btn-ar {
                    right: 5px;
                }
            .docu-arrow_left .btn-al {
                right: 50px;
            }
            .docu-pagenate {
                font-size: 16px;
                right: 95px;
                a{
                    padding:0;
                }
            }
        }
        .ui-image-gallery {
            display: block;
            .gallery-con .image-item #boxImage {
                width:-webkit-fill-available;
            }
            .docu-zoom .btn-zoom {
                display: none;
            }
            .annotation {
                top: 18.8px;
                left: 14.6px;
                height: 167px;
                width: 300px;
            }
        }
        
    }

     @media only screen 
        and (min-device-width: 320px) 
        and (max-device-width: 320px) 
        and (orientation: portrait) 
        and (-webkit-min-device-pixel-ratio: 2) {
        /* STYLES GO HERE */
        .ui-image-gallery {
            .gallery-con .image-item #boxImage {
                width:-webkit-fill-available;
            }
            .docuLayout {
                width: 320px;
                height: 227px;
                top: 190.8px;
                left: 0px;
                .DocumentWrapper .ui-close-button .ui-fab-mini{
                    width: 25px;
                    height: 25px;
                    .ui-icon {
                        font-size: 15px;
                    }
                }
                .meetingSales {
                    display: none;
                }
                .ui-close-button{
                    right: 2px;
                    top: 3px;
                }
                .document-footer .ui-button { // For Document Text and Download Button
                    margin: 12px 0px 0 7px;
                    font-size: 8px;
                    padding: 3px 4px;              
                }
                .docu-pagenate {
                    right: 90px;
                }
                .docu-arrow_right .btn-ar {
                    right: 13px;
                    padding: 4px;
                    margin: 3px 0;
                }
                .docu-arrow_left .btn-al {
                    right: 52px;
                    padding: 4px;
                    margin: 3px 0;
                }
                .docu-zoom .btn-zoom {
                    display: none;
                }
            }
            .annotation {
                top: 18.8px;
                left: 14.6px;
                width: 280px !important;
            }
            
        }
    }

    @media only screen 
        and (min-device-width: 375px) 
        and (max-device-width: 375px) 
        and (orientation: portrait) 
        and (-webkit-min-device-pixel-ratio: 2) {
        /* STYLES GO HERE */
        .ui-image-gallery {
            .gallery-con .image-item #boxImage {
                width:-webkit-fill-available;
            }
            .docuLayout {
                width: 375px;
                height: 266px;
                top: 200.4px;
                left: 0px;
                .DocumentWrapper .ui-close-button .ui-fab-mini{
                    width: 30px;
                    height: 30px;
                    .ui-icon {
                        font-size: 18px;
                    }
                }
                .meetingSales {
                    display: none;
                }
                .ui-close-button{
                    right: 2px;
                    top: 3px;
                }
                .document-footer .ui-button { // For Document Text and Download Button
                    margin: 12px 0px 0 7px;
                    font-size: 8px;
                    padding: 3px 4px;              
                }
                .docu-pagenate {
                    right: 140px;
                }
                .docu-arrow_right .btn-ar {
                    right: 23px;
                    padding: 7px;
                }
                .docu-arrow_left .btn-al {
                    right: 70px;
                    padding: 7px;
                }
                .docu-zoom .btn-zoom {
                    display: none;
                }
            }
            .annotation {
                top: 26px;
                left: 15px;
            }
            
        }
    }

    @media only screen 
        and (min-device-width: 414px) 
        and (max-device-width: 414px) 
        and (orientation: portrait) 
        and (-webkit-min-device-pixel-ratio: 2) {
        /* STYLES GO HERE */
        .ui-image-gallery {
            .gallery-con .image-item #boxImage {
                width:-webkit-fill-available;
            }
            .docuLayout {
                width: 414px;
                top: 221.333px;
                left: 0px;
                height: 290px;
            }
            .annotation {
                top: 46.1111px;
                left: 39.3333px;
            }
            .meetingSales {
                display: none;
            }
            .ui-close-button{
                right: 2px;
                top: 3px;
            }
            .document-footer .ui-button { // For Document Text and Download Button
                margin: 11px 0px 0 7px;
                font-size: 10px;
                padding: 5px 5px;                
            }
            .docu-pagenate {
                right: 140px;
            }
            .docu-arrow_right .btn-ar {
                right: 23px;
                padding: 7px;
            }
            .docu-arrow_left .btn-al {
                right: 70px;
                padding: 7px;
            }
            .docu-zoom .btn-zoom {
                display: none;
            }
        }
    }

    @media only screen 
        and (min-device-width: 768px) 
        and (max-device-width: 768px) 
        and (orientation: portrait) 
        and (-webkit-min-device-pixel-ratio: 2) {
        /* STYLES GO HERE */
        .ui-image-gallery {
            .docuLayout {
                top: 460px;
                left: 91.6px;
                .document-content {
                    .document-footer {
                        .ui-button{
                            margin: 11px 0 0 9px;
                            padding: 5px 7px;
                        }
                        .docu-pagenate {
                            right: 145px;
                        }
                        .docu-arrow_right .btn-ar {
                            right: 55px;
                            padding: 4px;
                            top: 10px;
                        }
                        .docu-arrow_left .btn-al {
                            right: 98px;
                            padding: 4px;
                            top: 10px;
                        }              
                    }
                }
            }
            .annotation {
                top: 60.6px;
                left: 360.2px;
            }
            .meetingSales {
                display: none;
            }
            .gallery-con {
                width: 100%;
                height: 100%;
                .image-item #boxImage {
                    width: -webkit-fill-available;
                    #docPage {
                        width: 100% !important;
                    }
                }
            }
        }
    }
    
    @media only screen 
        and (min-device-width: 1024px) 
        and (max-device-width: 1024px) 
        and (orientation: portrait) 
        and (-webkit-min-device-pixel-ratio: 2) {
        /* STYLES GO HERE */
        .ui-image-gallery {
            .docuLayout {
                top: 478.333px;
                left: 239.667px;
            }
            .annotation {
                top: 60.6667px;
                left: 630.667px;
            }
            .gallery-con {
                width: 100%;
                height: 100%;
                .image-item #boxImage {
                    width: -webkit-fill-available;
                    #docPage {
                        width: 100% !important;
                    }
                }
            }
        }
    }

    /* Landscape */
    @media only screen 
        and (min-device-width: 375px) 
        and (max-device-width: 667px) 
        and (-webkit-min-device-pixel-ratio: 2)
        and (orientation: landscape) { 
        /* STYLES */ 
        .ui-image-gallery {
            .gallery-con{
                height: 100%;
                .image-item #boxImage {
                overflow: scroll;
                height: 100%;
                width: -webkit-fill-available;
                .fill {
                    height: unset !important;
                    #docPage {
                        width: 100% !important;
                    }
                    img.imgResponsive {
                        -webkit-transform: translate(-50%, 0%);
                        -moz-transform: translate(-50%, 0%);
                        -ms-transform: translate(-50%, 0%);
                        -o-transform: translate(-50%, 0%);
                        transform: translate(-50%, 0%);
                    }
                }
            }}
            .footer-doc-toolbar .footer-doc-zoom .btn-zoom {
                display: none;
            }
            .annotation {
                height:200px !important;
                width: 300px !important;
                top: 24px;
                left: 45px;
            }
        }

    }

     @media only screen 
        and (min-device-width: 667px) 
        and (max-device-width: 736px) 
        and (-webkit-min-device-pixel-ratio: 3)
        and (orientation: landscape) { 
        /* STYLES */ 
        .ui-image-gallery {
            .gallery-con{
                height: 100%; 
                .image-item #boxImage {
                overflow: scroll;
                height: 100%;
                width: -webkit-fill-available;
                .fill {
                    height: unset !important;
                    #docPage {
                        width: 100% !important;
                    }
                    img.imgResponsive {
                        -webkit-transform: translate(-50%, 0%);
                        -moz-transform: translate(-50%, 0%);
                        -ms-transform: translate(-50%, 0%);
                        -o-transform: translate(-50%, 0%);
                        transform: translate(-50%, 0%);
                    }
                }
            }}
            .footer-doc-toolbar .footer-doc-zoom .btn-zoom {
                display: none;
            }
            .annotation {
                height:200px !important;
                width: 300px !important;
                top: 24px;
                left: 45px;
            }
        }

    }
    
    @media only screen 
        and (min-width: 736px) 
        and (max-width: 767px) 
        and (orientation: landscape) { 
        /* STYLES */ 
        .ui-image-gallery {
            .gallery-con{
                height: 100%;
                .image-item #boxImage {
                overflow: scroll;
                height: 100%;
                width: -webkit-fill-available;
                .fill {
                    height: unset !important;
                    #docPage {
                        width: 100% !important;
                    }
                    img.imgResponsive {
                        -webkit-transform: translate(-50%, 0%);
                        -moz-transform: translate(-50%, 0%);
                        -ms-transform: translate(-50%, 0%);
                        -o-transform: translate(-50%, 0%);
                        transform: translate(-50%, 0%);
                    }
                }
            }}
            .footer-doc-toolbar .footer-doc-zoom .btn-zoom {
                display: none;
            }
            .annotation {
                height:200px !important;
                width: 300px !important;
                top: 24px;
                left: 45px;
            }
        }

    }
    @media only screen 
        and (min-device-width: 767px) 
        and (max-device-width: 812px) 
        and (-webkit-min-device-pixel-ratio: 3)
        and (orientation: landscape) { 
        /* STYLES */ 
        .ui-image-gallery {
            .gallery-con{
                height: 100%;
                .image-item #boxImage {
                overflow: scroll;
                height: 100%;
                width: -webkit-fill-available;
                .fill {
                    height: unset !important;
                    #docPage {
                        width: 100% !important;
                    }
                    img.imgResponsive {
                        -webkit-transform: translate(-50%, 0%);
                        -moz-transform: translate(-50%, 0%);
                        -ms-transform: translate(-50%, 0%);
                        -o-transform: translate(-50%, 0%);
                        transform: translate(-50%, 0%);
                    }
                }
            }}
            .footer-doc-toolbar .footer-doc-zoom .btn-zoom {
                display: none;
            }
            .annotation {
                height:200px !important;
                width: 300px !important;
                top: 24px;
                left: 45px;
            }
        }

    }
    @media only screen 
        and (min-width: 1024px) 
        and (max-width: 1024px) 
        and (orientation: landscape) 
        and (-webkit-min-device-pixel-ratio: 2) {
        /* STYLES GO HERE */
        .ui-image-gallery {
            .docuLayout {
                top: 51.8px;
                left: 412.4px;
            }
            .annotation {
                top: 57.5333px;
                left: 50.8666px;
            }
            .gallery-con {
                width: 100%;
                height: 100%;
                .image-item #boxImage {
                    // width: fit-content;
                     #docPage {
                        // width: auto !important;
                    }
                    .footer-doc-toolbar{
                        .footer-iconleft span .ui-button { 
                            height: unset !important;
                            margin-left: 4px !important; }
                        .footer-doc-pagenate { right:126px !important; }
                    }
                }
            }
        }
    }

    @media (min-width: 768px) and (max-width: 1024px)and (orientation: portrait) { //portrait
        #boxImage{
            width: 100% !important;
        }
        .custom-container{
            padding: 0 !important;
        }
    }
    @media (min-width: 768px) and (max-width: 1024px) and (orientation: landscape) {
        #boxImage{
            // width: 100% !important;
        }
    }
    @media (min-width: 320px) and (max-width: 480px) and (orientation:portrait){
        .custom-container{
            display:none !important;
        }
    }

    @media only screen 
        and (min-width: 1030px) 
        and (max-width: 1370px) 
        and (orientation: landscape) 
        and (-webkit-min-device-pixel-ratio: 2) {
        /* STYLES GO HERE */
        .ui-image-gallery {
            .docuLayout {
                top: 266.2px;
                left: 600.2px;
            }
            .annotation {
                top: 267.8px;
                left: 227.2px;
            }
            .gallery-con {
                width: 100%;
                height: 100%;
                .image-item #boxImage {
                    // width: fit-content;
                     #docPage {
                        width: auto !important;
                    }
                }
            }
        }
    }
    @media (max-width: 767px) {
        textarea.ui-annotation-textarea {
            height: 80%;
        }
    }
    @media (max-width: 735px) {
        textarea.ui-annotation-textarea {
            height: 90%;
        }
    }
    @media (max-width: 667px) {
        textarea.ui-annotation-textarea {
            height: 80%;
        }
    }
    @media (max-width: 638px) {
        textarea.ui-annotation-textarea {
            height: 90%;
        }
    }
    @media (max-width: 480px) {
        textarea.ui-annotation-textarea {
            height: 74%;
        }
    }

// For IE Browser ONLY!
@media all and (-ms-high-contrast: none), (-ms-high-contrast: active) {
    // Close Button
    .ui-close-button .ui-fab i {
        position: static !important;
    }

    .footer-doc-toolbar {
        margin-top: -150px;
    }
}
</style>
