<template lang="">
    <div class="col-7 px-0">
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
};
</script>
<style lang="">
</style>