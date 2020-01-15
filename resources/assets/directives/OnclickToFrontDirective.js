export default {	
	bind() {
		let el = this.el;
		let vm = this.vm;
		// if vm.$parent.$parent is undefine it is parent vm
		vm.boxEls = [
			(vm.$parent.$parent === undefined) ? vm.$els.vidShare   : vm.$parent.$els.vidShare ,
			(vm.$parent.$parent === undefined) ? vm.$els.noteShare  : vm.$parent.$els.noteShare,
			(vm.$parent.$parent === undefined) ? vm.$els.salesShare : vm.$parent.$els.salesShare ,
			(vm.$parent.$parent === undefined) ? vm.$els.subTitle   : vm.$parent.$els.subTitle ,
			(vm.$parent.$parent === undefined) ? "fileAnnotation" : vm.$els.fileAnnotation ,
			(vm.$parent.$parent === undefined) ? "documentLayout" : vm.$els.documentLayout ,
			(vm.$parent.$parent === undefined) ? "boxImage"  : vm.$els.boxImage ,
			

		];

		let name = '';
		let forceTopGet = '';
		let onTopGet = '';


		el.addEventListener('mousedown', (evt) => {

			if(document.querySelector('.onTop')){
				var onTop = document.querySelectorAll('.onTop');
				for(var p = 0; p < onTop.length; p++)
				{
				    onTop[p].classList.remove('onTop');
					for (let pp = 0; pp < vm.boxEls.length; pp++) {
						
						if (onTop[p] == vm.boxEls[pp]) {
							onTopGet = vm.boxEls[pp];
							vm.boxEls = vm.boxEls.filter(function(value){
							    return value != onTopGet;

							});
							vm.boxEls.unshift(onTopGet);
						}

					}	
				}
			}



			if(document.querySelector('.forceOnTop')){
				var forceOnTops = document.querySelectorAll('.forceOnTop');
				for(var i = 0; i < forceOnTops.length; i++)
				{
				    forceOnTops[i].classList.remove('forceOnTop');	
					for (let ii = 0; ii < vm.boxEls.length; ii++) {
						
						if (forceOnTops[i] == vm.boxEls[ii]) {
							forceTopGet = vm.boxEls[ii];
							vm.boxEls = vm.boxEls.filter(function(value){
							    return value != forceTopGet;

							});
							vm.boxEls.unshift(forceTopGet);
						}

					}
				}
			}
			
			for (let e = 0 ; e < vm.boxEls.length; e++) {
				// console.log(el)
				if (el == vm.boxEls[e]) {
					name = vm.boxEls[e];
				}	
			}

			
			let removearr = vm.boxEls.filter(function(value){

			    return value != name;

			});

			vm.boxEls = removearr;	

			vm.boxEls.unshift(name);

			let newgen = [];

			let ctr = vm.boxEls.length; 
			let meh = ctr;

			for( let x = 0; x < ctr; x++){
				   
				if (vm.$parent.$parent === undefined) {
					if (vm.boxEls[x] == "documentLayout") {
						vm.boxEls[x] = (vm.$refs.imageGallery.$els.documentLayout === undefined) ? "documentLayout" : vm.$refs.imageGallery.$els.documentLayout;
					}
					 if (vm.boxEls[x] == "boxImage") {
						vm.boxEls[x] = (vm.$refs.imageGallery.$els.boxImage === undefined) ? "boxImage" : vm.$refs.imageGallery.$els.boxImage;
					}
					 if (vm.boxEls[x] == "fileAnnotation") {
						vm.boxEls[x] = (vm.$refs.imageGallery.$els.fileAnnotation === undefined) ? "fileAnnotation" : vm.$refs.imageGallery.$els.fileAnnotation;
					}

				}

				let ob1 = {
				   'zindex': 40 + meh,
				   'content': vm.boxEls[x]
				}

				newgen.push(ob1);
				meh --;
			}
			// console.log(newgen)
			for (let op = 0 ; op < newgen.length; op++) {
				if (typeof newgen[op].content === 'object' ) {
					newgen[op].content.style.zIndex = newgen[op].zindex ;
				}
			}

			return false
			
		})
	}
}