<template>
<div class="nav text-sm pb-16 pt-6">
    <transition-group name="fade">
		<template v-if="$page.t">
            <inertia-link
                key="0"
                class="link pl-8 pr-4 py-4 font-bold flex items-center hover:bg-black-10 hover:text-white"
                :class="{'bg-black-10 text-white orange-inset': route().current('tournaments.edit') || route().current('tables.index'), 'text-blue-200': !route().current('tournaments.edit') && !route().current('tables.index')}"
                :href="route('tournaments.edit', [this.$page.t.id])">

                <icon class="mr-2" icon="cogs" fixed-width></icon>
                <span>Einstellungen</span>
            </inertia-link>

            <div key="1" :class="{ 'bg-black-10 orange-inset': contains('championships'), 'bg-black-10': championshipOpened}">
                <span
                    @click.prevent="championshipOpened = !championshipOpened"
                    class="cursor-pointer pl-8 pr-4 py-4 font-bold flex items-center hover:bg-black-10 hover:text-white"
                    :class="{'text-white': contains('championships'), 'text-blue-200': !contains('championships') }">

                    <icon class="mr-2" icon="clipboard-list" fixed-width></icon>
                    <span>Spielklassen</span>
                    <icon class="ml-auto" :icon="championshipAngle"></icon>
                </span>

                <transition-expand>
                    <div v-show="championshipOpened">
                        <inertia-link
                            class="link pl-16 pr-4 py-4 flex items-center hover:bg-black-10 hover:text-white"
                            :class="{'bg-black-10 text-white': route().current('championships.create', [$page.t.id]), 'text-blue-200': !route().current('championships.create', [$page.t.id])}"
                            :href="route('championships.create', [$page.t.id])">

                            <span>Neue Spielklasse</span>
                        </inertia-link>

                        <inertia-link
                            class="link pl-16 pr-4 py-4 flex items-center hover:bg-black-10 hover:text-white"
                            :class="{'bg-black-10 text-white': route().current('championships.index'), 'text-blue-200': !route().current('championships.index')}"
                            :href="route('championships.index', [$page.t.id])">

                            <span>Übersicht</span>
                        </inertia-link>

                        <div v-if="$page.t.championships.length > 0">
                            <inertia-link
                                v-for="championship in $page.t.championships"
                                :key="championship.id"
                                class="link pl-16 pr-4 py-4 flex items-center hover:bg-black-10 hover:text-white"
                                :class="{'bg-black-10 text-white': contains('championships/' + championship.id), 'text-blue-200': !contains('championships/' + championship.id)}"
                                :href="route('championships.edit', [$page.t.id, championship.id])">

                                <span>{{ championship.name }}</span>
                            </inertia-link>
                        </div>
                    </div>
                </transition-expand>
            </div>

            <div key="2" :class="{ 'bg-black-10 orange-inset': contains('players'), 'bg-black-10': playersOpened}">
                <span
                    @click.prevent="playersOpened = !playersOpened"
                    class="cursor-pointer pl-8 pr-4 py-4 font-bold flex items-center hover:bg-black-10 hover:text-white"
                    :class="{'text-white': contains('players'), 'text-blue-200': !contains('players') }">

                    <icon class="mr-2" icon="users" fixed-width></icon>
                    <span>Spieler</span>
                    <icon class="ml-auto" :icon="playersAngle"></icon>
                </span>

                <transition-expand>
                    <div v-show="playersOpened">
                        <inertia-link
                            class="link pl-16 pr-4 py-4 flex items-center hover:bg-black-10 hover:text-white"
                            :class="{'bg-black-10 text-white': route().current('players.create', [$page.t.id]), 'text-blue-200': !route().current('players.create', [$page.t.id])}"
                            :href="route('players.create', [$page.t.id])">

                            <span>Neuer Spieler</span>
                        </inertia-link>

                        <inertia-link
                            class="link pl-16 pr-4 py-4 flex items-center hover:bg-black-10 hover:text-white"
                            :class="{'bg-black-10 text-white': route().current('players.index'), 'text-blue-200': !route().current('players.index')}"
                            :href="route('players.index', [$page.t.id])">

                            <span>Spielerliste</span>
                        </inertia-link>
                    </div>
                </transition-expand>
            </div>

            <div
                key="3"
                :class="{ 'bg-black-10 orange-inset': contains('schedule'), 'bg-black-10': scheduleOpened}"
                v-if="$page.t.started_championships_count > 0">
                <span
                    @click.prevent="scheduleOpened = !scheduleOpened"
                    class="cursor-pointer pl-8 pr-4 py-4 font-bold flex items-center hover:bg-black-10 hover:text-white"
                    :class="{'text-white': contains('schedule'), 'text-blue-200': !contains('schedule') }">

                    <icon class="mr-2" icon="clock" fixed-width></icon>
                    <span>Spielplan</span>
                    <icon class="ml-auto" :icon="scheduleAngle"></icon>
                </span>

                <transition-expand>
                    <div v-show="scheduleOpened">
                        <inertia-link
                            class="link pl-16 pr-4 py-4 flex items-center hover:bg-black-10 hover:text-white"
                            :class="{'bg-black-10 text-white': route().current('schedule.index'), 'text-blue-200': !route().current('schedule.index')}"
                            :href="route('schedule.index', [$page.t.id]).url()">

                            <span>Turnierübersicht</span>
                        </inertia-link>

                        <div v-if="$page.t.championships.length > 0">
                            <inertia-link
                                v-for="championship in $page.t.championships"
                                :key="championship.id"
                                class="link pl-16 pr-4 py-4 flex items-center hover:bg-black-10 hover:text-white"
                                :class="{'bg-black-10 text-white': contains('schedule/' + championship.id), 'text-blue-200': !contains('schedule/' + championship.id)}"
                                :href="route('schedule.show', [$page.t.id, championship.id])">

                                <span>{{ championship.name }}</span>
                            </inertia-link>
                        </div>
                    </div>
                </transition-expand>
            </div>
		</template>
	</transition-group>
</div>
</template>

<script>
import TransitionExpand from '@/components/transition-expand'

export default {
    components: {
		TransitionExpand
    },

    props: {
        url: String,
    },

    data()
    {
        return {
            championshipOpened: false,
            playersOpened: false,
            scheduleOpened: false
        }
    },

    computed: {
        championshipAngle()
		{
			return this.championshipOpened ? 'angle-up': 'angle-down'
        },

        playersAngle()
		{
			return this.playersOpened ? 'angle-up': 'angle-down'
        },

        scheduleAngle()
		{
			return this.scheduleOpened ? 'angle-up': 'angle-down'
        },

        isChampionshipsRoute()
        {
            return this.contains('championships')
        }
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

        setOpened(route)
        {
			this.championshipOpened = this.contains(['/championships'])
			this.playersOpened = this.contains(['/players'])
			this.scheduleOpened = this.contains(['/schedule'])
        }
    },

    created()
	{
		this.setOpened()
	}
}
</script>
