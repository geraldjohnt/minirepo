import helper from '../js/helpers.js';
import Cookie from 'js-cookie';

export default {
  params: ['disabled'],
  bind() {
    let vm = this.vm;
    let el = this.el ;
    var startX, startY, initialMouseX, initialMouseY;
    // el.style.position = 'absolute'

    let notesClass = [
      'meeting-note-head',
      'meeting-note-footer',
      'meeting-sales-head',
      'meeting-sales-footer',
      'ui-close-button',
    ];

    const mousemove = (evt) => {
      try {
        let elm = notesClass.indexOf(el.className) !== -1 ? el.parentNode.parentNode : el;

        if (!vm.canvasShow || elm.className == 'textCanvas') {
          // console.log("MOUSE MOV : ",vm.canvasShow)
          let dx = evt.clientX - initialMouseX
          let dy = evt.clientY - initialMouseY
          let limitx = window.innerWidth - elm.offsetWidth
          let limity = window.innerHeight - elm.offsetHeight
          let mixY = startY + dy
          let mixX = startX + dx
          if (mixY <= limity) {
            elm.style.top = mixY > 0 ? `${mixY}px` : '0px'
          } else {
            elm.style.top = limity
          }
          if (mixX <= limitx) {
            elm.style.left = mixX > 0 ? `${mixX}px` : '0px'
          } else {
            elm.style.left = limitx
          }
        }
        return false

      } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Draggable mouse move'}
        let functionName = 'DraggableDirectives:mousemove';
        helper.catchError(errorStats, functionName);
      }
    }
    const touchend = () => {
      try{
        document.removeEventListener('touchmove', touchmove)
        document.removeEventListener('touchend', touchend)
      } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Draggable touchend'}
        let functionName = 'DraggableDirectives:touchend';
        helper.catchError(errorStats, functionName);
      }
    }
    const mouseup = () => {
      try{
        document.removeEventListener('mousemove', mousemove)
        document.removeEventListener('mouseup', mouseup)
      } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Draggable mouse up'}
        let functionName = 'DraggableDirectives:mouseup';
        helper.catchError(errorStats, functionName);
      }
    }
    const touchmove = (evt) => {
      try{
        let elm = notesClass.indexOf(el.className) !== -1 ? el.parentNode.parentNode : el;

        if (!vm.canvasShow && evt.touches.length <= 1 ||
          elm.className == 'textCanvas' ||
          vm.isOnMobileDrag && elm.className == 'toolbarDragMin' ||
          vm.isOnMobileDrag && elm.className == 'toolbarDragMax') {
          // console.log("drag TOUCH MOVE : ",vm.canvasShow)
          let dx = (evt.changedTouches ? evt.changedTouches[0].clientX : evt.clientX) - initialMouseX
          let dy = (evt.changedTouches ? evt.changedTouches[0].clientY : evt.clientY) - initialMouseY
          let limitx = window.innerWidth - elm.offsetWidth
          let limity = window.innerHeight - elm.offsetHeight
          let mixY = startY + dy
          let mixX = startX + dx
          if (mixY <= limity) {
            elm.style.top = mixY > 0 ? `${mixY}px` : '0px'
          } else {
            elm.style.top = limity
          }
          if (mixX <= limitx) {
            elm.style.left = mixX > 0 ? `${mixX}px` : '0px'
          } else {
            elm.style.left = limitx
          }
        }
        return false
      } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Draggable touch move'}
        let functionName = 'DraggableDirectives:touchmove';
        helper.catchError(errorStats, functionName);
      }
    }
    el.addEventListener('mousedown', (evt) => {
        try{
        let elm = notesClass.indexOf(el.className) !== -1 ? el.parentNode.parentNode : el;

        startX = elm.offsetLeft
        startY = elm.offsetTop
        initialMouseX = evt.clientX
        initialMouseY = evt.clientY
        if(!this.params.disabled) {
          document.addEventListener('mousemove', mousemove)
          document.addEventListener('mouseup', mouseup)
        }
        return false
      } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Draggable event listener mouse down'}
        let functionName = 'DraggableDirectives:addEventListener:mousedown';
        helper.catchError(errorStats, functionName);
      }
    })
    el.addEventListener('touchstart', (evt) => {
        try{
        let elm = notesClass.indexOf(el.className) !== -1 ? el.parentNode.parentNode : el;

        startX = elm.offsetLeft
        startY = elm.offsetTop
        initialMouseX = evt.changedTouches[0].clientX
        initialMouseY = evt.changedTouches[0].clientY
        if(!this.params.disabled) {

          document.addEventListener('touchmove', touchmove)
          document.addEventListener('touchend', touchend)
        }
        return false
      } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Draggable event listener touch start'}
        let functionName = 'DraggableDirectives:addEventListener:touchstart';
        helper.catchError(errorStats, functionName);
      }
    })
  },
  unbind() {
  }
}