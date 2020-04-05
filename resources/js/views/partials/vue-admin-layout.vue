<template>
<div class="wrap">
	<div class="title">
    	<slot name="title"></slot>
    </div>

    <aside ref="sidebar" class="sidebar h-full">
        <slot name="sidebar"></slot>
    </aside>

    <header class="header">
    	<slot name="header"></slot>
    </header>

    <main ref="main" class="main">
		<slot name="main"></slot>
	</main>
</div>
</template>

<script>
import Scrollbar from 'smooth-scrollbar'

export default {
    mounted()
    {
        Scrollbar.init(this.$refs.sidebar)
        Scrollbar.init(this.$refs.main, { alwaysShowTracks: true })
    }
}
</script>

<style>
.wrap {
	height: 100vh;
	display: grid;
	overflow: hidden;
	grid-template-rows: auto 1fr;
	grid-template-columns: 250px 1fr;
	grid-template-areas:
		"title header"
		"sidebar main";
}

.title {
	grid-area: title;
}

.sidebar {
	grid-area: sidebar;
}

.header {
	z-index: 10;
	grid-area: header;
}

.main {
	grid-area: main;
	overflow: auto;
}
</style>
