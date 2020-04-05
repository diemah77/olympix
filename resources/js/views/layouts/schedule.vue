<template>
<page width="full" title="Turnierübersicht">
    <schedule-nav class="mb-6"></schedule-nav>

    <transition name="fade" mode="out-in">
        <slot />
    </transition>
</page>
</template>

<script>
import ScheduleNav from '@/views/partials/schedule-nav'

export default {
    components: {
        ScheduleNav
    }
}
</script>
