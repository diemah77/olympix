<template>
<h1 class="m-auto text-xl mt-8">{{ $page.msg }}</h1>
</template>

<script>
import front from '@/views/layouts/front'

export default {
    layout: (h, page) => h(front, [page])
}
</script>
