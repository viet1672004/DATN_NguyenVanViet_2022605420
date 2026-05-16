import * as XLSX from "xlsx-js-style";

/*
|--------------------------------------------------------------------------
| Export Excel Professional
|--------------------------------------------------------------------------
*/

export const exportToExcel = (

  data = [],

  fileName = "export.xlsx",

  title = "BÁO CÁO HỆ THỐNG",

  subtitle = ""

) => {

  /*
  |--------------------------------------------------------------------------
  | Create Workbook
  |--------------------------------------------------------------------------
  */

  const workbook =
    XLSX.utils.book_new();

  /*
  |--------------------------------------------------------------------------
  | Create Worksheet
  |--------------------------------------------------------------------------
  */

  const worksheet =
    XLSX.utils.aoa_to_sheet([]);

  /*
  |--------------------------------------------------------------------------
  | Header Info
  |--------------------------------------------------------------------------
  */

  XLSX.utils.sheet_add_aoa(

    worksheet,

    [
      ["HỆ THỐNG QUẢN LÝ FUN TICKET"],

      [title],

      [subtitle],

      [],
    ],

    {
      origin: "A1",
    }

  );

  /*
  |--------------------------------------------------------------------------
  | Remove hidden field
  |--------------------------------------------------------------------------
  */

  const exportData =
    data.map((item) => {

      const clone = { ...item };

      delete clone.total_price;

      return clone;
    });

  /*
  |--------------------------------------------------------------------------
  | Add Table Data
  |--------------------------------------------------------------------------
  */

  XLSX.utils.sheet_add_json(

    worksheet,

    exportData,

    {
      origin: "A5",
      skipHeader: false,
    }

  );

  /*
  |--------------------------------------------------------------------------
  | Total Columns
  |--------------------------------------------------------------------------
  */

  const totalCols =
    Object.keys(exportData[0] || {}).length;

  /*
  |--------------------------------------------------------------------------
  | Merge Header
  |--------------------------------------------------------------------------
  */

  worksheet["!merges"] = [

    {
      s: { r: 0, c: 0 },
      e: {
        r: 0,
        c: totalCols - 1,
      },
    },

    {
      s: { r: 1, c: 0 },
      e: {
        r: 1,
        c: totalCols - 1,
      },
    },

    {
      s: { r: 2, c: 0 },
      e: {
        r: 2,
        c: totalCols - 1,
      },
    },

  ];

  /*
  |--------------------------------------------------------------------------
  | Header Styles
  |--------------------------------------------------------------------------
  */

  worksheet["A1"].s = {

    font: {

      bold: true,

      sz: 16,

      color: {
        rgb: "FFFFFF",
      },

      name: "Arial",
    },

    fill: {

      fgColor: {
        rgb: "059669",
      },

    },

    alignment: {

      horizontal: "center",

      vertical: "center",

    },

  };

  worksheet["A2"].s = {

    font: {

      bold: true,

      sz: 20,

      color: {
        rgb: "111827",
      },

      name: "Arial",
    },

    alignment: {

      horizontal: "center",

      vertical: "center",

    },

  };

  worksheet["A3"].s = {

    font: {

      italic: true,

      sz: 12,

      color: {
        rgb: "6B7280",
      },

      name: "Arial",
    },

    alignment: {

      horizontal: "center",

      vertical: "center",

    },

  };

  /*
  |--------------------------------------------------------------------------
  | Table Header Style
  |--------------------------------------------------------------------------
  */

  const headers =
    Object.keys(exportData[0] || {});

  headers.forEach((_, index) => {

    const cell =
      XLSX.utils.encode_cell({

        r: 4,

        c: index,

      });

    if (worksheet[cell]) {

      worksheet[cell].s = {

        font: {

          bold: true,

          color: {
            rgb: "FFFFFF",
          },

          sz: 12,

          name: "Arial",
        },

        fill: {

          fgColor: {
            rgb: "059669",
          },

        },

        alignment: {

          horizontal: "center",

          vertical: "center",

        },

        border: {

          top: {
            style: "thin",
          },

          bottom: {
            style: "thin",
          },

          left: {
            style: "thin",
          },

          right: {
            style: "thin",
          },

        },

      };
    }
  });

  /*
  |--------------------------------------------------------------------------
  | Body Style
  |--------------------------------------------------------------------------
  */

  const range =
    XLSX.utils.decode_range(
      worksheet["!ref"]
    );

  for (
    let row = 5;
    row <= range.e.r;
    row++
  ) {

    for (
      let col = 0;
      col < totalCols;
      col++
    ) {

      const cell =
        XLSX.utils.encode_cell({

          r: row,

          c: col,

        });

      if (worksheet[cell]) {

        worksheet[cell].s = {

          alignment: {

            vertical: "center",

          },

          border: {

            top: {
              style: "thin",
            },

            bottom: {
              style: "thin",
            },

            left: {
              style: "thin",
            },

            right: {
              style: "thin",
            },

          },

          font: {

            name: "Arial",

            sz: 11,

          },

        };
      }
    }
  }

  /*
  |--------------------------------------------------------------------------
  | TOTAL ROW
  |--------------------------------------------------------------------------
  */

  const totalAmount =
    data.reduce(

      (sum, item) =>

        sum + Number(item.total_price || 0),

      0

    );

  /*
  |--------------------------------------------------------------------------
  | Current Month
  |--------------------------------------------------------------------------
  */

  const currentMonth =
    new Date().getMonth() + 1;

  /*
  |--------------------------------------------------------------------------
  | Create Total Row
  |--------------------------------------------------------------------------
  */

  const totalRow =
    new Array(totalCols).fill("");

  totalRow[0] =
    `TỔNG DOANH THU THÁNG ${currentMonth}`;

  totalRow[totalCols - 1] =
    totalAmount.toLocaleString("vi-VN") + " đ";

  XLSX.utils.sheet_add_aoa(

    worksheet,

    [totalRow],

    {
      origin: -1,
    }

  );

  /*
  |--------------------------------------------------------------------------
  | Merge Total Row
  |--------------------------------------------------------------------------
  */

  worksheet["!merges"].push({

    s: {
      r: data.length + 5,
      c: 0,
    },

    e: {
      r: data.length + 5,
      c: totalCols - 2,
    },

  });

  /*
  |--------------------------------------------------------------------------
  | Total Row Style
  |--------------------------------------------------------------------------
  */

  const totalRowIndex =
    data.length + 5;

  for (
    let col = 0;
    col < totalCols;
    col++
  ) {

    const cell =
      XLSX.utils.encode_cell({

        r: totalRowIndex,

        c: col,

      });

    if (worksheet[cell]) {

      worksheet[cell].s = {

        font: {

          bold: true,

          color: {
            rgb: "FFFFFF",
          },

          sz: 13,

          name: "Arial",

        },

        fill: {

          fgColor: {
            rgb: "059669",
          },

        },

        alignment: {

          horizontal: "center",

          vertical: "center",

        },

        border: {

          top: {
            style: "thin",
          },

          bottom: {
            style: "thin",
          },

          left: {
            style: "thin",
          },

          right: {
            style: "thin",
          },

        },

      };
    }
  }

  /*
  |--------------------------------------------------------------------------
  | Align Money Right
  |--------------------------------------------------------------------------
  */

  const totalMoneyCell =
    XLSX.utils.encode_cell({

      r: totalRowIndex,

      c: totalCols - 1,

    });

  if (worksheet[totalMoneyCell]) {

    worksheet[totalMoneyCell].s.alignment = {

      horizontal: "right",

      vertical: "center",

    };
  }

  /*
  |--------------------------------------------------------------------------
  | Column Width
  |--------------------------------------------------------------------------
  */

  worksheet["!cols"] = [

    { wch: 28 },

    { wch: 20 },

    { wch: 20 },

    { wch: 24 },

    { wch: 20 },

  ];

  /*
  |--------------------------------------------------------------------------
  | Row Height
  |--------------------------------------------------------------------------
  */

  worksheet["!rows"] = [

    { hpt: 30 },

    { hpt: 28 },

    { hpt: 24 },

  ];

  /*
  |--------------------------------------------------------------------------
  | Append Sheet
  |--------------------------------------------------------------------------
  */

  XLSX.utils.book_append_sheet(

    workbook,

    worksheet,

    "Report"

  );

  /*
  |--------------------------------------------------------------------------
  | Export File
  |--------------------------------------------------------------------------
  */

  XLSX.writeFile(

    workbook,

    fileName

  );
};