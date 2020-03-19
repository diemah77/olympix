<template>
<ul class="flex items-center list-reset pt-3 pb-1 text-sm">
	<li v-for="(b, index) in breadcrumbs" :key="index">
		<template v-if="! last(index)">
			<inertia-link :href="b.url" class="link text-blue-600 hover:text-blue-400 hover:underline">{{ b.title }}</inertia-link>

			<span class="mx-1 text-gray-600">/</span>
		</template>

		<template v-else>
			<span class="text-gray-600">{{ b.title }}</span>
		</template>
	</li>
</ul>
</template>

<script>
export default {
	props: {
		breadcrumbs: {
			type: Array,
			required: true
		}
	},

	methods: {
		last(index)
		{
			return index == this.breadcrumbs.length - 1
		}
	}
}
</script>
