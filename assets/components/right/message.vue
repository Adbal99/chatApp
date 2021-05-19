<template lang="">
         <!-- Sender Message-->
        <div class="media w-50 mb-3" :class="{'ml-auto': message.mine}" v-if="message.mine || message.user.username !== USERNAME" >
          <img v-if="!message.mine" src="https://www.pngfind.com/pngs/m/470-4703547_icon-user-icon-hd-png-download.png" alt="user" width="50" class="rounded-circle">
          <div class="media-body ml-3">
            <div class=" rounded py-2 px-3 mb-2" :class="[message.mine ? 'bg-primary' : 'bg-light']">
              <p class="text-small mb-0 " :class="[message.mine ? 'text-white' : 'text-muted']" >{{ message.content }}</p>
            </div>
            <p class="small text-muted">{{ date }}</p>
          </div>
        </div>
</template>
<script>
import { mapGetters } from "vuex";
export default {
  props: {
    message: Object,
  },
  computed: {
    ...mapGetters(["USERNAME"]),
    date() {
      return new Date(this.message.createdAt).toLocaleString();
    },
  },
};
</script>
<style lang="">
</style>