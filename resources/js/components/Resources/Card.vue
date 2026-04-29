<template>
  <div class="col-12 col-sm-6 col-lg-3">
    <div class="card" :class="[this.color, {'h-100': this.fullHeight}]">
      <div class="card-header" v-if="this.header">
        <h4>{{ this.header }}</h4>
      </div>
      <div class="card-body">
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["header", "color", "fullHeight"],
};
</script>