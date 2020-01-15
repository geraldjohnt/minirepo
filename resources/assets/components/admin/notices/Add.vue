<template>
    <div class="protoDashboard">
        <!-- <admin-sidebar v-ref:sidebar></admin-sidebar> -->
<staff-sidebar v-ref:sidebar ></staff-sidebar>
        <div class="dashboardContent dashAddContent" v-el:adj-container>
            <div class="wrapper">

                <ui-alert @dismissed="error = false" type="error" :show="error">
                    {{ error_message }}
                </ui-alert>

                <div class="row">
                    <div class="col-xs-12 col-sm-8">
                        <h1>Add Notice</h1>
                        <form autocomplete="off" @submit.prevent="addNotice">
                            <div class="form-group">
                                <ui-textbox
                                    label="Title" name="title" id="title" type="text" placeholder="Enter notice title" :value.sync="notice.title" 
                                    validation-rules="required" v-ref:input-title
                                ></ui-textbox>
                            </div>
                            <div class="form-group">
                                <ui-textbox
                                    label="Content" name="content" id="content" type="text" placeholder="Enter notice content" :value.sync="notice.content" :multi-line="true" :max-length="1024" validation-rules="required|max:1024" help-text="Max 1024 characters" v-ref:input-content
                                ></ui-textbox>
                            </div>
                            <div class="form-group">
                                <div class="buttonCon">
                                    <ui-button type="normal" button-type="normal" color="primary" icon="file_upload" class="protoButton buttonCon" v-ref:upload-document :loading="loading.cover_upload" text="Upload Cover Photo"></ui-button>
                                    <file-preview name="cover" id="cover" @changed="changeDocument" v-ref:upload-file hide-image-preview></file-preview>
                                </div>
                            </div>
                            <div class="form-group">
                                <ui-switch name="broadcast" :value.sync="notice.broadcast">全ユーザーに向けて表示する</ui-switch>
                            </div>
                            <div class="ui-button-group">
                                <ui-button type="normal" button-type="normal" v-link="{ name: 'manage-notices' }" color="default" icon="keyboard_arrow_left">戻る</ui-button>
                                <ui-button type="normal" button-type="submit" color="success">Add Notice</ui-button>
                            </div>
                        </form>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
// import AdminSidebar from '../partials/AdminSidebar.vue';
import StaffSidebar from '../../staff/partials/StaffSidebar.vue';
import FilePreview from '../../common/FilePreview.vue';
import {APP_URL} from '../../../js/config.js';
import admin from '../../../js/admin.js';

export default {
    data() 
    {
        return {
            auth: this.$parent.auth,
            APP_URL,
            static_images: {
                avatar: `${APP_URL}/image/avatar_flat.png`
            },
            notice: admin.data.notice,
            cover: '',
            error: false,
            error_message: null,
            loading: {
                cover_upload: false
            }
        }
    },
    methods: {
        addNotice() {
            let data = new FormData();
            for ( var key in this.notice ) {
                let value  
                    = (key === 'broadcast') 
                    ? (this.notice[key] == true ? 1:0) 
                    : this.notice[key]

                data.append(key, value)
            }
            data.append('cover', this.cover)

            admin.addNotice(this, data)
        },
        toggleValidation() {
            this.$refs.inputTitle.blurred()
            this.$refs.inputContent.blurred()
        },
        changeDocument() {
            let filename = this.$refs.uploadFile.value.name
            this.cover = this.$refs.uploadFile.value
            filename = `${filename.replace(/.[^.]+$/,'').substring(0,10)}.${filename.replace(/^.*\./,'')}`
            this.$refs.uploadDocument.text = `FILE: ${filename}`
        }
    },
    ready() {
        let config = {
            role: 'admin',
            showToolbar: true
        }

        this.$dispatch('component-ready', config)
    },
    components: {
        // AdminSidebar,
        StaffSidebar,
        FilePreview
    }
}
</script>