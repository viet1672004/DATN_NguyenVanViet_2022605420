import api from "@/api/axios";

export default {
  // 🔥 LẤY USER (sửa ở đây)
  me() {
    return api.get("/me"); // ✅ KHỚP backend
  },

  // UPDATE PROFILE
  updateProfile(data) {
    return api.post("/user/update", {
      Name: data.name,
      Email: data.email,
      Phone: data.phone,
    });
  },

  // CHANGE PASSWORD
  changePassword(data) {
    return api.post("/user/change-password", {
      current_password: data.current,
      new_password: data.new,
      new_password_confirmation: data.confirm,
    });
  }
};