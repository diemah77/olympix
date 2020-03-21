<template>
<div class="">
    <!-- <brackets
        v-if="phase.rounds.length > 0"
        :rounds="phase.rounds">
    </brackets> -->

    <brackets-slider
        v-if="phase.rounds.length > 0"
        :rounds="phase.rounds">
    </brackets-slider>

    <el-alert
        v-else
        type="info"
        :closable="false"
        show-icon>

        Noch nicht ausgelost
    </el-alert>
</div>
</template>

<script>
import admin from '@/views/layouts/admin'
import schedule from '@/views/layouts/schedule'
import Brackets from '@/components/brackets'
import BracketsSlider from '@/components/bracketsSlider'

export default {
     props : {
        championship: Object,
        phase: Object
    },

    components: {
        Brackets,
        BracketsSlider
    },

    layout: (h, page) => {
        return h(admin, {
            props: {title: page.data.props.championship.name}
        } , [
            h(schedule, {props: {championship: page.data.props.championship}}, [page]),
        ])
    }
}
</script>
