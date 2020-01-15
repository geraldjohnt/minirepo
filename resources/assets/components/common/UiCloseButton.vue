<template>
    <div class="ui-close-button">
        <ui-fab type="mini" color="primary" icon="close" @click="close" v-el:close-button>
            非表示
        </ui-fab>
    </div>
</template>

<script>
    export default {
        name: 'ui-close-button',

        data() {
            return {}
        },

        methods: {
            close(event) {
                // console.log(event, 'event close');
            }
        }
    }
</script>

<style lang="scss" scoped>
    .ui-close-button {
        position: absolute;
        right: -19px;
        top: -19px;
    }

</style>