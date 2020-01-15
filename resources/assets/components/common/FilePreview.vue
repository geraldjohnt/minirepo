<template>
    <div class="file-preview">
        <input type="file" :id="id" :name="name" @change="changed" />
        <img v-bind:src="image" v-if="!hideImagePreview">
    </div>
</template>

<script>
export default {
    data() {
        return {
            image: '',
            file: '',
            value: ''
        }
    },
    name: 'file-preview',
    props: {
        hideImagePreview: {
            type: Boolean,
            default: false
        },
        readFile: {
            type: Boolean,
            default: true
        }
    },
    methods: {
        changed(event) {
            var input = event.target
            if (input.files && input.files[0] && this.readFile) {
                var reader = new FileReader()
                var vm = this
                
                reader.onload = (e) => {
                    vm.image = e.target.result
                }

                reader.readAsDataURL(input.files[0])
                this.value = input.files[0]
            } else {
                this.value = input.files[0];
            }
            this.$dispatch('changed');
        }
    }
}
</script>

<style lang="sass">
.file-preview {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    img {
        width: inherit;
        height: inherit;
    }
    input[type="file"] {
        width: 100%;
        height: 100%;
        opacity: 0;
        top: 0;
        overflow: hidden;
        position: absolute;
        z-index: 15;
        cursor: pointer;
    }
}
</style>