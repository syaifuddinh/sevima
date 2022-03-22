<template>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div
                        style="margin-bottom:5px;display:flex;justify-content:center"
                        v-if="image"
                    >
                        <img
                            :src="image"
                            style="width:30%;height:auto"
                        >
                    </div>
                    <textarea rows="4" class="form-control" v-model="content"></textarea>
                    <br>
                    <button
                        v-if="image"
                        class="btn btn-danger pull-right"
                        style="display: inline-block;margin-left:5px"
                        @click="remove_photo"
                    >
                        Remove Photo
                    </button>
                    <label
                        class="btn btn-primary pull-right"
                        for="upload"
                        style="display: inline-block;margin-left:5px"
                    >
                        Upload Photo
                    </label>
                    <input
                        type="file"
                        id="upload"
                        style="display:none"
                        accept="image/*"
                        @change="upload_photo"
                    >
                    <button class="btn btn-success pull-right mr-5px" :disabled="not_working" @click="create_post">Create post</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    let baseURL = document.head.querySelector('meta[name="base-url"]').content;
    export default {
        mounted () {

        },
        data () {
            return {
                content: '',
                image: '',
                imageFile: null,
                not_working: true
            }
        },
        methods: {
            upload_photo(e) {
                 const file = e.target.files[0];
                 let src = URL.createObjectURL(file);
                 this.image = src;
                 this.imageFile = file;
            },
            remove_photo() {
                this.image = "";
                this.imageFile = null;
            },
            create_post() {
                const formData = new FormData();
                formData.append("content", this.content);
                if(this.imageFile) {
                    formData.append("image", this.imageFile);
                }
                axios.post(
                    baseURL + '/api/posts', 
                    formData
                )
                    .then((response) => {
                        this.content = '';
                        this.image = '';
                        this.imageFile = null;
                        noty({
                            type: 'success',
                            layout: 'bottomLeft',
                            text: 'Your post has been published.',
                            timeout: 3000
                        })
                        let newPost = response.data;
                        this.$store.commit('addPost', newPost)
                    })
            }
        },
        watch: {
            content() {
                if (this.content.length > 0)
                    this.not_working = false
                else
                    this.not_working = true
            }
        }
    }
</script>