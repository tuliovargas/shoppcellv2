<template>
    <div>
        <slot :data="dataLoaded"></slot>
    </div>
</template>

<script>

import Requestable from '../libraries/requestable.js'

export default {
    name: "datasource",
    props:{
        refScroll:{
            required: true
        },
        url:{
            required: true
        },
        page:{
            required: false,
            default:1
        },
        pageSize:{
            required: false,
            default:15
        },
        method:{
            required: false,
            default:'post'
        },
        filters:{}
    },
    data() {
        return {
            oldScrollTop: null,
            lastRequestInstance:null,
            lastRequestTime: null,
            dataLoaded:[],
            internalRequests: {}
        }
    },

    methods: {

        load(){
            this.lastRequestInstance = this.construct().request();
        },
        reload() {
            /// as vezes o valor não de mudança ainda não foi atualizado
            this.$nextTick(() => {
                this.clearData();
                this.load();
            });
        },

        construct () {

            //dados de configuração
            const config = {
                data: {
                    datasource: {
                        page: this.page,
                        page_size:this.pageSize,
                        filters: this.filters
                    }
                },
                method: this.method
            };

            this.lastRequestTime = Date.now();
            const instance = new Requestable(this.url, {
                requestConfig: config,
                onSuccess: (response) => {

                    //Garante que a requisição é diferente
                    this.internalRequests[JSON.stringify(config)] = response.data.data.data;

                    this.dataLoaded = _.flatMap(this.internalRequests, (value) => {
                        return value;
                    });
                }
            });

            return instance
        },

        loadNextPage() {
            this.page ++;
            this.load();
        },
        canRequestNow() {
            return !this.lastRequestInstance.loading && Date.now() - this.lastRequestTime > 1000;
        },
        clearData() {
            this.dataLoaded = [];
            this.internalRequests = {};
            this.page = 1;
        },
    },
    mounted() {

        this.load();

        $(this.refScroll.ref[this.refScroll.name]).on('scroll', (e) => {

            if (this.canRequestNow()) {
                let totalHeight = $(this.refScroll.ref[this.refScroll.name])[0].scrollHeight;
                //let heightToLoad = totalHeight - (totalHeight * 0.20);
                let heightToLoad = totalHeight - (totalHeight * 0.05);

                let currentModalScrollTop = _.clone($(this.refScroll.ref[this.refScroll.name]).scrollTop());

                if ($(this.refScroll.ref[this.refScroll.name]).scrollTop() + $(this.refScroll.ref[this.refScroll.name]).innerHeight() >= heightToLoad) {
                    if (this.oldScrollTop < currentModalScrollTop) {
                        this.loadNextPage();
                    }
                }

                this.oldScrollTop = currentModalScrollTop;
            }
        });
    },


    watch : {
        filters: {
            deep: true,
            handler: function(){
                this.reload();
            },
        },
    }
}
</script>

<style lang="scss" scoped>

</style>
