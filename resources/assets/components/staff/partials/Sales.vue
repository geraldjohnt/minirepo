<template>
	<div class="sales" style="background-color: white;height: 100%;width: 100%">
        		<ui-textbox
				id="textbox" :value.sync="memo"
				name="textb"
                 :multi-line="true" class="custom_txtbox"
                placeholder="商談ページに表示するメモを記入してください。" 
                style="height:100%; width:100%;background-color: none" @blurred="updateBox()"
        	>
        	</ui-textbox>
	</div>
</template>
<script>
	import isMobile from '../../../js/isMobileDetect.js';
	import {tinymceConf} from "../../../js/config";
	import helper from '../../../js/helpers.js';
	export default{
		name: 'sales',
		props: 
		{
			memo: ''
        },
		data() {
			return {
				auth: this.$parent.auth,
			}
		},
		methods: {
			active(){
				document.getElementById("textbox").classList.add('active');
			},
			inactive(){
				document.getElementById("textbox").classList.remove('active');
				this.memo = document.getElementById("textbox").innerHTML;
				this.updateBox();
			},
			initTinyMCE(){
				try {
					let cond = ((isMobile.phone || isMobile.tablet) && isMobile.apple.device);
					let tinymceConfig = cond ? tinymceConf.mobile : tinymceConf.desktop;
					
					tinymceConfig.selector = '#textbox';
					if(cond) tinymceConfig.fixed_toolbar_container += "-textbox";
					tinymce.init(tinymceConfig).then(function(e){});
					
				}catch(e) {
					let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action'}
					let functionName = 'Sales:initTinyMCE';
					helper.checkError(errorStats, functionName);
				}
			},
			updateBox(){
				this.$emit('updatelist');
			}
		},
		computed: {

		},
		created(){
		}
	}
</script>
