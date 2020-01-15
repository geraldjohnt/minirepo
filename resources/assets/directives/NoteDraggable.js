export default {
  params: ['disabled'],
  bind() {
    let vm = this.vm;
    let el = this.el;
    var startX, startY, initialMouseX, initialMouseY

    const mousemove = (evt) => {
      let elm = el.parentNode.parentNode;
      if (!vm.canvasShow || el.className == 'textCanvas') {

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

    }
    const touchend = () => {
      document.removeEventListener('touchmove', touchmove)
      document.removeEventListener('touchend', touchend)
    }
    const mouseup = () => {
      document.removeEventListener('mousemove', mousemove)
      document.removeEventListener('mouseup', mouseup)
    }
    const touchmove = (evt) => {

      let elm = el.parentNode.parentNode;
      if (!vm.canvasShow && evt.touches.length <= 1 ||
        elm.className == 'textCanvas' ||
        vm.isOnMobileDrag && elm.className == 'toolbarDragMin' ||
        vm.isOnMobileDrag && elm.className == 'toolbarDragMax') {
        // console.log("drag TOUCH MOVE : ",vm.canvasShow)
        let dx = (evt.changedTouches ? evt.changedTouches[0].clientX : evt.clientX) - initialMouseX
        let dy = (evt.changedTouches ? evt.changedTouches[0].clientY : evt.clientY) - initialMouseY
        let limitx = window.innerWidth - el.offsetWidth
        let limity = window.innerHeight - el.offsetHeight
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
    }
    el.addEventListener('mousedown', (evt) => {

      // let textArea = document.querySelector('.'+this.el.className).querySelector('.tinymce-textarea');
      // if(document.activeElement.id === textArea.id)  return false;

      let elm  = el.parentNode.parentNode;
      startX = elm.offsetLeft
      startY = elm.offsetTop
      initialMouseX = evt.clientX
      initialMouseY = evt.clientY
      if(!this.params.disabled) {
        document.addEventListener('mousemove', mousemove)
        document.addEventListener('mouseup', mouseup)
      }
      return false
    })
    el.addEventListener('touchstart', (evt) => {
      let elm  = el.parentNode.parentNode;

      startX = elm.offsetLeft
      startY = elm.offsetTop
      initialMouseX = evt.changedTouches[0].clientX
      initialMouseY = evt.changedTouches[0].clientY
      if(!this.params.disabled) {
        // console.log("DISABLED : ",!this.params.disabled," --> ",this.params.disabled)
        document.addEventListener('touchmove', touchmove)
        document.addEventListener('touchend', touchend)
      }
      return false
    })
  },
  unbind() {
  }
}