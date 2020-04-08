<template>
<div>
	<div v-for="(group, index) in groups" :class="{'mb-16': index < groups.length - 1}" :key="group.id">
        <div class="row">
            <div class="col-8">
                <h1 class="text-xl pb-2">{{ group.name }}</h1>

                <group-matrix
                    :rows="group.matchGrid">
                </group-matrix>
            </div>

            <div class="col-4">
                <h1 class="text-xl pb-2">Tabelle</h1>

                <group-standing
                    :standings="group.standings"
                    :name="group.name">
                </group-standing>
            </div>
        </div>
	</div>
</div>
</template>

<script>
import groupStanding from '@/components/groupStanding'
import groupMatrix from '@/components/groupMatrix'

export default {
	props: {
		groups: {
			type: Array,
			required: true
        }
	},

	components: {
		groupStanding,
		groupMatrix
	}
}
</script>
