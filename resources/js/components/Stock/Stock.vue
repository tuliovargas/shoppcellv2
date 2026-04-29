<template>
  <div>
    <div class="row">
      <div class="col-md-6">
        <div class="row" v-if="false">
          <div class="col-md-11">
            <input
              type="search"
              ref="searchClient"
              autocomplete="off"
              @keyup="getSuppliers"
              v-model="supplierSearch"
              class="form-control"
              placeholder="Fornecedor"
              id="supplier"
              @keyup.down="onArrowDown"
              @keyup.up="onArrowUp"
              @keyup.enter="onEnter"
            />
            <ul
              ref="scrollContainer"
              v-if="supplierSearch && showListSupplier"
              class="list-group search-popup"
            >
              <li
                ref="options"
                v-if="suppliers.length === 0"
                class="list-group-item is-active"
              >
                <div
                  class="flex justify-between items-center"
                  @click="createSupplier()"
                >
                  Fornecedor não existe, clique aqui para criá-lo
                </div>
              </li>
              <li
                v-for="(supplier, index) in suppliers"
                :key="supplier.id"
                :class="{ 'is-active': index === arrowCounter }"
                ref="options"
                class="list-group-item"
              >
                <div
                  class="flex justify-between items-center"
                  @click="selectedSupplier(supplier)"
                >
                  {{ supplier.name + " | " + "CNPJ: " + supplier.cnpj }}
                </div>
              </li>
            </ul>
          </div>
          <div class="col-md-1">
            <button
              @click="openCreateModal"
              type="button"
              class="btn btn-info btn-block btn-flat"
            >
              <i class="fa fa-plus"></i>
            </button>
          </div>
        </div>
        <div class="row">
          <div class="col-12 text-right">
            <button class="btn btn-info" @click="updateStock">Atualizar</button>
          </div>
        </div>
      </div>
      <div class="col-md-6"></div>
    </div>

    <div class="row mt-3">
      <div class="col-md-6">
        <div class="card card-body">
          <div class="form-group">
            <div class="position-relative">
              <div class="input-group">
                <input
                  ref="searchProduct"
                  type="search"
                  autocomplete="no"
                  placeholder="Produto"
                  v-model="productSearch"
                  @keyup="getProducts"
                  class="form-control"
                  @keyup.down="onArrowDownProd"
                  @keyup.up="onArrowUpProd"
                  @keyup.enter="onEnterProd"
                  id="product"
                />
              </div>
              <ul
                v-if="
                  productSearch &&
                  products &&
                  products.length &&
                  showListProduct
                "
                ref="scrollContainerProd"
                class="list-group w-100 search-popup"
              >
                <li
                  v-for="(product, i) in products"
                  :key="product.id"
                  @click="addCart(product)"
                  class="list-group-item"
                  ref="optionsProd"
                  :class="{ 'is-active': i === arrowCounterProd }"
                  v-if="product.id != 1"
                >
                  <div class="flex justify-between items-center">
                    <span class="font-weight-bold">{{ product.name }} | </span>

                    <span :class="false">
                      <span class="font-weight-bold">Estoque:</span>
                      {{ product.quantity_in_stock }}
                    </span>

                    <span class="font-weight-bold">| Marca:</span
                    >{{
                      product.brand && product.brand.name
                        ? product.brand.name
                        : ""
                    }}

                    <span v-if="product.barcode" class="font-weight-bold"
                      >| Código:</span
                    >{{ product.barcode }}
                  </div>
                </li>
              </ul>
            </div>
          </div>

          <div v-if="stock.length > 0" class="table-responsive">
            <table class="table table-budget">
              <thead>
                <tr>
                  <th class="th__prod-name" scope="col">Produtos</th>
                  <th class="th__prod-amount" scope="col">Quantidade</th>
                  <th class="th__prod-remove" scope="col">Excluir</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  style="cursor: default"
                  v-for="(item, index) in stock"
                  :key="index"
                >
                  <td :title="item.productName" class="td__prod-name">
                    <span>{{ item.productName }}</span>
                  </td>
                  <td class="td__prod-amount">
                    <span>
                      <a
                        href=""
                        @click.prevent="removeAmountProductCart(index)"
                        class="text-primary mr-2"
                      >
                        <i class="fas fa-minus-circle"></i>
                      </a>
                      {{ item.amount }}
                      <a
                        href=""
                        @click.prevent="addAmountProductCart(index)"
                        class="text-primary ml-2"
                      >
                        <i class="fas fa-plus-circle"></i>
                      </a>
                    </span>
                  </td>
                  <td class="td__prod-remove">
                    <span>
                      <a
                        href=""
                        @click.prevent="removeProductCart(index)"
                        class="text-danger"
                      >
                        <i class="fas fa-trash"></i>
                      </a>
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-6"></div>
    </div>

    <!-- Modal supplier -->
    <div
      class="modal"
      id="createSupplierModal"
      tabindex="-1"
      role="dialog"
      v-if="false"
    >
      <form class="modal-dialog" @submit.prevent="storeSupplier">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title font-weight-bold">
              {{ supplier ? "Atualizar fornecedor" : "Novo fornecedor" }}
            </h5>
            <button
              type="button"
              class="close"
              @click="clearModalSupplier"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <input
                v-model="newSupplier.name"
                class="form-control"
                placeholder="Nome"
                autocomplete="no"
                ref="supplierName"
                required
              />
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <input
                    v-model="newSupplier.cnpj"
                    class="form-control"
                    placeholder="CNPJ"
                    v-mask="'##.###.###/####-##'"
                    required
                  />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input
                    v-model="newSupplier.state_registration"
                    class="form-control"
                    placeholder="Inscrição estadual"
                    required
                  />
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <input
                    v-model="newSupplier.cellphone"
                    class="form-control"
                    placeholder="Celular"
                    v-mask="'(##) #####-####'"
                  />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input
                    v-model="newSupplier.phone"
                    class="form-control"
                    placeholder="Telefone"
                    v-mask="'(##) ####-####'"
                  />
                </div>
              </div>
            </div>

            <div class="form-group">
              <input
                v-model="newSupplier.responsible_person"
                class="form-control"
                placeholder="Responsável"
              />
            </div>
            <div class="form-group">
              <input
                v-model="newSupplier.observation"
                class="form-control"
                placeholder="Observação"
              />
            </div>

            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <input
                    v-model="newSupplier.postcode"
                    @keyup="handleViacep"
                    autocomplete="no"
                    class="form-control"
                    placeholder="CEP"
                    v-mask="'#####-###'"
                  />
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-9">
                <div class="form-group">
                  <input
                    v-model="newSupplier.street"
                    autocomplete="no"
                    class="form-control"
                    placeholder="Endereço"
                  />
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <input
                    v-model="newSupplier.number"
                    class="form-control"
                    placeholder="Numero"
                  />
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <input
                    v-model="newSupplier.neighborhood"
                    autocomplete="no"
                    class="form-control"
                    placeholder="Bairro"
                  />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input
                    v-model="newSupplier.complement"
                    class="form-control"
                    placeholder="Complemento"
                  />
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-9">
                <div class="form-group">
                  <input
                    v-model="newSupplier.city"
                    class="form-control"
                    placeholder="Cidade"
                  />
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <input
                    v-model="newSupplier.state"
                    class="form-control"
                    placeholder="Estado"
                  />
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary">
              Confirmar
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import toastr from "toastr";
import "toastr/build/toastr.min.css";
import { mask } from "vue-the-mask";

export default {
  name: "Stock",
  directives: { mask },
  data() {
    return {
      supplierSearch: "",
      showListSupplier: "",
      suppliers: [],
      arrowCounter: 0,
      arrowCounterProd: 0,
      supplier: "",
      newSupplier: {
        name: "",
        cnpj: "",
        state_registration: "",
        cellphone: "",
        phone: "",
        responsible_person: "",
        observation: "",
        postcode: "",
        street: "",
        state: "",
        number: "",
        complement: "",
        city: "",
        neighborhood: "",
      },
      productSearch: "",
      showListProduct: "",
      products: [],
      stock: [],
    };
  },
  created: function () {
    let self = this;
    window.addEventListener("click", function () {
      self.showListSupplier = false;
    });
  },
  methods: {
    onArrowDown(ev) {
      ev.preventDefault();
      if (this.arrowCounter < this.suppliers.length - 1) {
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
    async updateStock() {
      if (this.stock.length == 0) {
        toastr.error("Selecione pelo menos um produto");
      } else {
        await axios
          .post("/stocks/updateAll", {
            products: this.stock,
          })
          .then(({ data }) => {
            if (data.hasFail) {
              toastr.warning("Um ou mais produtos não poderam ser atualizados");
            } else {
              toastr.success("Produtos atualizados com sucesso.");
            }
            this.stock = [];
          });
      }
    },

    onEnter() {
      this.showListSupplier = false;
      if (this.suppliers.length > 0) {
        this.supplier = this.suppliers[this.arrowCounter];
        this.supplierSearch = this.supplier.full_name;
      } else {
        this.createSupplier();
      }
      this.arrowCounter = 0;
      this.$refs.searchProduct.focus();
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

    async getSuppliers() {
      this.showListSupplier = true;

      addEventListener("keydown", this.closePopup);

      const config = {
        params: {
          search: this.supplierSearch,
          paginate: false,
        },
      };

      let url = "/suppliers";

      try {
        let response = await axios.get(url, config);
        this.suppliers = response.data;
      } catch (error) {
        console.error(error);
      }
    },

    createSupplier() {
      if (!this.emailIsValid || !this.cpfIsValid) return;
      this.newSupplier.full_name = this.supplierSearch;
      this.$nextTick(function () {
        this.$refs.supplierName.focus();
      });
      $("#createSupplierModal").modal("show");
    },

    async storeSupplier() {
      if (Object.keys(this.supplier).length === 0) {
        toastr.options = {
          positionClass: "toast-bottom-right",
        };
        toastr.info("Criando fornecedor");

        try {
          toastr.clear();
          const formData = new FormData();
          Object.keys(this.newSupplier).forEach((key) => {
            formData.append(key, this.newSupplier[key]);
          });
          let response = await axios.post("/suppliers", this.newSupplier);
          this.supplier = response.data;
          this.supplierSearch = this.newSupplier.full_name;
          this.showListSupplier = false;
          $("#createSupplierModal").modal("hide");
          toastr.success("Fornecedor criado com sucesso");
        } catch (error) {
          toastr.clear();
          toastr.error("Não foi possível criar o fornecedor");
          console.error(error);
        }
      } else {
        await this.updateSupplier();
      }
    },

    async updateSupplier() {
      toastr.options = {
        positionClass: "toast-bottom-right",
      };
      toastr.info("Atualizando fornecedor...");

      try {
        toastr.clear();
        const formData = new FormData();
        Object.keys(this.newSupplier).forEach((key) => {
          formData.append(key, this.newSupplier[key]);
        });
        formData.append("_method", "PUT");

        let response = await axios.post(
          "/suppliers/" + this.supplier.id,
          formData
        );
        this.supplier = response.data;

        this.supplierSearch = this.newSupplier.name;
        this.showListSupplier = false;
        $("#createSupplierModal").modal("hide");
        toastr.success("Fornecedor atualizado com sucesso");
      } catch (error) {
        toastr.clear();
        toastr.error("Não foi possível atualizar o fornecedor");
        console.error(error);
      }
    },

    selectedSupplier(supplier) {
      this.arrowCounter = 0;
      this.supplier = supplier;
      this.supplierSearch = supplier.name;
      this.showListSupplier = false;
      removeEventListener("keydown", this.closePopup);
    },

    clearModalSupplier() {
      this.newSupplier = {
        name: "",
      };
    },

    openCreateModal() {
      this.newSupplier.name = this.supplier.name;
      this.newSupplier.cnpj = this.supplier.cnpj;
      this.newSupplier.state_registration = this.supplier.state_registration;
      this.newSupplier.cellphone = this.supplier.cellphone;
      this.newSupplier.phone = this.supplier.phone;
      this.newSupplier.responsible_person = this.supplier.responsible_person;
      this.newSupplier.observation = this.supplier.observation;
      this.newSupplier.postcode = this.supplier.address.postcode;
      this.newSupplier.street = this.supplier.address.street;
      this.newSupplier.number = this.supplier.address.number;
      this.newSupplier.complement = this.supplier.address.complement;
      this.newSupplier.neighborhood = this.supplier.address.neighborhood;
      this.newSupplier.state = this.supplier.address.state;
      this.newSupplier.city = this.supplier.address.city;
      this.$nextTick(function () {
        this.$refs.supplierName.focus();
      });
      $("#createSupplierModal").modal("show");
    },

    async getProducts() {
      this.showListProduct = true;
      addEventListener("keydown", this.closePopup);

      const config = {
        params: {
          search: this.productSearch,
          paginate: false,
        },
      };

      let url = "/products";

      try {
        let response = await axios.get(url, config);
        this.products = response.data;
      } catch (error) {
        console.error(error);
      }
    },

    onArrowDownProd(ev) {
      ev.preventDefault();
      if (this.arrowCounterProd < this.products.length - 1) {
        this.arrowCounterProd = this.arrowCounterProd + 1;
        this.fixScrollingProd();
      }
    },

    onArrowUpProd(ev) {
      ev.preventDefault();
      if (this.arrowCounterProd > 0) {
        this.arrowCounterProd = this.arrowCounterProd - 1;
        this.fixScrollingProd();
      }
    },

    onEnterProd() {
      const product = this.products[this.arrowCounterProd];
      this.addCart(product);
      this.showListProduct = false;
      this.productSearch = "";
      this.arrowCounterProd = 0;
    },

    fixScrollingProd() {
      const liH = this.$refs.optionsProd[this.arrowCounterProd].clientHeight;
      if (
        liH * this.arrowCounterProd >
        this.$refs.scrollContainerProd.offsetHeight - liH
      ) {
        this.$refs.scrollContainerProd.scrollTop = liH * this.arrowCounterProd;
      } else {
        this.$refs.scrollContainerProd.scrollTop = 0;
      }
    },

    addCart(product) {
      this.arrowCounterProd = 0;
      const ID_CATEGORY_MAINTENANCE = 2;

      const isMaintenance = product.categories.find(
        (cat) => cat.id === ID_CATEGORY_MAINTENANCE
      );
      if (isMaintenance) this.showMaintenanceForm = true;

      let productInCart = this.stock.find(
        (element) => element.productName === product.name
      );

      if (productInCart === undefined) {
        this.stock.push({
          id: product.id,
          productName: product.name,
          barcode: product.barcode,
          amount: 1,
          price: parseFloat(product.price),
          discount: 0,
          stock: 10,
          subtotal: parseFloat(product.price),
        });
      } else {
        let productIndex = this.stock.findIndex(
          (element) => element.productName === product.name
        );
        if (this.stock[productIndex].amount >= this.stock[productIndex].stock)
          return;

        let subtotal =
          parseFloat(this.stock[productIndex].subtotal) +
          parseFloat(product.price);

        this.stock[productIndex].amount++;
        this.stock[productIndex].subtotal = subtotal;
      }

      this.productSearch = "";
      this.showListProduct = false;

      this.calculateCart();
    },

    calculateCart() {
      let total = 0;
      let amount = 0;

      this.stock.map((item) => {
        amount += item.amount;
        item.subtotal =
          (item.price - item.discount + item.addition) * item.amount;
        total += item.subtotal;
      });

      this.total = total;
      this.amount = amount;
    },

    removeAmountProductCart(index) {
      this.stock[index].amount--;
      if (this.stock[index].amount === 0) {
        this.removeProductCart(index);
      } else {
        this.calculateCart();
      }
    },

    addAmountProductCart(index) {
      this.stock[index].amount++;
      this.calculateCart();
    },

    removeProductCart(index) {
      this.stock.splice(index, 1);
      this.calculateCart();
    },

    async handleViacep() {
      const postcode = this.newSupplier.postcode.replace(/\D/g, "");

      if (postcode.length === 8) {
        this.newSupplier.street = "";
        this.newSupplier.neighborhood = "";
        this.newSupplier.city = "";
        this.newSupplier.state = "";

        const { data } = await axios.get(
          `https://viacep.com.br/ws/${postcode}/json`
        );
        if (data.erro) {
          toastr.options = {
            positionClass: "toast-bottom-right",
          };
          toastr.error("CEP não encontrado");
          return;
        }
        this.newSupplier.street = data.logradouro;
        this.newSupplier.neighborhood = data.bairro;
        this.newSupplier.city = data.localidade;
        this.newSupplier.state = data.uf;
      }
    },
  },
};
</script>

<style lang="scss"
       scoped>
li:hover {
  background-color: #f2f4f6;
  cursor: pointer;
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
</style>
