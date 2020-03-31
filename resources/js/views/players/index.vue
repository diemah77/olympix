<template>
<page width="default" title="Spielerliste">
    <template slot="buttons">
        <el-alert
            class="mb-4"
            effect="dark"
            v-if="this.has('players')"
            :title="this.get('players')"
            type="error">
        </el-alert>

        <el-button type="primary" @click="$inertia.visit(route('players.create', [$page.t.id]))">
            <icon class="mr-1" icon="plus" fixed-width></icon>
            <span>Spieler erfassen</span>
        </el-button>

        <el-button type="info" @click="selectFile()">
            <icon class="mr-1" icon="cloud-upload-alt" fixed-width></icon>
            <span>Spieler aus Excel importieren</span>
        </el-button>

        <input @change="uploadFile()" ref="uploadInput" type="file" v-show="false">

        <el-popover
            placement="right"
            trigger="click"
            v-model="showPopover"
            @hide="newPlayers = 0">

            <div class="flex items-center">
                <el-input class="w-16" v-model="newPlayers"></el-input>
                <span class="ml-3 mr-8">Zufallsspieler</span>
                <el-button
                    type="success"
                    @click="addRandomPlayers">

                    Hinzufügen
                </el-button>
            </div>

            <icon class="cursor-pointer text-orange-400 ml-3" slot="reference" icon="lightbulb" fixed-width></icon>
        </el-popover>
    </template>

    <box>
        <div class="flex justify-between items-center pb-4 mb-4 border-b border-gray-200">
            <el-input
                class="w-2/5"
                placeholder="Suchen..."
                clearable
                v-model="filterKey">
            </el-input>

            <span>{{ sortedPlayers.length }} Spieler erfasst</span>
        </div>

        <el-table
            ref="playerTable"
            :data="sortedPlayers"
            empty-text="Keine Spieler erfasst"
            @sort-change="sortPlayers">

            <el-table-column
                type="index"
                width="50">
            </el-table-column>

            <el-table-column
                label="Name"
                sortable
                property="fullname">

                <el-button slot-scope="scope" type="text" class="p-0" @click="$inertia.visit(route('players.edit', [$page.t.id, scope.row.id]))">
                    <span>{{ scope.row.fullname }}</span>
                </el-button>
            </el-table-column>

            <el-table-column
                label="TTR-Punkte"
                sortable
                property="ttr">
            </el-table-column>

            <el-table-column
                label-class-name="text-center"
                class-name="text-center">

                <template slot-scope="scope">
                    <el-button type="info" plain @click="$inertia.visit(route('players.edit', [$page.t.id, scope.row.id]))">
                        <icon icon="eye" fixed-width></icon>
                    </el-button>
                </template>
            </el-table-column>
        </el-table>
    </box>
</page>
</template>

<script>
import sort from 'fast-sort'
import admin from '@/views/layouts/admin'
import validation from '@/mixins/validation'

export default {
    props: {
	    initialPlayers: Array
    },

    mixins: [validation],

    layout: (h, page) => h(admin, [page]),

	data()
	{
		return {
            players: this.initialPlayers,
            filterKey: '',
            sortCol: 'fullname',
            sortDir: 'asc',
            newPlayers: 0,
            showPopover: false
		}
	},

	computed: {
        filteredPlayers()
        {
            return this.filterKey == '' ? this.players : this.players.filter(player => player.fullname.toLowerCase().indexOf(this.filterKey) > -1)
        },

		sortedPlayers()
		{
            const dir = this.sortDir
            const col = this.sortCol
            const obj = {}
            obj[dir] = col

            return sort(this.filteredPlayers).by([obj])
		}
	},

	methods: {
        sort: sort,

        uploadFile()
        {
            let data = new FormData();
            let file = this.$refs.uploadInput.files[0]

            data.append('players', file)

            axios.post(route('players.import', [this.$page.t.id]).url(), data).then(response =>
            {
                this.$message.success('Spieler importiert!')
                this.$inertia.reload()
                this.clear()
            })
            .catch(errors => {
                this.errors = errors.response.data
            })

            this.$refs.uploadInput.value = ''
        },

        selectFile()
        {
            return this.$refs.uploadInput.click()
        },

        addRandomPlayers()
        {
            if (this.newPlayers == 0) return this.showPopover = false

            axios.post(route('players.random', [this.$page.t.id]).url(), {count: this.newPlayers}).then(response =>
            {
                response.data.forEach(p => this.players.push(p))
                this.showPopover = false
            })
        },

        sortPlayers(data)
        {
            this.sortCol = data.prop
            this.sortDir = data.order == 'ascending' ? 'asc' : 'desc'
        }
	}
}
</script>
