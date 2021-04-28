<template lang="en">
 <div class="container py-5 px-4">
  <!-- For demo purpose-->
  <header class="text-center">
    <h1 class="display-4 text-white">Bootstrap Chat</h1>
    <p class="text-white lead mb-0">An elegant chat widget compatible with Bootstrap 4</p>
    <p class="text-white lead mb-4">Snippet by
      <a href="https://bootstrapious.com" class="text-white">
        <u>Bootstrapious</u></a>
    </p>
  </header>

  <div class="row rounded-lg overflow-hidden shadow">
    <!-- Users box-->
        <left></left>
    <!-- Chat Box-->
        <right></right>
  </div>
</div>

</template>
<script>
import left from "./left/left";
import right from "./right/right";
export default {
  components: { left, right },
};
</script>

<style lang="en">
</style>