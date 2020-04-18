<template>
<div class="rounded-full transform scale-100" :class="classes"></div>
</template>

<script>
export default {
    props: {
        color: {
            type: String,
            required: false,
            default: 'green',
            validator: function (value) {
                return ['green', 'orange'].indexOf(value) !== -1
            }
        },

        size: {
            type: Number,
            default: 3,
            validator: function (value) {
                return [1, 2, 3, 4, 5, 6, 8].indexOf(value) !== -1
            }
        }
    },

    computed: {
        // classes()
        // {
        //     let classes =
        //     return this.color == 'green' ? 'pulse-green' : 'pulse-orange'

        // }

        classes() {
            let r =  {
                'pulse-green': this.color == 'green',
                'pulse-orange': this.color != 'green',
            }

            const widthKey = "w-" + this.size + ' h-' + this.size

            r[widthKey] = true

            return r
        }
    }
}
</script>
