function getChartColorsArray(r) {
  r = $(r).attr("data-colors");
  return (r = JSON.parse(r)).map(function (r) {
    r = r.replace(" ", "");
    if (-1 == r.indexOf("--")) return r;
    r = getComputedStyle(document.documentElement).getPropertyValue(r);
    return r || void 0;
  });
}
var columnDatalabelColors = getChartColorsArray("#column_chart_datalabel"),
  options = {
    chart: { height: 350, type: "bar", toolbar: { show: !1 } },
    plotOptions: { bar: { borderRadius: 10, dataLabels: { position: "top" } } },
    dataLabels: {
      enabled: !0,
      formatter: function (e) {
        return e + "";
      },
      offsetY: -22,
      style: { fontSize: "10px", colors: ["#0fb390"] },
    },
    series: [
      {
        name: "Value",
        data: [
          23.5, 30, 25, 10.1, 14.2, 23.8, 37, 21.4, 41, 10.2, 13.5, 10.8, 45,
          52, 64,
        ],
      },
    ],
    colors: columnDatalabelColors,
    grid: { borderColor: "#0fb390" },
    xaxis: {
      categories: [
        "Operating Income",
        "GST",
        "Retention",
        "TDS Recovery",
        "Other Bill Deductions",
        "Net Invoice Value",
        "Client Payments Received",
        "Balance Payment",
        "Operating Expenses",
        "Depreciation",
        "P/L as per standard Accounting",
        "Indirect Expenses",
        "Non-Operating Income",
        "Prepaid expenses (Progress)",
        "End Profit Receivable",
      ],
      position: "bottom",
      labels: { offsetY: 10, style: { fontSize: "9px" } },
      axisBorder: { show: !1 },
      axisTicks: { show: !1 },
      crosshairs: {
        fill: {
          type: "gradient",
          gradient: {
            colorFrom: "#0fb390",
            colorTo: "#0fb390",
            stops: [0, 100],
            opacityFrom: 0.4,
            opacityTo: 0.5,
          },
        },
      },
    },
    yaxis: {
      axisBorder: { show: !1 },
      axisTicks: { show: !1 },
      labels: {
        show: !1,
        formatter: function (e) {
          return e + "";
        },
      },
    },
  };
(chart = new ApexCharts(
  document.querySelector("#column_chart_datalabel"),
  options
)).render();

var pieColors = getChartColorsArray("#pie_chart"),
  options = {
    chart: { height: 320, type: "pie" },
    series: [74, 55, 41, 17, 15, 45, 55, 5],
    labels: [
      "Total",
      "AAP KGB",
      "AAP SBI",
      "AAP SIB",
      "AAP HDFC",
      "TKP KGB",
      "TKP NRE",
      "TKP HDFC",
    ],
    colors: pieColors,
    legend: {
      show: !0,
      position: "right",
      horizontalAlign: "center",
      verticalAlign: "middle",
      floating: !1,
      fontSize: "14px",
      offsetX: 0,
    },
    responsive: [
      {
        breakpoint: 600,
        options: { chart: { height: 240 }, legend: { show: !1 } },
      },
    ],
  };
(chart = new ApexCharts(
  document.querySelector("#pie_chart"),
  options
)).render();
var donutColors = getChartColorsArray("#donut_chart"),
  options = {
    chart: { height: 320, type: "donut" },
    series: [44, 55, 41],
    labels: ["Assets ", "Liability", "Net "],
    colors: donutColors,
    legend: {
      show: !0,
      position: "bottom",
      horizontalAlign: "center",
      verticalAlign: "middle",
      floating: !1,
      fontSize: "14px",
      offsetX: 0,
    },
    responsive: [
      {
        breakpoint: 600,
        options: { chart: { height: 240 }, legend: { show: !1 } },
      },
    ],
  };
(chart = new ApexCharts(
  document.querySelector("#donut_chart"),
  options
)).render();
