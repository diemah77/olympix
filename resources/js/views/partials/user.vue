<template>
<div class="flex items-center mr-8">
	<div class="pr-3">
		<user-avatar :image="auth.avatar" size="small"></user-avatar>
	</div>

    <el-dropdown class="ml-auto text-base" trigger="click" @command="visit">
		<div class="flex items-center leading-none">
			<span class="mr-1">{{ auth.name }}</span>
			<icon icon="chevron-down"></icon>
		</div>

		<el-dropdown-menu slot="dropdown">
			<!-- <el-dropdown-item :command="route('profile.show')">
				Mein Profil
			</el-dropdown-item> -->

			<el-dropdown-item :command="route('logout')">
				Logout
			</el-dropdown-item>
		</el-dropdown-menu>
	</el-dropdown>
</div>
</template>

<script>
import UserAvatar from '@/components/user-avatar.vue'

export default {
	props: {
		auth: {
			type: Object,
			required: true
		}
	},

	components: {
		UserAvatar
	},

	methods: {
		visit(command)
		{
			if (command.name == 'logout')
			{
				return window.location.href = '/logout'
			}

			this.$inertia.visit(command)
		}
	}
}
</script>
