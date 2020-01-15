<template>
	<div 
		class="ui-sidebar"
		:class="{ 'ui-sidebar-right': positionRight, 'ui-sidebar-left': !positionRight }"
        @keyup.esc="close" tabindex="0"
    >
    	<div class="ui-sidebar-content">
            <div class="sideContent">
                <slot name="top-con"></slot>
                <!-- <div class="menuContent"> -->
                <slot name="bottom-con"></slot>
               <!--  </div> -->
               <div class="footer">
                   <span class="footer-copyright">&#169; 2017 Mee2box. All right Reserved.</span>
               </div>
                
            </div>
        </div>

        <ui-backdrop class="ui-sidebar-backdrop" @close="close"></ui-backdrop>
    </div>
</template>

<script>
import UiBackdrop from './UiBackdrop.vue';

export default {
	name: 'ui-sidebar',

    data() {
        return {
            fullWidth: document.documentElement.clientWidth
        }
    },

	props: {
        visible: {
            type: Boolean,
            default: true
        },
		positionRight: {
			type: Boolean,
			default: false
		}
	},

	events: {
		'sidebar-opened': function() {
            if(this.hasAdjustableCon()) {
    			this.$parent.$parent.$els.adjContainer.classList.add('openedSidebar')
                this.$parent.$parent.$els.adjContainer.classList.add(!this.positionRight ? 'left' : 'right')
            }
		},
		'sidebar-closed': function() {
            if(this.hasAdjustableCon()) {
                this.$parent.$parent.$els.adjContainer.classList.remove('openedSidebar')
            }
		}
	},

	methods: {
        show() {
            this.open();
        },
        open() {
            this.visible = true;
            if(this.$el) {
                this.$el.classList.add('opened')
            }
            this.$dispatch('sidebar-opened')
        },
        close() {
            this.visible = false;
            if(this.$el) {
                this.$el.classList.remove('opened')
            }
            this.$dispatch('sidebar-closed');
        },
		toggle() {
            if(this.visible) {
                this.close()
            } else {
                this.open()
            }
		},
        handleResize() {
            this.fullWidth = document.documentElement.clientWidth
            if(this.fullWidth <= 1024 && this.visible) {
                this.close()
            }
            if(this.fullWidth > 1024 && !this.visible) {
                this.open()
            }
            this.$dispatch('handle-resize', this.fullWidth)
        },
        hasAdjustableCon() {
            return this.$parent.$parent.$els.adjContainer
        }
	},

	ready() {
        window.addEventListener('resize', this.handleResize)
        if(this.visible) {
            this.open()
            this.handleResize()
        }
	},

    beforeDestory() {
        window.removeEventListener('resize', this.handleResize)
    },

    components: {
        UiBackdrop
    }
}
</script>

<style lang="sass">
.ui-sidebar {
    .ui-sidebar-content {
        background-color: #f2f2f2;
        width: 260px;
        position: absolute;
        top: 0;
        bottom: 0;
        z-index: 100;
        overflow: auto;
        pointer-events: none;
        -webkit-overflow-scrolling: touch;
        transition: all .4s cubic-bezier(.25,.8,.25,1);
        transition-property: transform;
        will-change: transform;

        .footer-copyright {
            color: #3B5998;
            bottom: 8px;
            left: 20px;
            font-weight: bold;
            font-size: 10px;
        }
    }
    &.ui-sidebar-left .ui-sidebar-content{
        left: 0;
        transform: translate3D(-100%, 0, 0);
    }
    &.ui-sidebar-right .ui-sidebar-content{
        right: 0;
        transform: translate3D(100%, 0, 0);
        .footer-copyright {
            display:none;
        }
    }
    &.opened .ui-sidebar-content{ 
        transform: translate3D(0,0,0);
        pointer-events: auto;
        box-shadow: 0 1px 5px rgba(0,0,0,.2), 0 2px 2px rgba(0,0,0,.14), 0 3px 1px -2px rgba(0,0,0,.12);
    }
    &:not(.opened).ui-sidebar-left .ui-sidebar-content{
        @media (max-width:1024px) {
            transform: translate3D(-100%,0,0);
        }
    }
    &:not(.opened).ui-sidebar-right .ui-sidebar-content{
        @media (max-width:1024px) {
            transform: translate3D(100%,0,0);
        }
    }
    .ui-sidebar-backdrop {
        position: fixed;
    }
    &.opened {
        .ui-sidebar-backdrop {
            @media (max-width:1024px) {
                opacity: 1;
                pointer-events: auto;
            }
        }
    }
}
.openedSidebar {
    &.left {
        padding-left: 260px !important;
    }
    &.right {
        padding-right: 260px !important;
    }
    @media (max-width:1024px) {
        &.left {
            padding-left: 0px !important;
        }
        &.right {
            padding-right: 0px !important;
        }
    }
}
</style>