<template>
    
    <div class="button1">
        <file-upload
            ref="upload"
            v-model="files"
            post-action="/api/rhythmExerciseBar/import/musicXML"
            @input-file="inputFile"
            @input-filter="inputFilter"
        >
            {{text}} {{getFirstFileProgress}}
        </file-upload>
    </div>

</template>

<style lang="scss" scoped>

</style>

<script>

let VueUploadComponent = require('vue-upload-component');

export default {
    
    data() {
        return {
            files: []
        }
    },

    props: ['text', 'onFileUploaded', 'onResponse'],

    components: {
        FileUpload: VueUploadComponent
    },

    computed: {

        getFirstFileProgress() {
            if(this.files == null || this.files.length == 0) return "";
            return this.files[0].progress + "%";

        }

    },

    methods: {

        /**
        * Has changed
        * @param  Object|undefined   newFile   Read only
        * @param  Object|undefined   oldFile   Read only
        * @return undefined
        */
        inputFile: function (newFile, oldFile) {

            if(!oldFile || !oldFile.active){
                newFile.active = true;
            }

            if (newFile && oldFile && !newFile.active && oldFile.active) {
                // Get response data
                console.log('response', newFile.response)
                if (newFile.xhr) {
                    //  Get the response status code
                    console.log('status', newFile.xhr.status);
                    this.onFileUploaded(newFile.response);
                }
            }
        },

        /**
        * Pretreatment
        * @param  Object|undefined   newFile   Read and write
        * @param  Object|undefined   oldFile   Read only
        * @param  Function           prevent   Prevent changing
        * @return undefined
        */
        inputFilter: function (newFile, oldFile, prevent) {
            if (newFile && !oldFile) {
                if (!/\.(musicxml|json)$/i.test(newFile.name)) {
                    return prevent()
                }
            }

            // Create a blob field
            newFile.blob = ''
            let URL = window.URL || window.webkitURL
            if (URL && URL.createObjectURL) {
                newFile.blob = URL.createObjectURL(newFile.file)
            }
        }

    }

}
</script>


