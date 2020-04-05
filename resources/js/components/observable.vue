<template>
  <div>
    <slot :width="width" :height="height"></slot>
  </div>
</template>

<script>
export default {
  data() {
    return {
      width: null,
      height: null,
    }
  },
  mounted() {
    const observer = new ResizeObserver(([entry]) => {
      this.width = entry.contentRect.width
      this.height = entry.contentRect.height
    })
    observer.observe(this.$el)
  }
}
</script>
