<template lang="">
    <div class="col-7 px-0">
      <div class="px-4 py-5 chat-box bg-white" ref="messagesBody">
      <!-- message -->
      <template v-for="(message, index) in MESSAGES">

        <message :message="message"></message>
      </template>
      </div>

      <!-- Typing area -->
      <messageInput></messageInput>

    </div>
</template>
<script>
import message from "./message";
import messageInput from "./messageInput";
export default {
  components: { message, messageInput },
  methods: {
    scrollDown() {
      this.$refs.messagesBody.scrollTop = this.$refs.messagesBody.scrollHeight;
    },
  },
  computed: {
    MESSAGES() {
      return this.$store.getters.MESSAGES(this.$route.params.id);
    },
  },
  mounted() {
    // console.log(this.$route.params.id)

    this.$store
      .dispatch("GET_MESSAGES", this.$route.params.id)
      .then((result) => {
        this.scrollDown();
      })
      .catch((err) => {});
  },
};
</script>
<style lang="">
</style>