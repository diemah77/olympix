<template>
<div class="flex border-b border-gray-300">
    <inertia-link
		class="link px-6 py-3 flex items-center text-sm text-gray-600 hover:text-gray-800 border-b-2 border-transparent"
		:class="{'text-gray-800 border-blue-500': route().current('championships.edit') || route().current('championships.create')}"
		:href="editRoute()">

        <span>Grunddaten</span>
	</inertia-link>

    <template v-if="championship.mode != 'create'">
        <inertia-link
            class="link px-6 py-3 flex items-center text-sm text-gray-600 hover:text-gray-800 border-b-2 border-transparent"
            :class="{'text-gray-800 border-blue-500': route().current('championship-players')}"
            :href="route('championship-players', [$page.t.id, championship.id])">

            <span>Gemeldete Spieler</span>
        </inertia-link>

        <inertia-link
            v-if="$page.c.isDoubles"
            class="link px-6 py-3 flex items-center text-sm text-gray-600 hover:text-gray-800 border-b-2 border-transparent"
            :class="{'text-gray-800 border-blue-500': route().current('championship-doubles')}"
            :href="route('championship-doubles', [$page.t.id, championship.id])">

            <span>Doppelpaarungen</span>
        </inertia-link>

        <inertia-link
            class="link px-6 py-3 flex items-center text-sm text-gray-600 hover:text-gray-800 border-b-2 border-transparent"
            :class="{'text-gray-800 border-blue-500': route().current('championship-seeding')}"
            :href="route('championship-seeding', [$page.t.id, championship.id])">

            <span>Setzliste</span>
        </inertia-link>

        <inertia-link
            v-for="phase in championship.phases"
            :key="phase.id"
            class="link px-6 py-3 flex items-center text-sm text-gray-600 hover:text-gray-800 border-b-2 border-transparent"
            :class="{'text-gray-800 border-blue-500': phase.isCurrent}"
            :href="route('phases.edit', [$page.t.id, championship.id, phase.id])">

            <span>{{ phase.name }}</span>
        </inertia-link>
    </template>
</div>
</template>

<script>
export default {
    props : ['championship'],

    methods: {
        editRoute()
        {
            return this.championship.mode == 'create' ? route('championships.create', this.$page.t.id) : route('championships.edit', [this.$page.t.id, this.championship.id])
        }
    }
}
</script>
