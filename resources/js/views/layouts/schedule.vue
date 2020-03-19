<template>
<div>
    <schedule-nav class="mb-6" :championship="championship"></schedule-nav>

    <box>
        <transition name="fade" mode="out-in">
			<slot />
		</transition>
    </box>
</div>
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
