import axios from "@/api/axios";

export default {
    getDashboard() {
        return axios.get("/dashboard");
    }
};