<template>
  <select class="form-control select2-component" style="width: 100%;">
    <slot></slot>
  </select>
</template>

<script>
import Select2 from "select2";

export default {
  props: ["options", "value", "name", "placeholder"],

  mounted() {
    var vm = this;

    $(this.$el)
      .select2({
        theme: "bootstrap4",
        data: this.options,
        placeholder: this.placeholder,
        height: 'resolve'
      })
      .val(this.value)
      .trigger("change")
      .on("change", function () {
        vm.$emit("input", this.value);
      });
  },

  watch: {
    value(param) {
      $(this.$el).val(param).trigger("change");
    },

    options(param) {
      $(this.$el).select2({
        data: param,
      });
    },

    destroyed() {
      $(this.$el).off().select2("destroy");
    },
  },
};
</script>

