<template>
    <div>
        <div class="card card-body" style="overflow-y: scroll; height:500px" ref="refDivScroll">

            <slot name="filters" :filters="filters"></slot>

            <datasource :ref-scroll="{ref:$refs, name:'refDivScroll'}"
                        url="/clients/get"
                        :filters="filters"
            >

                <div slot-scope="{data}">
                    <slot :data="data"></slot>
                </div>

            </datasource>
        </div>

    </div>
</template>

<script>

export default {

    components:{

    },
    props:{

    },
    data() {
        return {
            filters: {},
        }
    },

    methods: {

    },
    mounted() {

    }
}
</script>

<style lang="scss" scoped>

</style>
