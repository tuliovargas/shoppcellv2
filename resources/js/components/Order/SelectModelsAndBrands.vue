<template>
	<div class="form-group mb-0">
		<div class="position-relative">
			<div class="input-group">
				<input
					ref="inputSearch"
					:disabled="disable"
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
						'is-active': i === arrowCounter
					}"
					ref="options"
				>
					<div class="flex justify-between items-center">
						<span class="font-weight-bold">{{ item.name }}</span>
					</div>
				</li>
			</ul>
			<div
				v-if="createItem && !items.length"
				class="list-group w-100 search-popup"
				style="border-radius: 5px; margin-top: 5px;"
			>
				<div
					class="flex justify-between items-center item-register"
					@click="handleCreateItem"
				>
					<span class="font-weight-bold">{{
						`Deseja cadastrar o modelo "${search}" ?`
					}}</span>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import toastr from "toastr";
import "toastr/build/toastr.min.css";

export default {
	props: {
		placeholder: {
			type: String
		},
		showList: {
			type: Boolean,
			default: false
		},
		items: {
			type: Array,
			default: []
		},
		disable: {
			type: Boolean,
			default: false
		},
		createItem: {
			type: Boolean,
			default: false
		},
		brandId: {
			default: null
		}
	},
	data() {
		return {
			arrowCounter: 0,
			search: "",
			isCreatingItem: false
		};
	},

	watch: {
		search(value) {
			if (!value) this.$emit("select", null);
		}
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
			this.$emit("close", false);
			if (this.items[this.arrowCounter]) {
				this.search = this.items[this.arrowCounter].name;
				this.$emit(
					"select",
					this.items[this.arrowCounter].id || { text: this.search }
				);
			}
			this.arrowCounter = 0;
			this.$refs.inputSearch.focus();
			this.$emit("validate");
		},

		handleSelect(item) {
			this.$emit("close", false);
			if (item) {
				this.search = item.name;
				this.$emit("select", item.id);
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

		async handleCreateItem() {
			if (this.isCreatingItem) return;
			this.isCreatingItem = true;
			await axios
				.post(`/brand-models`, {
					name: this.search,
					brand_id: this.brandId,
					type: "vue"
				})
				.then(({ data: brandModel }) => {
					toastr.success(`Modelo ${this.search} cadastrado com sucesso`);
					this.$emit("select", brandModel.id);
					this.$emit("created", false);
				})
				.catch(e => {
					console.error("Erro on handleCreateItem", e);
					toastr.error("Erro ao tentar cadastrar o modelo, tente novamente.");
				})
				.finally(() => (this.isCreatingItem = false));
		},

		focus() {
			this.$refs.inputSearch.focus();
		}
	}
};
</script>

<style scoped>
input {
	padding: 12px 10px;
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

.item-register {
	padding: 10px 20px;
	background-color: #fff;
	cursor: pointer;
	user-select: none;
}
</style>
