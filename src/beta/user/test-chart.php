<!DOCTYPE html>
<html>
<head>
  <title>Test Chart</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <canvas id="doughnutChart" width="500" height="500"></canvas>
  <script>
    function getDataAndDrawChart() {
  $.ajax({
    url: '../report_types/pdf_chart_record.php', // Replace with the actual URL to retrieve data
    method: 'GET',
    dataType: 'json',
    success: function(data) {
      //   var outerLabels = [];
      // var outerData = [];
      // var innerLabels = [];
      // var innerData = [];

      // // Parse the JSON data and extract the labels and values
      // for (var i = 0; i < data.children.length; i++) {
      //   var category = data.children[i];
      //   outerLabels.push(category.name);
      //   outerData.push(category.value);

      //   for (var j = 0; j < category.children.length; j++) {
      //     var subCategory = category.children[j];
      //     innerLabels.push(subCategory.name);
      //     innerData.push(subCategory.value);
      //   }
      // }

      getDataAndDrawChart2(data);
    },
    error: function(xhr, status, error) {
      console.error('Error retrieving data:', error);
    }
  });
}
function getDataAndDrawChart2(data) {
  // var data = {
  //   "name": "categories",
  //   "rows": 48,
  //   "children": [
  //     {
  //       "name": "Interessentsystemet",
  //       "children": [
  //         {"name": 1, "value": 1},
  //         {"name": 2, "value": 2},
  //         {"name": 3, "value": 3},
  //         {"name": 4, "value": 4},
  //         {"name": 5, "value": 5},
  //         {"name": 7, "value": 7},
  //         {"name": 8, "value": 8},
  //         {"name": 9, "value": 9}
  //       ]
  //     },
  //     {
  //       "name": "Politisk prosess",
  //       "children": [
  //         {"name": 10, "value": 10},
  //         {"name": 11, "value": 11},
  //         {"name": 12, "value": 12}
  //       ]
  //     },
  //     {
  //       "name": "Ledelsessystemet",
  //       "children": [
  //         {"name": 13, "value": 13},
  //         {"name": 17, "value": 17},
  //         {"name": 20, "value": 20},
  //         {"name": 24, "value": 24},
  //         {"name": 26, "value": 26},
  //         {"name": 27, "value": 27}
  //       ]
  //     },
  //     {
  //       "name": "Koordineringsprosess",
  //       "children": [
  //         {"name": 28, "value": 28},
  //         {"name": 29, "value": 29}
  //       ]
  //     },
  //     {
  //       "name": "Psykososialt arbeidsmiljø",
  //       "children": [
  //         {"name": 40, "value": 40},
  //         {"name": 41, "value": 41},
  //         {"name": 42, "value": 42},
  //         {"name": 43, "value": 43},
  //         {"name": 44, "value": 44},
  //         {"name": 45, "value": 45},
  //         {"name": 46, "value": 46},
  //         {"name": 47, "value": 47}
  //       ]
  //     },
  //     {
  //       "name": "Verdiutviklingsprosess",
  //       "children": [
  //         {"name": 56, "value": 56},
  //         {"name": 57, "value": 57},
  //         {"name": 58, "value": 58},
  //         {"name": 59, "value": 59},
  //         {"name": 60, "value": 60}
  //       ]
  //     },
  //     {
  //       "name": "Lederutviklingsprosess",
  //       "children": [
  //         {"name": 48, "value": 48},
  //         {"name": 49, "value": 49},
  //         {"name": 50, "value": 50},
  //         {"name": 51, "value": 51}
  //       ]
  //     },
  //     {
  //       "name": "Organisatoriske arbeidsmiljø",
  //       "children": [
  //         {"name": 30, "value": 30},
  //         {"name": 31, "value": 31},
  //         {"name": 33, "value": 33},
  //         {"name": 34, "value": 34},
  //         {"name": 35, "value": 35}
  //       ]
  //     },
  //     {
  //       "name": "Organisatorisk rettferdighet",
  //       "children": [
  //         {"name": 52, "value": 52},
  //         {"name": 54, "value": 54},
  //         {"name": 55, "value": 55}
  //       ]
  //     },
  //     {
  //       "name": "Profesjonaliseringsprosess",
  //       "children": [
  //         {"name": 36, "value": 36},
  //         {"name": 37, "value": 37},
  //         {"name": 38, "value": 38},
  //         {"name": 39, "value": 39}
  //       ]
  //     }
  //   ]
  // };

  var outerLabels = [];
  var outerData = [];
  var innerLabels = [];
  var innerData = [];

  // Parse the JSON data and extract the labels and values
  for (var i = 0; i < data.children.length; i++) {
    var category = data.children[i];
    outerLabels.push(category.name);
    outerData.push(category.children.length);

    for (var j = 0; j < category.children.length; j++) {
      var subCategory = category.children[j];
      innerLabels.push("responder"+subCategory.name);
      innerData.push(subCategory.value);
    }
  }

  drawChart(outerLabels, outerData, innerLabels, innerData);
}

function drawChart(outerLabels, outerData, innerLabels, innerData) {
  var ctx = document.getElementById('doughnutChart').getContext('2d');
  var doughnutChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      datasets: [
        {
          data: outerData,
          backgroundColor: ['red', 'green', 'blue'], // Customize the colors as desired
        },
        {
          data: innerData,
          backgroundColor: ['yellow', 'orange', 'purple'], // Customize the colors as desired
        }
      ],
      labels: outerLabels.concat(innerLabels),
    },
    options: {
      cutoutPercentage: 70, // Adjust the cutout percentage as desired for the inner part
      plugins: {
        legend: {
          display: true, // Hide the default legend
        },
        datalabels: {
          color: '#fff', // Customize the label color
          formatter: function(value, ctx) {
            var datasetIndex = ctx.datasetIndex;
            var labelIndex = ctx.dataIndex;

            // Display outer labels for outerData, inner labels for innerData
            if (datasetIndex === 0) {
              return '<input type="checkbox" value="' + outerLabels[labelIndex] + '"> ' + outerLabels[labelIndex];
            } else {
              return '<input type="checkbox" value="' + innerLabels[labelIndex] + '"> ' + innerLabels[labelIndex];
            }
          },
        },
      },
    },
  });
}

$(document).ready(function() {
  getDataAndDrawChart();
});


    </script>
</body>
</html>