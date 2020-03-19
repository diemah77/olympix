<template>
<div class="">
    <brackets
        v-if="phase.rounds.length > 0"
        :rounds="phase.rounds">
    </brackets>

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

export default {
     props : {
        championship: Object,
        phase: Object
    },

    components: {
        Brackets
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
