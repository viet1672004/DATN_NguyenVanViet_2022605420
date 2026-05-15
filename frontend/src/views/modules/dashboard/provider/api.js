import axios from "@/api/axios";

export default {

  /*
  |--------------------------------------------------------------------------
  | Get Dashboard
  |--------------------------------------------------------------------------
  */

  getDashboard(params = {}) {

    return axios.get(
      "/dashboard",
      {
        params
      }
    );
  }

};