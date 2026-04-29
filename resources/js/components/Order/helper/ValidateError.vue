<template>
  <div>
    <div :class="{ styleClass, error: hasError }" class="w-100">
      <slot></slot>
      <small v-if="hasError" class="text-danger">{{ hasError }}</small>
    </div>
  </div>
</template>

<script>
import _ from "lodash";
export default {
  props: {
    styleClass: {
      type: String,
    },
    error: {
      type: Array,
    },
    index: null,
    property: null,
  },

  computed: {
    hasError() {
      return this.error
        ? _.get(this.error, `[${this.index}]${this.property}`)
        : null;
    },

  },
};
</script>

<style lang="scss">
.error {
  > select,
  > div,
  > input,
  > textarea {
    border: 1px solid red;
  }
}
</style>
