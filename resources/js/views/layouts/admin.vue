<template>
<admin-layout>
	<template slot="title">
		<h1 class="flex h-full items-center pl-6 text-xl text-blue-500 font-thin uppercase bg-black-10">
            <icon class="mr-2" icon="trophy" fixed-width></icon>
            {{ $page.t ? $page.t.name : 'Olympix' }}
        </h1>
	</template>

	<template slot="sidebar">
		<admin-nav :url="url()"></admin-nav>
	</template>

	<div slot="header" class="flex items-center justify-between shadow bg-white px-4 py-4">
		<div>
			<h1 class="font-normal text-xl m-0">{{ title }}</h1>

			<breadcrumbs :breadcrumbs="$page.breadcrumbs"></breadcrumbs>
		</div>

		<admin-sidebar :auth="$page.auth"></admin-sidebar>
	</div>

	<div slot="main" id="content" class="pt-8 px-4 pb-16">
		<transition name="fade" mode="out-in">
			<slot />
		</transition>
	</div>
</admin-layout>
</template>

<script>
import AdminLayout from '@/views/partials/vue-admin-layout.vue'
import AdminSidebar from '@/views/partials/admin-sidebar.vue'
import AdminNav from '@/views/partials/admin-nav.vue'
import Breadcrumbs from '@/components/breadcrumbs'

export default {
	props: {
		title: {
			type: String,
			default: 'Meine Turniere'
        },
	},

	components: {
		AdminLayout,
		AdminNav,
		AdminSidebar,
		Breadcrumbs
    },

    methods: {
        url() {
            return location.pathname.substr(1)
        }
    }
}
</script>
