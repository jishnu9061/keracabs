function getChartColorsArray(e) {
  e = $(e).attr("data-colors");
  return (e = JSON.parse(e)).map(function (e) {
    e = e.replace(" ", "");
    if (-1 == e.indexOf("--")) return e;
    e = getComputedStyle(document.documentElement).getPropertyValue(e);
    return e || void 0;
  });
}

var columnDatalabelColors = getChartColorsArray("#column_chart_datalabel"),
  options = {
    chart: { height: 350, type: "bar", toolbar: { show: !1 } },
    plotOptions: { bar: { borderRadius: 10, dataLabels: { position: "top" } } },
    dataLabels: {
      enabled: !0,
      formatter: function (e) {
        return e + "%";
      },
      offsetY: -22,
      style: { fontSize: "12px", colors: ["#0fb390"] },
    },
    series: [
      {
        name: "Inflation",
        data: [2.5, 3.2, 5, 10.1, 4.2, 3.8, 3, 2.4, 4, 1.2, 3.5, 0.8],
      },
    ],
    colors: columnDatalabelColors,
    grid: { borderColor: "#0fb390" },
    xaxis: {
      categories: [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec",
      ],
      position: "top",
      labels: { offsetY: -18 },
      axisBorder: { show: !1 },
      axisTicks: { show: !1 },
      crosshairs: {
        fill: {
          type: "gradient",
          gradient: {
            colorFrom: "#D8E3F0",
            colorTo: "#BED1E6",
            stops: [0, 100],
            opacityFrom: 0.4,
            opacityTo: 0.5,
          },
        },
      },
      tooltip: { enabled: !0, offsetY: -35 },
    },
    yaxis: {
      axisBorder: { show: !1 },
      axisTicks: { show: !1 },
      labels: {
        show: !1,
        formatter: function (e) {
          return e + "%";
        },
      },
    },
    title: {
      text: "",
      floating: !0,
      offsetY: 330,
      align: "center",
      style: { color: "#0fb390", fontWeight: "500" },
    },
  };
(chart = new ApexCharts(
  document.querySelector("#column_chart_datalabel"),
  options
)).render();

var pieColors = getChartColorsArray("#pie_chart"),
  options = {
    chart: { height: 320, type: "pie" },
    series: [44, 55, 41, 17, 15],
    labels: ["Series 1", "Series 2", "Series 3", "Series 4", "Series 5"],
    colors: pieColors,
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
  document.querySelector("#pie_chart"),
  options
)).render();
var donutColors = getChartColorsArray("#donut_chart"),
  options = {
    chart: { height: 320, type: "donut" },
    series: [44, 55, 41, 17, 15],
    labels: ["Series 1", "Series 2", "Series 3", "Series 4", "Series 5"],
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
