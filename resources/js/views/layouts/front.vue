<template>
<div class="wrap bg-white">
    <header class="bar shadow-md py-3 px-4">
        <div class="flex">
            <inertia-link :href="$page.tournament.results_route">
                <h1 class="flex h-full items-center text-lg sm:text-xl font-bold">
                    <img class="w-8 mr-2" src="/img/olympix.png">
                    <span>{{ $page.tournament.name }}</span>
                </h1>
            </inertia-link>

            <button @click="toggle" class="xl:hidden ml-auto inline-flex items-center text-gray-600 p-2 -mr-2">
                <icon :icon="icon" class="w-5 h-5"></icon>
            </button>
        </div>

        <div class="py-4 pl-4" v-show="isOpen">
			<div v-for="championship in $page.championships" class="py-2 truncate" :key="championship.id">
                <inertia-link
                    @click="hide()"
                    class="link font-medium hover:text-blue-500"
                    :class="{'text-blue-500': contains(championship.results_route)}"
                    :href="championship.results_route">

                    {{ championship.name }}
                </inertia-link>
            </div>
		</div>
    </header>

    <aside ref="aside" class="aside h-full hidden xl:block">
        <div class="mt-6 ml-6">
            <h2 class="uppercase text-sm font-bold text-gray-500 mb-3">Spielklassen</h2>

            <div v-for="championship in $page.championships" class="py-2 truncate" :key="championship.id">
                <inertia-link
                    class="link font-medium hover:text-blue-500"
                    :class="{'text-blue-500': contains(championship.results_route)}"
                    :href="championship.results_route">

                    {{ championship.name }}
                </inertia-link>
            </div>
        </div>
    </aside>

    <main ref="main" class="main px-4 md:px-6 pt-4 md:pt-6">
		<slot />
	</main>
</div>
</template>

<script>
import Scrollbar from 'smooth-scrollbar'

export default {
    data()
    {
        return {
            isOpen: false
        }
    },

    mounted()
    {
        Scrollbar.init(this.$refs.aside)
        Scrollbar.init(this.$refs.main)
    },

    computed: {
        icon()
  		{
  			return this.isOpen ? 'times' : 'bars'
  		}
    },

    methods: {
        hide()
        {
            this.$nextTick(() => this.isOpen = false)
        },

        toggle()
  		{
  			this.isOpen = !this.isOpen
          },

        contains(matches)
		{
			const paths = Array.isArray(matches) ? matches : [matches]

		    return paths.some(path =>
		    {
		    	return location.href.indexOf(path) > -1
		    })
		},
    }
}
</script>

<style>
.wrap {
	height: 100vh;
	display: grid;
	overflow: hidden;
	grid-template-rows: auto 1fr;
	grid-template-columns: 1fr;
	grid-template-areas:
		"bar"
        "main";
}

@screen xl {
    .wrap {
        grid-template-columns: 250px 1fr;
        grid-template-areas:
            "bar bar"
            "aside main";
    }
}

.aside {
	grid-area: aside;
}

.bar {
	z-index: 20;
    grid-area: bar;
    width: 100vw;
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
}

.main {
	grid-area: main;
	overflow: auto;
}
</style>
