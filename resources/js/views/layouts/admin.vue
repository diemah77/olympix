<template>
<admin-layout>
	<template slot="title">
		<h1 class="flex h-full items-center pl-2 text-xl text-blue-500 font-thin uppercase bg-black-10">
            <img class="w-8 mr-2" src="/img/olympix.png">
            <span>{{ $page.t ? $page.t.name : 'Olympix' }}</span>
        </h1>
	</template>

	<template slot="sidebar">
		<admin-nav :url="url()"></admin-nav>
	</template>

	<div slot="header" class="flex items-center shadow bg-white px-4 py-2">
        <transition name="fade">
            <inertia-link
                v-if="$page.t"
                class="link flex items-center text-sm text-blue-500 hover:text-blue-400 hover:underline"
                :href="route('tournaments.index')">

                <icon class="mr-1" icon="long-arrow-alt-left" fixed-width></icon>
                <span>Zu meinen Turnieren</span>
            </inertia-link>
        </transition>

		<user class="ml-auto" :auth="$page.auth"></user>
	</div>

	<div slot="main" id="content" class="pt-10 px-4 pb-16">
		<transition name="fade" mode="out-in">
			<slot />
		</transition>
	</div>
</admin-layout>
</template>

<script>
import AdminLayout from '@/views/partials/vue-admin-layout.vue'
import User from '@/views/partials/user.vue'
import AdminNav from '@/views/partials/admin-nav.vue'

export default {
	components: {
		AdminLayout,
		AdminNav,
		User
    },

    methods: {
        url() {
            return location.pathname.substr(1)
        }
    }
}
</script>
