<template>
    <div class="ui-resizeable" >
        <ui-fab type="mini" icon="crop_free" @mousedown="resize" v-el:resizeable></ui-fab>
    </div>
</template>

<script>

import helper from '../../js/helpers.js';
import Cookie from 'js-cookie';

export default {
    name: 'ui-resizeable',

    data() {
        return {
            startX: 0,
            startY: 0,
            startWidth: 0,
            startHeight: 0
        }
    },

    methods: {
        resize(evt) {
            try{
                this.startX = evt.clientX
                this.startY = evt.clientY
                this.startWidth = this.$el.offsetParent.offsetWidth
                this.startHeight = this.$el.offsetParent.offsetHeight
                document.documentElement.addEventListener('mousemove', this.doDrag, false)
                document.documentElement.addEventListener('mouseup', this.stopDrag, false)
                this.$dispatch('resized', this.$el.offsetParent)
            } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Resizing notes'}
            let functionName = 'UiResizable:resize';
            helper.catchError(errorStats, functionName);
            }
        },
        doDrag(evt) {
            try{
                if(this.$el.offsetParent.offsetWidth > 224){
                    var initialPositionL = this.$el.offsetParent.offsetLeft;
                    var initialPositionT = this.$el.offsetParent.offsetTop;

                    var queryClass = this.$el.offsetParent.className.split(' ')
                    var classList = queryClass.toString();

                    var newWidth = this.startWidth + (evt.clientX - this.startX);
                    var newHeight = this.startHeight + (evt.clientY - this.startY);

                    if (classList == "meetingNotes"){
                    this.$parent.notesTopPosition = initialPositionT;
                    this.$parent.notesLeftPosition = initialPositionL;
                    this.$parent.meetingNotesWidth = newWidth;
                    this.$parent.meetingNotesHeight = newHeight;
                    }else if(classList == "meetingSales"){
                    this.$parent.salesTopPosition = initialPositionT;
                    this.$parent.salesLeftPosition = initialPositionL; 
                    this.$parent.meetingSalesWidth = newWidth;
                    this.$parent.meetingSalesHeight = newHeight;
                    }else if(classList == "subtitle"){
                    this.$parent.subtitleTopPosition = initialPositionT;
                    this.$parent.subtitleLeftPosition = initialPositionL; 
                    this.$parent.noteSubtitleWidth = newWidth;
                    this.$parent.noteSubtitleHeight = newHeight;
                    }else{
                        console.log('no classlist')
                    }
                }
                } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Resize dragging notes'}
                let functionName = 'UiResizable:doDrag';
                helper.catchError(errorStats, functionName);
            }
            //else if (evt.clientX <= 224 || evt.clientY <=199) {
                //this.$el.offsetParent.style.width = '225px';
                 //this.$el.offsetParent.style.height = '200px';
                //this.$parent.meetingNotesWidth = '225px';
                //this.$parent.meetingNotesHeight = '350px';
            //}
        },
        stopDrag(evt) {
            try{
                document.documentElement.removeEventListener('mousemove', this.doDrag, false)
                document.documentElement.removeEventListener('mouseup', this.stopDrag, false)
                this.$dispatch('stop-resize', this.$el.offsetParent)
                } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Resize stop dragging notes'}
                let functionName = 'UiResizable:stopDrag';
                helper.catchError(errorStats, functionName);
            }
        },
        touchResize(evt){
            try{
                this.startX = evt.changedTouches[0].clientX
                this.startY = evt.changedTouches[0].clientY
                this.startWidth = this.$el.offsetParent.offsetWidth
                this.startHeight = this.$el.offsetParent.offsetHeight
                document.documentElement.addEventListener('touchmove', this.touchActive, false)
                document.documentElement.addEventListener('touchend', this.touchInactive, false)
                this.$dispatch('resized', this.$el.offsetParent)
                } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Touch resizing notes'}
                let functionName = 'UiResizable:touchResize';
                helper.catchError(errorStats, functionName);
            }
        },
        touchActive(evt) {

            try{
            if(this.$el.offsetParent.offsetWidth > 224) {

                var queryClass = this.$el.offsetParent.className.split(' ')
                var touchClassList = queryClass.toString();

                var initialPositionL = this.$el.offsetParent.offsetLeft;
                var initialPositionT = this.$el.offsetParent.offsetTop;

                var touchnewWidth = (this.startWidth + evt.changedTouches[0].clientX- this.startX);
                var touchnewHeight = (this.startHeight + evt.changedTouches[0].clientY - this.startY);

                if (touchClassList == "meetingNotes,forceOnTop" || touchClassList == "meetingNotes"){
                this.$parent.notesTopPosition = initialPositionT;
                this.$parent.notesLeftPosition = initialPositionL;
                this.$parent.meetingNotesWidth = touchnewWidth;
                this.$parent.meetingNotesHeight = touchnewHeight;

                }else if(touchClassList == "meetingSales,forceOnTop" || touchClassList == "meetingSales"){
                this.$parent.salesTopPosition = initialPositionT;
                this.$parent.salesLeftPosition = initialPositionL;
                this.$parent.meetingSalesWidth = touchnewWidth;
                this.$parent.meetingSalesHeight = touchnewHeight;

                }else if(touchClassList == "subtitle,forceOnTop" || touchClassList == "subtitle"){
                this.$parent.subtitleTopPosition = initialPositionT;
                this.$parent.subtitleLeftPosition = initialPositionL;
                this.$parent.noteSubtitleWidth = touchnewWidth;
                this.$parent.noteSubtitleHeight = touchnewHeight;

                }else{
                    console.log('no classlist', touchClassList)
                }

            } //else if (this.$el.offsetParent.offsetWidth <= 224) {
              //  this.$el.offsetParent.style.width = '225px';
            //}
                } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Touch active notes'}
                let functionName = 'UiResizable:touchActive';
                helper.catchError(errorStats, functionName);
            }

        },
        touchInactive(evt) {
            try{
                console.log("touch Inactive : ",evt);
                document.documentElement.removeEventListener('touchmove', this.touchActive, false)
                document.documentElement.removeEventListener('touchend', this.touchInactive, false)
                this.$dispatch('stop-resize', this.$el.offsetParent)
                } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Touch active notes'}
                let functionName = 'UiResizable:touchActive';
                helper.catchError(errorStats, functionName);
            }
        }
    }
}
</script>

<style lang="sass">
.ui-resizeable {
    position: absolute;
    bottom: -103px;
    right: 0;
    .ui-fab {
        cursor: se-resize;
    }
}
</style>