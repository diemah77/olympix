<template>
    <el-table
        class="w-100"
        :data="rows"
        :show-header="false"
        :cell-class-name="className">

        <el-table-column v-for="row in rows.length" :key="row">
            <template slot-scope="scope">
                <span class="whitespace-no-wrap">{{ scope.row["col_" + (row-1)].label }}</span>
            </template>
        </el-table-column>
    </el-table>
</template>

<script>
export default {
	props: {
		rows: {
			type: Array,
			required: true
		}
	},

	methods: {
        className({row, column, rowIndex, columnIndex})
        {
            if (rowIndex == 0 || columnIndex == 0)
            {
                return 'font-bold text-gray-800'
            }

            const key = 'col_' + columnIndex

            return row[key].result_cell ? 'text-center' : 'bg-gray-200'
        }
	}
}
</script>
