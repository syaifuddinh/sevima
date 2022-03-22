<template>
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2">
            <div class="panel panel-default" v-for="(post, index) in localPosts">
                <div class="panel-heading">
                    <small class="pull-right">{{ post.published }}</small>
                    <h3 class="panel-title">{{ post.user.name }}</h3>
                </div>
                <div
                    v-if="post.image"
                    class="panel-body"
                    style="display:flex;justify-content:center"
                >
                    <img
                        :src="post.imageUrl"
                        style="width:30%;height:auto"
                    >
                </div>
                <div class="panel-body">
                    {{ post.content }}
                </div>
                <div class="panel-footer">
                    <like :post="post" :current_user_id="current_user_id"></like>
                    <div 
                        v-if="post.comment"
                        v-for="item in post.comment"
                        :key="item.id"
                        style="margin-top:10px;border:1px solid grey;border-radius:8px;padding:10px"
                    >
                        {{ item.content }}
                        <div style="margin-top:10px">
                            <i style="font-size:9px">
                                {{ item.created_at }}
                            </i>
                        </div>
                    </div>
                    <div style="margin-top:10px">
                        <textarea
                            placeholder="Komentar...."
                            style="width:100%"
                            rows="2"
                            :key="index"
                            @keyup="commentOnChange($event, post.id)"
                            v-model="localPosts[index].commentValue"
                        >
    
                        </textarea>

                        <button
                            type="button"
                            :key="counter"
                            :disabled="!localPosts[index].comment || localPosts[index].disabled"
                            class="btn btn-primary btn-sm"
                            style="margin-top:10px"
                            @click="storeComment(post.id)"
                        >
                            Add Comment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    let baseURL = document.head.querySelector('meta[name="base-url"]').content;
    export default {
        mounted() {
            this.getFeed()
        },
        props: ['current_user_id'],
        components: ['like'],
        data() {
            return {
                localPosts: [],
                content: "",
                counter: 0
            }
        },
        methods: {
            storeComment(id) {
                let localPosts = this.localPosts;
                let postIndex = localPosts.findIndex(param => param.id === id);
                let formData = {};
                if(postIndex > -1) {
                    localPosts[postIndex].disabled = true;
                    this.counter += 1;
                    formData.content = localPosts[postIndex].commentValue;
                    axios.post(
                        baseURL + '/api/posts/' + id + '/comment',
                        formData
                    )
                    .then((response) => {
                        this.counter += 1;
                        this.getFeed();
                    })
                }
            },
            commentOnChange(e, id) {
                let localPosts = this.localPosts;
                let postIndex = localPosts.findIndex(param => param.id === id);
                if(postIndex > -1) {
                    localPosts[postIndex].commentValue = e.target.value;
                    this.counter += 1;
                }
            },
            getLocalPosts() {
                let localPosts = this.$store.getters.allPosts;
                localPosts = localPosts.map(param => {
                    let response = param;
                    response.commentValue = "";
                    response.disabled = false;

                    return response;
                });
                this.localPosts = localPosts;
                window.console.log(this.localPosts);
            },
            getFeed() {
                axios.get(baseURL + '/api/posts')
                    .then((response) => {
                        this.$store.commit('pushPosts', response.data)
                        this.getLocalPosts();
                    })
            }
        },
        computed: {
            posts() {
                return this.$store.getters.allPosts
            }
        }
    }
</script>