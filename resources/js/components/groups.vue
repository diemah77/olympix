<template>
<div class="flex flex-wrap -mx-4">
	<div v-for="group in groups" class="w-1/2 px-4 pb-8" :key="group.id">
        <div class="border border-gray-200 p-4">
            <h1 class="text-base pb-2">{{ group.name }}</h1>

            <el-table
                class="w-100"
                :data="group.standings"
                :show-header="false"
                empty-text="Keine Daten">

                <el-table-column
                    label="Name"
                    property="name">
                </el-table-column>
            </el-table>
        </div>
	</div>
</div>
</template>

<script>
export default {
	props: {
		groups: {
			type: Array,
			required: true
		}
    }
}
</script>
