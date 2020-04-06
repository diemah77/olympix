<template>
<div class="flex border-b border-gray-300">
    <inertia-link
		class="link px-6 py-3 flex items-center text-sm text-gray-600 hover:text-gray-800 border-b-2 border-transparent"
		:class="{'text-gray-800 border-blue-500': route().current('tournaments.edit') || route().current('tournaments.create')}"
		:href="editRoute()">

        <span>Grunddaten</span>
	</inertia-link>

    <template v-if="tournament.mode != 'create'">
        <inertia-link
            class="link px-6 py-3 flex items-center text-sm text-gray-600 hover:text-gray-800 border-b-2 border-transparent"
            :class="{'text-gray-800 border-blue-500': route().current('tables.index')}"
            :href="route('tables.index', [tournament.id])">

            <span>Tischanordnung</span>
        </inertia-link>
    </template>
</div>
</template>

<script>
export default {
    props : ['tournament'],

    methods: {
        editRoute()
        {
            return this.tournament.mode == 'create' ? route('tournaments.create') : route('tournaments.edit', [this.tournament.id])
        }
    }
}
</script>
