<template>
<page title="Spielklassen" width="default">
    <template slot="buttons">
        <el-button type="primary" @click="$inertia.visit(route('championships.create', [$page.t.id]))">
            <icon class="mr-1" icon="plus" fixed-width></icon>
            <span>Neue Klasse erstellen</span>
        </el-button>
    </template>

    <box>
        <el-table
            class="mb-4"
            :data="paginatedChampionships"
            empty-text="Keine Klasse erfasst">

            <el-table-column
                label="Name">

                <el-button
                    slot-scope="scope"
                    @click="$inertia.visit(route('championships.edit', [$page.t.id, scope.row.id]))"
                    type="text">

                    {{ scope.row.name }}
                </el-button>
            </el-table-column>

            <el-table-column
                label="Typ"
                property="typeName">
            </el-table-column>

            <el-table-column
                label="Spielsystem"
                property="systemName">
            </el-table-column>

            <el-table-column
                label="Gemeldete Spieler"
                class-name="text-center"
                label-class-name="text-center"
                property="registrations_count">
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
                    <el-button type="info" plain @click="$inertia.visit(route('championships.edit', [$page.t.id, scope.row.id]).url())">
                        <icon icon="eye" fixed-width></icon>
                    </el-button>

                    <el-button type="danger" plain @click="deleteChampionship(scope.row.id)">
                        <icon icon="trash" fixed-width></icon>
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
    </box>
</page>
</template>

<script>
import admin from '@/views/layouts/admin'

export default {
	props: {
	    championships: Array,
    },

    layout: (h, page) => h(admin, [page]),

    data()
    {
        return {
            perPage: 10,
            currentPage: 1
        }
    },

    computed: {
        paginatedChampionships()
        {
            return this.championships.slice(Math.max(this.currentPage - 1, 0) * this.perPage, Math.max(this.currentPage, 1) * this.perPage)
		},

		pageCount()
		{
			return Math.ceil(this.championships.length / this.perPage)
		}
    },

    methods: {
        goToPage(page)
		{
			this.currentPage = page
        },

        deleteChampionship(id)
        {
            this.$confirm('Soll die Spielklasse gelöscht werden?', 'Achtung', {
                confirmButtonText: 'Löschen',
                cancelButtonText: 'Abbrechen',
                type: 'warning'
            })
            .then(() => {
                axios.delete(route('championships.destroy', [this.$page.t.id, id]).url()).then(() => this.$inertia.reload())
            })
            .catch(() => {

            })
        }
    }
}
</script>
