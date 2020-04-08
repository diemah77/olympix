<template>
<page :centered="false" width="max-xl" :title="$page.championship.name">
    <results-nav class="mb-8 -mt-4 md:mt-0"></results-nav>

    <transition name="fade" mode="out-in">
        <slot />
    </transition>
</page>
</template>

<script>
import ResultsNav from '@/views/partials/results-nav'

export default {
    components: {
        ResultsNav
    }
}
</script>
