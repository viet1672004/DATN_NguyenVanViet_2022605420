import html2canvas from "html2canvas";

import jsPDF from "jspdf";

/*
|--------------------------------------------------------------------------
| Export PDF Professional
|--------------------------------------------------------------------------
*/

export const exportToPdf = async (

  element,

  filename = "document.pdf",

  title = "BAO CAO HE THONG",

  subtitle = ""

) => {

  let hiddenElements = [];

  try {

    /*
    |--------------------------------------------------------------------------
    | Hide Elements
    |--------------------------------------------------------------------------
    */

    hiddenElements =
      element.querySelectorAll(".no-pdf");

    hiddenElements.forEach((el) => {

      el.dataset.originalDisplay =
        el.style.display;

      el.style.display = "none";

    });

    /*
    |--------------------------------------------------------------------------
    | Capture HTML
    |--------------------------------------------------------------------------
    */

    const canvas =
      await html2canvas(element, {

        scale: 2,

        useCORS: true,

        allowTaint: true,

        logging: false,

        backgroundColor: "#ffffff",

      });

    const imgData =
      canvas.toDataURL("image/png");

    /*
    |--------------------------------------------------------------------------
    | Create PDF
    |--------------------------------------------------------------------------
    */

    const pdf = new jsPDF(
      "p",
      "mm",
      "a4"
    );

    const pageWidth =
      pdf.internal.pageSize.getWidth();

    const pageHeight =
      pdf.internal.pageSize.getHeight();

    /*
    |--------------------------------------------------------------------------
    | Header
    |--------------------------------------------------------------------------
    */

    pdf.setFillColor(5, 150, 105);

    pdf.rect(
      0,
      0,
      pageWidth,
      18,
      "F"
    );

    pdf.setFont(
      "helvetica",
      "bold"
    );

    pdf.setFontSize(18);

    pdf.setTextColor(255, 255, 255);

    pdf.text(

      "HE THONG QUAN LY BAN VE TRUC TUYEN FUN TICKET",

      pageWidth / 2,

      11,

      {
        align: "center",
      }

    );

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    */

    pdf.setTextColor(17, 24, 39);

    pdf.setFontSize(22);

    pdf.text(

      title,

      pageWidth / 2,

      30,

      {
        align: "center",
      }

    );

    /*
    |--------------------------------------------------------------------------
    | Subtitle
    |--------------------------------------------------------------------------
    */

    if (subtitle) {

      pdf.setFontSize(11);

      pdf.setTextColor(107, 114, 128);

      pdf.text(

        subtitle,

        pageWidth / 2,

        38,

        {
          align: "center",
        }

      );
    }

    /*
    |--------------------------------------------------------------------------
    | Export Date
    |--------------------------------------------------------------------------
    */

    pdf.setFontSize(11);

    pdf.setTextColor(107, 114, 128);

    pdf.text(

      `Ngay xuat: ${new Date()
        .toLocaleDateString("vi-VN")}`,

      pageWidth / 2,

      subtitle ? 45 : 38,

      {
        align: "center",
      }

    );

    /*
    |--------------------------------------------------------------------------
    | Image Size
    |--------------------------------------------------------------------------
    */

    const margin = 10;

    const startY =
      subtitle ? 55 : 48;

    const contentWidth =
      pageWidth - margin * 2;

    const contentHeight =
      (canvas.height * contentWidth)
      / canvas.width;

    /*
    |--------------------------------------------------------------------------
    | Add First Page
    |--------------------------------------------------------------------------
    */

    let heightLeft =
      contentHeight;

    let position = startY;

    pdf.addImage(

      imgData,

      "PNG",

      margin,

      position,

      contentWidth,

      contentHeight

    );

    heightLeft -=
      (pageHeight - position);

    /*
    |--------------------------------------------------------------------------
    | Multi Page
    |--------------------------------------------------------------------------
    */

    while (heightLeft > 0) {

      position =
        heightLeft
        - contentHeight
        + 10;

      pdf.addPage();

      pdf.addImage(

        imgData,

        "PNG",

        margin,

        position,

        contentWidth,

        contentHeight

      );

      heightLeft -= pageHeight;
    }

    /*
    |--------------------------------------------------------------------------
    | Footer
    |--------------------------------------------------------------------------
    */

    const totalPages =
      pdf.getNumberOfPages();

    for (
      let i = 1;
      i <= totalPages;
      i++
    ) {

      pdf.setPage(i);

      pdf.setDrawColor(
        220,
        220,
        220
      );

      pdf.line(

        10,

        pageHeight - 15,

        pageWidth - 10,

        pageHeight - 15

      );

      pdf.setFontSize(10);

      pdf.setTextColor(120);

      pdf.text(

        `HE THONG QUAN LY FUN TICKET | Trang ${i}/${totalPages}`,

        pageWidth / 2,

        pageHeight - 8,

        {
          align: "center",
        }

      );
    }

    /*
    |--------------------------------------------------------------------------
    | Restore Elements
    |--------------------------------------------------------------------------
    */

    hiddenElements.forEach((el) => {

      el.style.display =
        el.dataset.originalDisplay || "";

    });

    /*
    |--------------------------------------------------------------------------
    | Save PDF
    |--------------------------------------------------------------------------
    */

    pdf.save(filename);

  } catch (error) {

    hiddenElements.forEach((el) => {

      el.style.display =
        el.dataset.originalDisplay || "";

    });

    console.error(
      "Export PDF Error:",
      error
    );

    throw error;
  }
};