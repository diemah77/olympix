<template>
<div class="flex border-b border-gray-300">
    <inertia-link
        v-for="p in $page.championship.phases"
        :key="p.id"
        class="link px-6 py-3 flex items-center text-sm text-gray-600 hover:text-gray-800 border-b-2 border-transparent"
        :class="{'text-gray-800 border-blue-500': p.id == $page.phase.id}"
        :href="route('results.show', [$page.tournament.hash, $page.championship.slug, p.id])">

        <span>{{ p.name }}</span>
    </inertia-link>
</div>
</template>
