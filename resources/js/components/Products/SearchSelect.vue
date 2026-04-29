<template>
  <div class="form-group">
    <div class="position-relative">
      <div class="input-group">
        <input
          ref="inputSearch"
          type="search"
          autocomplete="no"
          :placeholder="placeholder"
          class="form-control"
          @keyup="handleInput"
          @keyup.down="onArrowDown"
          @keyup.up="onArrowUp"
          @keyup.enter="onEnter"
          v-model="search"
        />
      </div>
      <ul
        v-if="search && items.length && showList"
        ref="scrollContainer"
        class="list-group w-100 search-popup"
      >
        <li
          v-for="(item, i) in items"
          :key="item.id"
          @click="handleSelect(item)"
          class="list-group-item"
          :class="{
            'is-active': i === arrowCounter,
          }"
          ref="options"
        >
          <div class="flex justify-between items-center">
            <span class="font-weight-bold">{{ item.text }}</span>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    placeholder: {
      type: String,
    },
    showList: {
      type: Boolean,
      default: false,
    },
    items: {
        type: Array,
        default: [],
    }
  },
  data() {
    return {
      arrowCounter: 0,
      search: "",
    };
  },

  methods: {
    handleInput(event) {
      this.$emit("input", event);
    },

    select(item) {
      this.$emit("select", item);
    },

    onArrowDown(ev) {
      ev.preventDefault();
      if (this.arrowCounter < this.items.length - 1) {
        this.arrowCounter = this.arrowCounter + 1;
        this.fixScrolling();
      }
    },

    onArrowUp(ev) {
      ev.preventDefault();
      if (this.arrowCounter > 0) {
        this.arrowCounter = this.arrowCounter - 1;
        this.fixScrolling();
      }
    },

    onEnter() {
      this.$emit("close");
      if (this.items[this.arrowCounter]) {
        this.search = this.items[this.arrowCounter].text;
        this.$emit("select", this.items[this.arrowCounter] || { text: this.search });
      }
      this.arrowCounter = 0;
      this.$refs.inputSearch.focus();
    },

    handleSelect(item) {
      this.$emit("close");
      if (item) {
        this.search = item.text;
        this.$emit("select", item);
      }
      this.arrowCounter = 0;
      this.$refs.inputSearch.focus();
    },

    fixScrolling() {
      const liH = this.$refs.options[this.arrowCounter].clientHeight;
      if (
        liH * this.arrowCounter >
        this.$refs.scrollContainer.offsetHeight - liH
      ) {
        this.$refs.scrollContainer.scrollTop = liH * this.arrowCounter;
      } else {
        this.$refs.scrollContainer.scrollTop = 0;
      }
    },
  }
};
</script>

<style scoped>
input {
  padding: 24px 20px;
  border: 1px solid #e4e7ed;
  color: #4b545c;
}

.search-popup {
  position: absolute;
  top: 38px;
  z-index: 20;
  width: calc(100% - 16px);
  overflow: auto;
  color: #4b545c;
  box-shadow: 0 4px 12px 0 rgb(0, 0, 0, 20%);
}

.search-popup li.is-active {
  color: #0983e8;
  background-color: #e3f2ff;
}

.list-group-item:hover {
    cursor: pointer;
    background: #e9f3fc;
}
</style>
