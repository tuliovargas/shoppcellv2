<template>
	<div
		class="modal"
		id="commentModal"
		tabindex="-1"
		role="dialog"
		ref="modalComment"
	>
		<form class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title font-weight-bold">
						Comentários
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
				<div class="comments-container">
					<div
						v-for="comment in comments"
						:key="comment.id"
						class="comment-card"
					>
						<h6 class="comment-card__title">
							{{ comment.user.name }} -
							<span>{{
								moment(comment.updated_at).format("DD/MM/YYYY - HH:MM")
							}}</span>
						</h6>
						<p class="comment-card__description">
							{{ comment.text }}
						</p>
						<h6
							v-if="comment.attachments && comment.attachments.length"
							class="comment-card__title-attatch"
						>
							Anexos
						</h6>
						<div class="d-flex flex-wrap">
							<a
								v-for="(file, i) in comment.attachments"
								:key="i"
								class="comment-card__attatchment"
								:href="'storage/' + file.file_name"
								v-text="getAttachmentName(file.file_name)"
								@click.prevent="
									downloadFile(
										file.file_name,
										getAttachmentName(file.file_name)
									)
								"
							/>
						</div>
					</div>
				</div>
				<div class="comments-controllers">
					<div class="form-group pt-2 d-flex row">
						<div class="col-md-9">
							<textarea
								class="form-control comment-input"
								:class="{ 'form-error': errors.comment }"
								rows="3"
								placeholder="Comentário"
								v-model="form.comment"
							>
							</textarea>
							<small v-if="errors.comment" class="comment-error">{{
								errors.comment
							}}</small>
						</div>
						<div class="col-md-3 d-flex flex-column">
							<label
								class="position-relative text-secondary font-weight-normal text-center"
							>
								<input class="file-input" type="file" @change="listFiles" />
								<svg
									class="mr-1"
									width="17"
									height="15"
									fill="none"
									xmlns="http://www.w3.org/2000/svg"
								>
									<path
										d="M8.565 1.548L2.331 7.782c-1.232 1.232-1.426 3.23-.318 4.573a3.328 3.328 0 004.932.242l7.283-7.284c.772-.772.92-2.027.23-2.87a2.084 2.084 0 00-3.094-.158L5.177 8.472a.836.836 0 000 1.178.836.836 0 001.178 0l5.156-5.156a.63.63 0 01.884 0 .63.63 0 010 .884l-5.073 5.074c-.772.772-2.027.919-2.87.23a2.083 2.083 0 01-.159-3.094L10.38 1.5c1.232-1.232 3.23-1.426 4.573-.318a3.332 3.332 0 01.241 4.932l-7.23 7.23c-1.691 1.691-4.443 1.968-6.287.442a4.588 4.588 0 01-.33-6.788L7.68.664a.63.63 0 01.884 0 .63.63 0 010 .884z"
										fill="#000"
										fill-opacity=".54"
									/>
								</svg>
								Anexar arquivo
							</label>
							<button
								class="btn btn-lg btn-secondary font-weight-bold"
								@click.prevent="submitComment"
							>
								Comentar
							</button>
						</div>
					</div>
					<ul class="list-group list-files px-2">
						<li
							class="list-group-item d-flex justify-content-between align-items-center"
							v-for="(file, i) in filesUploaded"
							:key="i"
						>
							<span>{{ file.name }}</span>
							<button
								class="btn"
								style="padding: 0 0.75rem"
								@click="removeImageFromList(i)"
							>
								<span style="display: block; color: #d63030">
									<i class="far fa-trash-alt"></i>
								</span>
							</button>
						</li>
					</ul>
				</div>
			</div>
		</form>
	</div>
</template>

<script>
import moment from "moment";
import _ from "lodash";
import axios from "axios";

export default {
	props: {
		comments: {
			required: true,
			default: () => []
		},
		currentOrder: {
			type: Object,
			default: () => {}
		},
		user: {
			type: Object,
			default: () => {}
		}
	},
	data() {
		return {
			form: {
				comment: ""
			},
			errors: {
				comment: ""
			},
			filesUploaded: [],
			moment: moment
		};
	},
	methods: {
		async submitComment() {
			const newComment = new FormData();
			newComment.append("user_id", this.user.id);
			newComment.append("user_name", this.user.name ? this.user.name : null);
			newComment.append(
				"created_at",
				moment(new Date()).format("DD/MM/YYYY hh:mm:ss")
			);
			newComment.append("text", this.form.comment);

			this.filesUploaded.forEach(function logArrayElements(
				element,
				index,
				array
			) {
				newComment.append("attachments[]", element);
			});

			if (this.form.comment.length < 5)
				return (this.errors.comment =
					"Comentários devem conter pelo menos 5 caracteres");
			else this.errors.comment = "";

			await axios
				.post(`/orders/${this.currentOrder.id}/comments`, newComment, {
					headers: {
						"Content-Type": "multipart/form-data"
					}
				})
				.then(r => {
					if (r.status === 200) {
						this.filesUploaded = [];
						this.form.comment = "";
						this.$emit("updateComments", true);
					}
				})
				.catch(e => console.error("Error Comment", e));
		},

		listFiles(event) {
			this.filesUploaded.push(event.target.files[0]);
		},

		getAttachmentName(path) {
			const lastSegment = path.split("/").pop();
			return lastSegment;
		},

		downloadFile(url, file_name) {
			axios
				.get("storage/" + url, { responseType: "blob" })
				.then(response => {
					const blob = new Blob([response.data]);
					const link = document.createElement("a");
					link.href = URL.createObjectURL(blob);
					link.download = file_name;
					link.click();
					URL.revokeObjectURL(link.href);
				})
				.catch(console.error);
		},

		clearData() {
			this.form.comment = "";
			this.filesUploaded = [];
			return (this.errors.comment = "");
		},

		scrollComments() {
			setTimeout(() => {
				const el = this.$el.querySelector("#comments");
				if (el) el.scrollTop = el.scrollHeight > 1 ? el.scrollHeight : 40;
			}, 75);
		}
	}
};
</script>

<style lang="scss" scoped>
.comments-container {
	padding: 20px;
	max-height: 420px;
	overflow-y: scroll;

	.comment-card {
		font-family: Roboto;
		background: #f5f6f8;
		border-radius: 4px;
		padding: 15px;
		margin-bottom: 10px;

		&__title {
			font-size: 14px;
			line-height: 22px;
			letter-spacing: 0.01em;
			color: #21316f;

			span {
				font-weight: 500;
				font-size: 12px;
				line-height: 14px;
				letter-spacing: 0.01em;
				color: #4b545c;
			}
		}

		&__description {
			font-weight: 300;
			font-size: 14px;
			line-height: 22px;
			letter-spacing: 0.01em;
			color: #4b545c;
		}

		&__title-attatch {
			font-weight: 500;
			font-size: 12px;
			line-height: 14px;
			letter-spacing: 0.01em;
			color: #4b545c;
		}

		&__attatchment {
			margin-right: 15px;
			font-size: 14px;
		}
	}
}

.comments-controllers {
	padding: 15px 20px 40px;
	.file-input {
		position: absolute;
		width: 100%;
		height: 100%;
		top: 0;
		left: 0;
		opacity: 0;
		cursor: pointer;
	}

	.comment-input {
		max-height: 80px;
		resize: none;
		border: 1px solid #e4e7ed;
	}

	.comment-error {
		color: #ee3939;
	}

	.form-error {
		border: 1px solid #ee3939;
	}

	.list-files {
		margin-top: 15px;
		padding: 0 1.5rem;
		max-height: 150px;
		overflow-y: auto;

		.list-group-item {
			border: none;
			background: rgba(228, 231, 237, 0.3);
			border-radius: 4px;
		}

		li {
			padding: 0.75rem 15px 0.75rem 35px;

			span {
				display: list-item;

				&::marker {
					color: #0983e8;
				}
			}
		}
	}
}
</style>
