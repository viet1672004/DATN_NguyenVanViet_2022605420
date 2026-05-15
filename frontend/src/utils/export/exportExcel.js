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
  | Create Empty Worksheet
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
  | Add JSON Data
  |--------------------------------------------------------------------------
  */

  XLSX.utils.sheet_add_json(

    worksheet,

    data,

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
    Object.keys(data[0] || {}).length;

  /*
  |--------------------------------------------------------------------------
  | Merge Cells
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
  | System Name Style
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

  /*
  |--------------------------------------------------------------------------
  | Title Style
  |--------------------------------------------------------------------------
  */

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

  /*
  |--------------------------------------------------------------------------
  | Subtitle Style
  |--------------------------------------------------------------------------
  */

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
  | Header Style
  |--------------------------------------------------------------------------
  */

  const headers =
    Object.keys(data[0] || {});

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
  | Column Width
  |--------------------------------------------------------------------------
  */

  worksheet["!cols"] = [

    { wch: 28 },

    { wch: 20 },

    { wch: 18 },

    { wch: 18 },

    { wch: 28 },

  ];

  /*
  |--------------------------------------------------------------------------
  | Row Height
  |--------------------------------------------------------------------------
  */

  worksheet["!rows"] = [

    { hpt: 28 },

    { hpt: 24 },

    { hpt: 20 },

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