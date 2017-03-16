<style>
    .btn-upload input[type="file"] {
        cursor: pointer;
        margin: 0;
        opacity: 0;
        padding: 0;
        position: absolute;
        right: 0;
        top: 0;
    }

    input[type="file"] {
        display: block;
    }

    .form-control {
        display: block;
        width: 100%;
        height: 36px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.6;
        color: #555555;
        background-color: #fff;
        background-image: none;
        border: 1px solid #ccd0d2;
        border-radius: 4px;
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        -webkit-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
        transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
    }
</style>
<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default" v-if="user">
                    <div class="panel-heading">Upload Photo</div>

                    <div class="panel-body">
                        <form class="form-horizontal" role="form">
                            <!-- Update Button -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">&nbsp;</label>

                                <div class="col-md-6 text-center">
                                    <label type="button" class="btn btn-primary btn-upload">
                                        <span>Select New Photo</span>

                                        <input ref="photo" type="file" class="form-control" name="photo" @change="update">
                                    </label>
                                </div>

                                <label class="col-md-3 control-label">&nbsp;</label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8 col-md-offset-2">
                <div class="row">
                    <div class="col-xs-6 col-md-3" v-for="photo in photos">
                        <a href="#" class="thumbnail">
                            <img :src="photo.photo_url" alt="...">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['user'],
        data() {
            return {
                photos: []
            }
        },
        mounted() {
            this.getPhotos();
            this.listen();
        },
        methods: {
            update(e) {
                e.preventDefault();

                // We need to gather a fresh FormData instance with the profile photo appended to
                // the data so we can POST it up to the server. This will allow us to do async
                // uploads of the profile photos. We will update the user after this action.
                axios.post('/api/upload/photo?api_token=' + this.user.api_token, this.gatherFormData())
                    .then(response => this.photos.unshift(response.data));
            },
            /**
             * Gather the form data for the photo upload.
             */
            gatherFormData() {
                const data = new FormData();

                data.append('photo', this.$refs.photo.files[0]);

                return data;
            },
            getPhotos() {
                axios.get('/api/photos').then(response => this.photos = response.data);
            },
            listen() {
                Echo.private('photos')
                    .listen('NewPhoto', (e) => this.photos.unshift(e.photo));
            }
        },
    }
</script>
