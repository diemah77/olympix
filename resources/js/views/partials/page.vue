<template>
<div :class="['mx-auto', classes]">
	<slot />
</div>
</template>

<script>
export default {
    props: {
        width: {
            type: String,
            required: false,
            default: 'full',
            validator: function (value) {
                return ['full', 'narrow', 'default'].indexOf(value) !== -1
            }
        }
    },

    computed: {
        classes()
        {
            if (this.width == 'narrow')
            {
                return 'max-w-4xl'
            }

            return this.width == 'default' ? 'max-w-screen-xl' : 'max-w-none'
        }
    }
}
</script>
