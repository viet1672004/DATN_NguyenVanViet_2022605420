<template>
  <div>

    <!-- BUTTON -->
    <FloatingButton @toggle="open = !open" />

    <!-- CHATBOX -->
    <div
      v-if="open"
      class="chatbox"
    >

      <!-- HEADER -->
      <div class="chat-header">

        <div class="bot-info">

          <div class="avatar">
            🤖
          </div>

          <div>
            <div class="name">
              FunTicket AI
            </div>

            <div class="status">
              Đang hoạt động
            </div>
          </div>

        </div>

        <!-- ACTION -->
        <div class="header-actions">

          <!-- NEW CHAT -->
          <button
            class="icon-btn"
            @click="handleNewChat"
            title="Cuộc trò chuyện mới"
          >
            +
          </button>

          <!-- MINIMIZE -->
          <button
            class="icon-btn"
            @click="open = false"
          >
            _
          </button>

          <!-- CLOSE -->
          <button
            class="icon-btn close"
            @click="handleClose"
          >
            ✕
          </button>

        </div>

      </div>

      <!-- BODY -->
      <div
        class="chat-body"
        ref="chatBodyRef"
      >

        <div
          v-for="(item, index) in store.messages"
          :key="index"
          class="message"
          :class="item.type"
        >
          {{ item.text }}
        </div>

      </div>

      <!-- INPUT -->
      <div class="chat-input">

        <input
          v-model="message"
          type="text"
          placeholder="Nhập tin nhắn..."
          @keyup.enter="handleSend"
        />

        <button
          @click="handleSend"
          :disabled="store.loading"
        >
          Gửi
        </button>

      </div>

    </div>

  </div>
</template>

<script setup>
import {
  ref,
  onMounted,
  nextTick,
  watch,
  computed,
} from "vue";

import FloatingButton from "./FloatingButton.vue";

import { useChatBotStore } from "../provider/store";

import { useAuthStore } from "@/views/modules/auths/provider/store";

const store = useChatBotStore();

const authStore = useAuthStore();

const open = ref(false);

const message = ref("");

const chatBodyRef = ref(null);

const defaultMessage = computed(() => ({
  type: "bot",
  text: `Xin chào ${
    authStore.user?.Name || "bạn"
  } 👋 Tôi có thể giúp gì cho bạn?`,
}));

/* SCROLL */
const scrollToBottom = async () => {

  await nextTick();

  if (chatBodyRef.value) {

    chatBodyRef.value.scrollTop =
      chatBodyRef.value.scrollHeight;

  }

};

/* SEND */
const handleSend = async () => {

  if (!message.value.trim()) return;

  const text = message.value;

  message.value = "";

  await store.sendMessage(text);

  scrollToBottom();

};

/* CLOSE */
const handleClose = () => {

  open.value = false;

  message.value = "";

};

/* NEW CHAT */
const handleNewChat = () => {

  store.messages = [
    defaultMessage.value,
  ];

  scrollToBottom();

};

/* AUTO SCROLL */
watch(
  () => store.messages.length,
  () => {
    scrollToBottom();
  }
);

watch(
  () => authStore.user?.Name,
  (newName) => {

    if (!newName) return;

    const firstMessage = store.messages[0];

    if (firstMessage?.type === "bot") {

      firstMessage.text =
        `Xin chào ${newName} 👋 Tôi có thể giúp gì cho bạn?`;

    }

  }
);

onMounted(() => {

  if (!store.messages.length) {

    store.messages.push(defaultMessage.value);

  }

  scrollToBottom();

});
</script>

<style scoped>
.chatbox {
  position: fixed;
  right: 18px;
  bottom: 78px;

  width: 300px;
  height: 430px;

  background: #ffffff;

  border-radius: 14px;

  overflow: hidden;

  display: flex;
  flex-direction: column;

  box-shadow: 0 10px 28px rgba(0, 0, 0, 0.14);

  z-index: 9999;

  font-family: system-ui, -apple-system, BlinkMacSystemFont,
    "Segoe UI", Roboto, sans-serif;
}

/* HEADER */
.chat-header {
  height: 56px;
  padding: 0 14px;

  background: linear-gradient(
    90deg,
    #0099FF,
    #f97316
  );

  color: #ffffff;

  display: flex;
  align-items: center;
  justify-content: space-between;

  flex-shrink: 0;
}

.bot-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.avatar {
  font-size: 20px;

  background: rgba(255, 255, 255, 0.2);

  width: 32px;
  height: 32px;

  border-radius: 50%;

  display: flex;
  align-items: center;
  justify-content: center;
}

.name {
  font-size: 14px;
  font-weight: 600;
}

.status {
  font-size: 10px;
  opacity: 0.85;

  display: flex;
  align-items: center;
  gap: 4px;
}

.status::before {
  content: "";

  width: 6px;
  height: 6px;

  background: #4ade80;

  border-radius: 50%;
}

/* ACTION */
.header-actions {
  display: flex;
  align-items: center;
  gap: 6px;
}

.icon-btn {
  width: 24px;
  height: 24px;

  border: none;

  background: rgba(255,255,255,.15);

  color: white;

  border-radius: 6px;

  cursor: pointer;

  font-size: 12px;

  display: flex;
  align-items: center;
  justify-content: center;

  transition: .2s;
}

.icon-btn:hover {
  background: rgba(255,255,255,.3);
}

.close:hover {
  background: #ef4444;
}

/* BODY */
.chat-body {
  flex: 1;

  padding: 12px;

  overflow-y: auto;
  overflow-x: hidden;

  background: #f8fafc;

  display: flex;
  flex-direction: column;

  gap: 10px;

  scroll-behavior: smooth;
}

.chat-body::-webkit-scrollbar {
  width: 8px;
}

.chat-body::-webkit-scrollbar-thumb {
  background: #a0a1a2;
  border-radius: 10px;
}

.chat-body::-webkit-scrollbar-thumb:hover {
  background: #888787;
}

.message {
  max-width: 78%;

  padding: 8px 12px;

  border-radius: 14px;

  line-height: 1.4;

  font-size: 13px;

  word-wrap: break-word;
}

.message.bot {
  background: #ffffff;

  color: #1e293b;

  align-self: flex-start;

  border-bottom-left-radius: 4px;

  border: 1px solid #e2e8f0;

  transition: .2s;
}

.message.bot:hover {
  background: #f8fafc;
}

.message.user {
  background: #0088cc;

  color: #ffffff;

  align-self: flex-end;

  border-bottom-right-radius: 4px;

  transition: .2s;
}

.message.user:hover {
  background: #0077b6;
}

/* INPUT */
.chat-input {
  padding: 10px;

  display: flex;
  gap: 6px;

  border-top: 1px solid #f1f5f9;

  background: #ffffff;

  flex-shrink: 0;
}

.chat-input input {
  flex: 1;

  height: 36px;

  border: 1px solid #cbd5e1;

  border-radius: 8px;

  padding: 0 12px;

  outline: none;

  font-size: 13px;

  transition: .2s;
}

.chat-input input:focus {
  border-color: #0088cc;

  box-shadow: 0 0 0 2px rgba(0,136,204,.12);
}

.chat-input button {
  border: none;

  padding: 0 14px;

  border-radius: 8px;

  background: #0088cc;

  color: white;

  font-size: 13px;

  font-weight: 600;

  cursor: pointer;

  transition: .2s;
}

.chat-input button:hover {
  background: #0066cc;
}

.chat-input button:disabled {
  background: #cbd5e1;

  cursor: not-allowed;
}
</style>