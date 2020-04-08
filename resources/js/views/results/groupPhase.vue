<template>
<div class="pb-16">
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
import front from '@/views/layouts/front'
import results from '@/views/layouts/results'
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
        return h(front, {} , [
            h(results, {}, [page]),
        ])
    }
}
</script>
