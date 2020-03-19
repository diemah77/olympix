<template>
<div class="">
	<el-table
		:ref="'standing_' + name"
	    :data="standings"
	    empty-text="Keine Daten">

	    <el-table-column
            label="Name"
            property="name">

            <template slot-scope="scope">
                <span class="whitespace-no-wrap">{{ scope.row.name }}</span>
            </template>
	    </el-table-column>

	    <el-table-column
	        label="Spiele"
	        property="matches"
            label-class-name="text-center"
            class-name="text-center"
            width="60">
	    </el-table-column>

	    <el-table-column
	        label="Sätze"
	        property="sets"
            label-class-name="text-center"
            class-name="text-center"
            width="60">
	    </el-table-column>

	    <el-table-column
	        label="Bälle"
	        property="points"
            label-class-name="text-center"
            class-name="text-center"
            width="60">
	    </el-table-column>
	</el-table>
</div>
</template>

<script>
export default {
	props: {
		standings: {
			type: Array,
			required: true
		},

		name: {
			type: String,
			required: false
		}
	}
}
</script>
