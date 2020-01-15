<template>
    <div class="image-preview">
        <img v-bind:src="image">
        <input type="file" :id="id" :name="name" @change="changed" />
    </div>
</template>

<script>
export default {
    data() {
        return {
            image: '',
            value: ''
        }
    },
    name: 'image-preview',
    methods: {
        changed(event) {
            var input = event.target
            if (input.files && input.files[0]) {
                var reader = new FileReader()
                var vm = this
                
                reader.onload = (e) => {
                    vm.image = e.target.result
                }

                reader.readAsDataURL(input.files[0])
                this.value = input.files[0]
            }
            this.$dispatch('changed');
        }
    }
}
</script>

<style lang="sass">
.image-preview {
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
}
</style>