<template>
  <div class="card vue-avatar-cropper-demo text-center">
    <div class="card-body">
      <img :src="user.avatar" class="card-img avatar" />
      <div class="card-img-overlay">
        <button class="btn btn-primary btn-sm" id="pick-avatar">
          Select an new image {{ baseUrl }}
        </button>
      </div>
    </div>
    <div class="card-footer text-muted" v-html="message"></div>
    <avatar-cropper 
      @uploading="handleUploading"
      v-bind:labels="{
        submit: 'Save',
        cancel: 'Cancel'
      }"
      @uploaded="handleUploaded"
      @completed="handleCompleted" 
      @error="handlerError" 
      trigger="#pick-avatar" 
      v-bind:upload-url="baseUrl" />
  </div>
</template>

<script>
import AvatarCropper from "vue-avatar-cropper";

export default {
  components: { AvatarCropper },
  data() {
    return {
      message: "ready",
      user: {
        id: 1,
        nickname: "安正超",
        username: "overtrue",
        avatar: userAvatar
      }
    };
  },
  props: {
    baseUrl: {
      type: String
    },
  },
  methods: {
    handleUploading(form, xhr) {
      this.message = "uploading...";
    },
    handleUploaded(response) {
      if (response.status == "200") {
        this.user.avatar = response.url;
        // Maybe you need call vuex action to
        // update user avatar, for example:
        // this.$dispatch('updateUser', {avatar: response.url})
        this.message = "user avatar updated.";
      }
    },
    handleCompleted(response, form, xhr) {
      this.message = "upload completed.";
    },
    handlerError(message, type, xhr) {
      this.message = "Oops! Something went wrong...";
    },
    url: function() {
     
      return this.baseUrl;
    }
  }
};
</script>

<style>
.vue-avatar-cropper-demo {
  max-width: 18em;
  margin: 0 auto;
}
.avatar {
  width: 160px;
  border-radius: 6px;
  display: block;
  margin: 20px auto;
}
.card-img-overlay {
  display: none;
  transition: all 0.5s;
}
.card-img-overlay button {
  margin-top: 20vh;
}
.card:hover .card-img-overlay {
  display: block;
}
</style>
