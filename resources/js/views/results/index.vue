<template>
<page title="Meine Turniere" width="default">
    <!-- <template slot="buttons">
        <el-button type="primary" @click="$inertia.visit(route('tournaments.create'))">
            <icon class="mr-1" icon="plus" fixed-width></icon>
            <span>Neues Turnier starten</span>
        </el-button>
    </template>

    <box>
        <el-table
            class="mb-4"
            :data="paginatedTournaments"
            empty-text="Keine Turniere erfasst">

            <el-table-column
                label="Name">

                <el-button
                    slot-scope="scope"
                    @click="$inertia.visit(route('tournaments.edit', scope.row.id))"
                    type="text">

                    {{ scope.row.name }}
                </el-button>
            </el-table-column>

            <el-table-column
                label="Erstellte Klassen"
                class-name="text-center"
                label-class-name="text-center"
                property="championships_count">
            </el-table-column>

            <el-table-column
                label="Anzahl Tische"
                class-name="text-center"
                label-class-name="text-center"
                property="tables_count">
            </el-table-column>

            <el-table-column
                label="Status"
                width="140">

                <status slot-scope="scope" :status="scope.row.statusName"></status>
            </el-table-column>

            <el-table-column
                label-class-name="text-center"
                class-name="text-center"
                label="Aktionen">

                <template slot-scope="scope">
                    <el-button type="info" plain @click="$inertia.visit(route('tournaments.edit', scope.row.id))">
                        <icon icon="edit" fixed-width></icon>
                    </el-button>
                </template>
            </el-table-column>
        </el-table>

        <el-pagination
            background
            layout="prev, pager, next"
            :page-count="pageCount"
            @current-change="goToPage">
        </el-pagination>
    </box> -->
    Test
</page>
</template>

<script>
import front from '@/views/layouts/front'

export default {
	props: {
	    tournaments: Array,
    },

    layout: (h, page) => h(front, [page]),

    data()
    {
        return {
            perPage: 10,
            currentPage: 1
        }
    },

    computed: {
        // paginatedTournaments()
        // {
        //     return this.tournaments.slice(Math.max(this.currentPage - 1, 0) * this.perPage, Math.max(this.currentPage, 1) * this.perPage)
		// },

		// pageCount()
		// {
		// 	return Math.ceil(this.tournaments.length / this.perPage)
		// }
    },

    methods: {
        goToPage(page)
		{
			this.currentPage = page
		}
    }
}
</script>
