<template>
<div class="">
    <group-standings
        :groups="phase.groups"
        v-if="phase.groups.length > 0">
    </group-standings>

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
import GroupStandings from '@/components/groupStandings'

export default {
     props : {
        championship: Object,
        phase: Object
    },

    components: {
        GroupStandings
    },

    layout: (h, page) => {
        return h(admin, {} , [
            h(schedule, {props: {championship: page.data.props.championship}}, [page]),
        ])
    }
}
</script>
