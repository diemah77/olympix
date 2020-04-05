<template>
<div :class="['w-3 h-3 rounded-full transform scale-100', classes]"></div>
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
        }
    },

    computed: {
        classes()
        {
            return this.color == 'green' ? 'pulse-green' : 'pulse-orange'
        }
    }
}
</script>
