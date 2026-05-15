<template>
  <button
    class="chat-toggle"
    @click="$emit('toggle')"
  >
    🤖
  </button>
</template>

<style scoped>
.chat-toggle {
  position: fixed;
  right: 5px;
  bottom: 10px;

  border: none;
  background: transparent;

  font-size: 56px;

  cursor: pointer;

  z-index: 9999;

  transition: .2s;
}

.chat-toggle:hover {
  transform: scale(1.08);
}
</style>