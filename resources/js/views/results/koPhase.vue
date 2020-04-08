<template>
<div class="pb-16">
    <observable v-slot="{ width }">
        <brackets-slider
            v-if="phase.rounds.length > 0"
            :rounds="phase.rounds"
            :width="width">
        </brackets-slider>
    </observable>
</div>
</template>

<script>
import front from '@/views/layouts/front'
import results from '@/views/layouts/results'
import Brackets from '@/components/brackets'
import BracketsSlider from '@/components/bracketsSlider'
import Observable from '@/components/observable'

export default {
     props : {
        phase: Object
    },

    components: {
        Brackets,
        BracketsSlider,
        Observable
    },

    layout: (h, page) => {
        return h(front, {} , [
            h(results, {}, [page]),
        ])
    }
}
</script>
