<!DOCTYPE html>
<html>
<head>
  <title>Uneven Pie Chart</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .chart-container {
      display: inline-block;
      width: 300px;
      margin: 10px;
    }
  </style>
</head>
<body>
  <div id="charts-container"></div>

  <script>
    // JSON response
    var jsonResponse = {
      "name": "categories",
      "rows": 48,
      "children": [
        {
          "name": "Interessentsystemet",
          "children": [
            {"name": 1,"value": 1},
            {"name": 2,"value": 2},
            {"name": 3,"value": 3},
            {"name": 4,"value": 4},
            {"name": 5,"value": 5},
            {"name": 7,"value": 7},
            {"name": 8,"value": 8},
            {"name": 9,"value": 9}
          ]
        },
        {
          "name": "Politisk prosess",
          "children": [
            {"name": 10,"value": 10},
            {"name": 11,"value": 11},
            {"name": 12,"value": 12}
          ]
        },
        // Add more children as needed
      ]
    };

    // Create pie charts for each name and value pair
    var chartsContainer = document.getElementById('charts-container');
    jsonResponse.children.forEach(function(item) {
      var canvas = document.createElement('canvas');
      canvas.width = 300;
      canvas.height = 300;
      var ctx = canvas.getContext('2d');
      chartsContainer.appendChild(canvas);

      var data = item.children.map(function(child) { return child.value; });
      var labels = item.children.map(function(child) { return child.name; });
      var colors = generateRandomColors(item.children.length); // Function to generate random colors

      new Chart(ctx, {
        type: 'polarArea',
        data: {            
          labels: labels,
          datasets: [{
            data: data,
            backgroundColor: colors,            
          }]
        },
        options: {
          responsive: true
        }
      });
    });

    // Function to generate random colors
    function generateRandomColors(count) {
      var colors = [];
      for (var i = 0; i < count; i++) {
        var color = '#' + Math.floor(Math.random() * 16777215).toString(16); // Generate random hex color
        colors.push(color);
      }
      return colors;
    }
  </script>
</body>
</html>
