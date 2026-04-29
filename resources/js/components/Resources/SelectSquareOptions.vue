<template>
    <div>

        <div class="d-flex w-100 justify-content-between" style="overflow-x: auto;">
            <template v-for="item in options">
                <div :class="(valueSelected == item.value) ? 'box mr-2 box-selected' : 'box mr-2'"
                     @click="boxClick(item.value)"
                >
                    <div class="box-content">
                        {{item.text}}
                    </div>
                </div>
            </template>
        </div>

    </div>
</template>

<script>

export default {

    name: "SelectSquareOptions",
    props:{
        options:{
            required:true,
            type: Array
        }
    },
    data() {
        return {
            valueSelected: null
        }
    },

    methods: {
        boxClick(value){
            this.$emit('input', value);
            this.valueSelected = value
        }
    },
    mounted() {

    }
}
</script>

<style lang="scss" scoped>
    .box div{
        border: dotted 2px #C2CFE0;
        height: 78px;
        width: 93px;
        justify-content: center;
        cursor: pointer;
        display: flex;
        align-items: center;
        border-radius: 10px;
    }
    .box:hover div{
        background-color: #0983E8;
        color: white;
    }
    .box-selected div{
        background-color: #0983E8;
        color: white;
    }
    .box-content{
        color: #4B545C;
        text-align: center;
    }
</style>
