<template lang="">
    <div class="col-7 px-0">
      {{ MESSAGES }}
      <div class="px-4 py-5 chat-box bg-white">
      <!-- message -->
      <message></message>
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
  computed: {
    MESSAGES () {
      console.log(this.$route.params.id)
      return this.$store.getters.MESSAGES(this.$route.params.id);
    }
  },
  mounted() {
    // console.log(this.$route.params.id)

    this.$store
      .dispatch("GET_MESSAGES", this.$route.params.id)
      .then((result) => {})
      .catch((err) => {});
  },
};
</script>
<style lang="">
</style>