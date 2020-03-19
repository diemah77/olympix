<template>
<div class="flex items-center">
    <icon v-if="finished" class="text-blue-500 mr-2" icon="check" fixed-width></icon>
    <div v-else :class="['h-3 w-3 mr-2 rounded-full', {'bg-green-500': started, 'bg-gray-400': !started}]"></div>

    <span class="">{{ status }}</span>
</div>
</template>

<script>
	export default {
		props: {
			status: {
				type: String,
				required: true
			}
		},

		computed:
		{
			started()
			{
				return this.status == 'Gestartet'
			},

			finished()
			{
				return this.status == 'Beendet'
			}
		}
	}
</script>
