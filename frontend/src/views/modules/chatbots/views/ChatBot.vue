<template>
  <div>

    <!-- BUTTON -->
    <FloatingButton
      @toggle="open = !open"
    />

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
            title="Cuộc trò chuyện mới"
            @click="handleNewChat"
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
        ref="chatBodyRef"
        class="chat-body"
      >

        <div
          v-for="(item, index) in store.messages"
          :key="index"
          class="message"
          :class="item.type"
        >
          {{ item.text }}
        </div>

        <!-- LOADING -->
        <div
          v-if="store.loading"
          class="message bot typing"
        >
          Đang trả lời...
        </div>

      </div>

      <!-- INPUT -->
      <div class="chat-input">

        <input
          ref="inputRef"
          v-model="message"
          type="text"
          placeholder="Nhập tin nhắn..."
          @keyup.enter="handleSend"
        />

        <button
          :disabled="
            store.loading ||
            !message.trim()
          "
          @click="handleSend"
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
  watch,
  computed,
  onMounted,
  nextTick,
} from "vue";

import FloatingButton from "./FloatingButton.vue";

import { useChatBotStore } from "../provider/store";

import { useAuthStore } from "@/views/modules/auths/provider/store";

const store = useChatBotStore();

const authStore = useAuthStore();

const open = ref(false);

const message = ref("");

const inputRef = ref(null);

const chatBodyRef = ref(null);

/*
|--------------------------------------------------------------------------
| DEFAULT MESSAGE
|--------------------------------------------------------------------------
*/

const defaultMessage = computed(() => ({

  type: "bot",

  text: `Xin chào ${
    authStore.user?.Name || "bạn"
  } 👋 Tôi có thể giúp gì cho bạn?`,
}));

/*
|--------------------------------------------------------------------------
| SCROLL
|--------------------------------------------------------------------------
*/

const scrollToBottom = async () => {

  await nextTick();

  if (!chatBodyRef.value) {
    return;
  }

  chatBodyRef.value.scrollTop =
    chatBodyRef.value.scrollHeight;
};

/*
|--------------------------------------------------------------------------
| SEND
|--------------------------------------------------------------------------
*/

const handleSend = async () => {

  /*
  |--------------------------------------------------------------------------
  | EMPTY
  |--------------------------------------------------------------------------
  */

  if (!message.value.trim()) {
    return;
  }

  /*
  |--------------------------------------------------------------------------
  | LOADING
  |--------------------------------------------------------------------------
  */

  if (store.loading) {
    return;
  }

  const text = message.value.trim();

  message.value = "";

  await store.sendMessage(text);

  scrollToBottom();

  inputRef.value?.focus();
};

/*
|--------------------------------------------------------------------------
| CLOSE
|--------------------------------------------------------------------------
*/

const handleClose = () => {

  open.value = false;

  message.value = "";
};

/*
|--------------------------------------------------------------------------
| NEW CHAT
|--------------------------------------------------------------------------
*/

const handleNewChat = () => {

  store.resetChat(
    defaultMessage.value
  );

  scrollToBottom();

  inputRef.value?.focus();
};

/*
|--------------------------------------------------------------------------
| AUTO SCROLL
|--------------------------------------------------------------------------
*/

watch(
  () => store.messages.length,
  () => {
    scrollToBottom();
  }
);

/*
|--------------------------------------------------------------------------
| AUTO FOCUS
|--------------------------------------------------------------------------
*/

watch(open, async (value) => {

  if (!value) {
    return;
  }

  await nextTick();

  inputRef.value?.focus();
});

/*
|--------------------------------------------------------------------------
| UPDATE USER NAME
|--------------------------------------------------------------------------
*/

watch(
  () => authStore.user?.Name,
  (newName) => {

    if (!newName) {
      return;
    }

    const firstMessage =
      store.messages[0];

    if (
      firstMessage &&
      firstMessage.type === "bot"
    ) {

      firstMessage.text =
        `Xin chào ${newName} 👋 Tôi có thể giúp gì cho bạn?`;
    }
  }
);

/*
|--------------------------------------------------------------------------
| MOUNTED
|--------------------------------------------------------------------------
*/

onMounted(() => {

  if (!store.messages.length) {

    store.messages.push(
      defaultMessage.value
    );
  }

  scrollToBottom();
});
</script>

<style scoped>
.chatbox {
  position: fixed;

  right: 18px;
  bottom: 78px;

  width: 320px;
  height: 460px;

  background: #ffffff;

  border-radius: 16px;

  overflow: hidden;

  display: flex;
  flex-direction: column;

  box-shadow:
    0 10px 28px rgba(0,0,0,.14);

  z-index: 9999;

  font-family:
    system-ui,
    -apple-system,
    BlinkMacSystemFont,
    "Segoe UI",
    Roboto,
    sans-serif;
}

/* HEADER */

.chat-header {
  height: 58px;

  padding: 0 14px;

  background: linear-gradient(
    90deg,
    #0099ff,
    #f97316
  );

  color: white;

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
  width: 34px;
  height: 34px;

  border-radius: 50%;

  background: rgba(255,255,255,.2);

  display: flex;

  align-items: center;
  justify-content: center;

  font-size: 20px;
}

.name {
  font-size: 14px;

  font-weight: 700;
}

.status {
  font-size: 10px;

  opacity: .85;

  display: flex;

  align-items: center;

  gap: 4px;
}

.status::before {
  content: "";

  width: 6px;
  height: 6px;

  border-radius: 50%;

  background: #4ade80;
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

  border-radius: 6px;

  background:
    rgba(255,255,255,.15);

  color: white;

  cursor: pointer;

  display: flex;

  align-items: center;
  justify-content: center;

  transition: .2s;
}

.icon-btn:hover {
  background:
    rgba(255,255,255,.3);
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
  width: 7px;
}

.chat-body::-webkit-scrollbar-thumb {
  background: #cbd5e1;

  border-radius: 20px;
}

/* MESSAGE */

.message {
  max-width: 80%;

  padding: 9px 12px;

  border-radius: 14px;

  line-height: 1.6;

  font-size: 13px;

  word-break: break-word;

  white-space: pre-wrap;
}

.message.bot {
  background: white;

  color: #1e293b;

  border: 1px solid #e2e8f0;

  align-self: flex-start;

  border-bottom-left-radius: 4px;
}

.message.user {
  background: #0088cc;

  color: white;

  align-self: flex-end;

  border-bottom-right-radius: 4px;
}

.typing {
  opacity: .7;

  font-style: italic;
}

/* INPUT */

.chat-input {
  padding: 10px;

  display: flex;

  gap: 6px;

  border-top:
    1px solid #f1f5f9;

  background: white;
}

.chat-input input {
  flex: 1;

  height: 38px;

  border:
    1px solid #cbd5e1;

  border-radius: 8px;

  padding: 0 12px;

  outline: none;

  font-size: 13px;

  transition: .2s;
}

.chat-input input:focus {
  border-color: #0088cc;

  box-shadow:
    0 0 0 2px rgba(0,136,204,.12);
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
  background: #0077b6;
}

.chat-input button:disabled {
  background: #cbd5e1;

  cursor: not-allowed;
}

/* MOBILE */

@media (max-width: 768px) {

  .chatbox {

    left: 10px;
    right: 10px;

    width: auto;

    bottom: 75px;

    height: 78vh;
  }

  .message {

    max-width: 88%;
  }
}
</style>