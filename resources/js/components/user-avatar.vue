<template>
    <img class="block rounded-full" :class="classes" :src="source">
</template>

<script>
export default {
	props: {
		image: {
			type: String,
			required: true
        },

		size: {
			type: String,
			default: 'medium'
		}
	},

	computed:
	{
		source()
		{
			return this.image == '' ? '/img/person.png' : this.image
		},

		classes()
		{
			const classes = {
				small: 'h-10 w-10',
				medium: 'h-16 w-16',
				large: 'h-24 w-24',
				huge: 'h-48 w-48'
			}

			return classes[this.size]
		}
	}
}
</script>
