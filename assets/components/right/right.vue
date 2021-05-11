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
import { mapGetters } from "vuex";
import message from "./message";
import messageInput from "./messageInput";
export default {
  components: { message, messageInput },
  methods: {
    scrollDown() {
      this.$refs.messagesBody.scrollTop = this.$refs.messagesBody.scrollHeight;
    },
    addMessage(data) {
      this.$store.commit("ADD_MESSAGE", {
        conversationId: this.$route.params.id,
        payload: data,
      });
    },
  },
  computed: {
    ...mapGetters(["HUBURL"]),

    MESSAGES() {
      return this.$store.getters.MESSAGES(this.$route.params.id);
    },
  },
  mounted() {
    const vm = this;
    // console.log(this.$route.params.id)

    this.$store
      .dispatch("GET_MESSAGES", this.$route.params.id)
      .then(() => {
        this.scrollDown();
          let url = new URL(this.HUBURL);
          url.searchParams.append(
            "topic",
            `/conversations/${this.$route.params.id}`
          );
          const eventSource = new EventSource(url, {
            withCredentials: true,
          });

          eventSource.onmessage = function (event) {
            console.log(event);
            vm.addMessage(JSON.parse(event.data));
          };
      })
      .catch((err) => {});
  },
  watch: {
    MESSAGES: function () {
      this.$nextTick(function () {
        this.scrollDown();
      });
    },
  },
};
</script>
<style lang="">
</style>