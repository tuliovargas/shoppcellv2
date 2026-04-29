<template>
	<div class="modal" id="usersModal" tabindex="-1" role="dialog">
		<form class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title text-secondary font-weight-bold">
						{{ modalLabel }}
					</h5>
					<button
						type="button"
						class="close"
						data-dismiss="modal"
						aria-label="Close"
					>
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body modal-body-seller">
					<div class="py-4">
						<div
							v-for="user in users"
							:key="user.id"
							class="row card-seller"
							:class="[
								user.id === userSelected.id
									? 'hover border-bottom border-top'
									: '',
							]"
							@click="selectedSeller(user)"
						>
							<div class="col-3">
								<img
									class="mr-3 py-2"
									:class="[
										user.id === userSelected.id ? 'opacity-1' : 'opacity-03',
									]"
									style="border-radius: 15px; max-width: 70%"
									:src="
										user && user.avatar
											? 'storage/' + user.avatar
											: '/images/default-avatar.png'
									"
									alt=""
								/>
							</div>
							<div class="col-9 d-flex flex-column justify-content-center">
								<p
									class="font-weight-bold mb-1 text-seller"
									:class="[
										user.id === userSelected.id ? 'text-primary' : 'text-muted',
									]"
								>
									{{ label }}
								</p>
								<p class="text-name">{{ user.name }}</p>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer justify-content-center">
					<button
						v-if="userSelected"
						:disabled="waitingSubmiting"
						type="button"
						@click.prevent="confirmedSeller()"
						class="btn btn-lg btn-primary button-confirm"
					>
						<span v-if="!waitingSubmiting">Confirmar</span>
						<i v-else class="fas fa-spinner fa-pulse px-5"></i>
					</button>
					<button v-else type="button" disabled class="btn btn-lg btn-primary">
						Vendedor
					</button>
				</div>
			</div>
		</form>
	</div>
</template>

<script>
import toastr from "toastr";
import "toastr/build/toastr.min.css";
import confetti from "canvas-confetti";

export default {
	name: "ModalUsers",

	props: {
		users: {
			type: Array,
			required: true,
			default: () => [],
		},
		userSelected: {
			type: Object,
			required: true,
			default: () => {},
		},
		type: {
			type: String,
			default: "Vendedor",
		},
	},

	data() {
		return {
			hasSeller: null,
			waitingSubmiting: false,
		};
	},

	beforeDestroy() {
		this.$emit("onClosed", null);
	},

	computed: {
		modalLabel() {
			return "Selecione um vendedor";
		},
		label() {
			return this.type.toString().toUpperCase();
		},
	},

	methods: {
		selectedSeller(user) {
			this.hasSeller = user;
			this.$emit("selectedUser", user);
		},
		async confirmedSeller() {
			if (!this.hasSeller)
				return toastr.error("Selecione um vendedor antes de confirmar a Order");

			this.waitingSubmiting = true;
			await confetti({
				particleCount: 150,
				spread: 70,
				zIndex: 2000,
			});
			this.$emit("confirmedUser", true);
			this.hasSeller = null;
		},
	},
};
</script>

<style lang="scss" scoped>
.card-seller:hover {
	background-color: rgba(228, 231, 237, 0.5);
	cursor: pointer;
}

.hover {
	background-color: rgba(228, 231, 237, 0.5);
}

.modal-body-seller {
	height: 400px;
	overflow-y: auto;
}

.modal-footer {
	border-top: none;
}

.text-seller {
	color: #4b545c;
	font-size: 13px;
	font-family: Roboto;
	font-weight: normal !important;
	letter-spacing: 0.05em;
}

.text-name {
	font-family: Roboto;
	font-style: normal;
	font-weight: normal;
	font-size: 16px;
	line-height: 19px;
	letter-spacing: 0.01em;
}

.button-confirm {
	background: #21316f !important;
	border-color: #21316f !important;
	border-radius: 4px !important;
	min-height: 48px !important;
}
</style>

<style></style>
