<template>
<div class="flex border-b border-gray-300">
    <inertia-link
        class="link px-6 py-3 flex items-center text-sm text-gray-600 hover:text-gray-800 border-b-2 border-transparent"
        :class="{'text-gray-800 border-blue-500': route().current('schedule.index')}"
        :href="route('schedule.index', [$page.t.id]).url()">

        <span>Spiele</span>
    </inertia-link>

    <inertia-link
        class="link px-6 py-3 flex items-center text-sm text-gray-600 hover:text-gray-800 border-b-2 border-transparent"
        :class="{'text-gray-800 border-blue-500': route().current('schedule.tables')}"
        :href="route('schedule.tables', [$page.t.id]).url()">

        <span>Tischansicht</span>
    </inertia-link>
</div>
</template>

<script>
export default {
    props : {
        championship: Object
    },

    methods: {
        contains(matches)
		{
			const paths = Array.isArray(matches) ? matches : [matches]

		    return paths.some(path =>
		    {
		    	return location.pathname.indexOf(path) > -1
		    })
		},
    }
}
</script>
