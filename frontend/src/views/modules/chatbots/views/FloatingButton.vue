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

  right: 12px;
  bottom: 12px;

  width: 62px;
  height: 62px;

  border: none;

  border-radius: 50%;

  background: linear-gradient(
    135deg,
    #0099ff,
    #f97316
  );

  color: white;

  font-size: 28px;

  cursor: pointer;

  z-index: 9999;

  box-shadow:
    0 8px 20px rgba(0,0,0,.2);

  transition: .2s;

  display: flex;

  align-items: center;
  justify-content: center;
}

.chat-toggle:hover {
  transform: scale(1.08);
}

@media (max-width: 768px) {

  .chat-toggle {

    width: 58px;
    height: 58px;

    font-size: 26px;
  }
}
</style>