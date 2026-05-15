import axios from "@/api/axios";

export default {

  getList(params = {}) {
    return axios.get("/blogs", { params });
  },

  getDetail(id) {
    return axios.get(`/blogs/${id}`);
  },

  create(formData) {

    return axios.post(
      "/blogs",
      formData,
      {
        headers: {
          "Content-Type": "multipart/form-data"
        }
      }
    );
  },

  update(id, formData) {

    return axios.post(
      `/blogs/${id}?_method=PUT`,
      formData,
      {
        headers: {
          "Content-Type": "multipart/form-data"
        }
      }
    );
  },

  delete(id) {
    return axios.delete(`/blogs/${id}`);
  }
};