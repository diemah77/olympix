<template>
<page width="full" :title="championship.name">
    <schedule-nav class="mb-6" :championship="championship"></schedule-nav>

    <box>
        <transition name="fade" mode="out-in">
			<slot />
		</transition>
    </box>
</page>
</template>

<script>
import ScheduleNav from '@/views/partials/schedule-nav'

export default {
    props : ['championship'],

    components: {
        ScheduleNav
    }
}
</script>
