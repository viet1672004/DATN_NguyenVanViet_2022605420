import axios from "@/api/axios";

export default {
  sendMessage(data) {
    return axios.post("/chatbot", data);
  },
};