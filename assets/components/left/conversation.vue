<template lang="">
<router-link :to="{ name: 'conversation', params: {id: conversation.conversationId }}"  class="list-group-item list-group-item-action rounded-0">
    <div class="media rounded-circle"><img src="https://www.pngfind.com/pngs/m/470-4703547_icon-user-icon-hd-png-download.png" alt="user" width="50">
        <div class="media-body ml-4">
        <div class="d-flex align-items-center justify-content-between mb-1">
            <h6 class="mb-0">{{ conversation.username }}</h6><small class="small font-weight-bold">{{ date }}</small>
        </div>
            <p class="font-italic mb-0 text-small">{{ conversation.content }}</p>
        </div>
    </div>
</router-link>
</template>
<script>
export default {
    props:{
        conversation: Object
    },
    computed: {
        date() {
            return new Date(this.conversation.createdAt).toLocaleTimeString();
        }
    }
};
</script>
<style lang="">
</style>