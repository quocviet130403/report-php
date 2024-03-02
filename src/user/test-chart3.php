<!doctype html>
<html>

<head>
  <meta charset="utf-8" />
  <title>score Meter</title>
  <meta name="viewport" content="width=device-width">

  <style>
    body {
      font-family: Arial;
    }

    .container {
      width: 600px;
      margin: 30px auto;
      text-align: center;
    }

    .gauge {
      width: 50%;
      height: 200px;
      float: left;
      border: 1px solid #ddd;
      box-sizing: border-box;
      margin: 30px 0 20px 0;
    }

    .conf {
      display: block;
      width: 50%;
      float: left;
      margin: 30px 0 20px 0;
      text-align: left;
    }

    .clear {
      clear: both;
    }

    h3 {
      margin: 50px 0 10px 0;
    }

    a:link.button,
    a:active.button,
    a:visited.button,
    a:hover.button {
      margin: 0 5px 0 2px;
      padding: 10px 13px;
    }
  </style>

</head>


<body>

  <div class="container">    
  <div id="jg2" class="gauge"></div>
    <div class="clear"></div>
    <a href="#" id="jg2_refresh" class="button grey">Random update</a>
  </div>

  <script src="justgage/raphael.min.js"></script>
  <script src="justgage/justgage.js"></script>
  <script>
    /** Random integer  */
    function getRandomInt(min, max) {
      return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    document.addEventListener("DOMContentLoaded", function (event) {      

      var jg2 = new JustGage({
        id: "jg2",
        value: 75,
        min: 0,
        max: 100,
        gaugeWidthScale: 0.6,
        customSectors: {
          ranges: [{
            color: "#ff3b30",
            lo: 0,
            hi: 35
          }, 
          {
            color: "#ff3000",
            lo: 36,
            hi: 70
          },{
            color: "#43bf58",
            lo: 71,
            hi: 100
          }]
        },
        counter: true
      });

   

      document.getElementById('jg2_refresh').addEventListener('click', function () {
        jg2.refresh(getRandomInt(0, 100));
      });

    });
  </script>
</body>

</html>