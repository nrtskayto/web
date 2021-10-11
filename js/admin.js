let mymap = L.map('mapid').setView([38.230462,21.753150], 1);
L.tileLayer('https://api.maptiler.com/maps/streets/{z}/{x}/{y}.png?key=H4YYIrADBiPJ65kRGBVc',
  {attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">© MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">© OpenStreetMap contributors</a>'
  }).addTo(mymap);

  let cfg = {"radius": 40,
    "maxOpacity": 0.8,
    "scaleRadius": false,
    "useLocalExtrema": false,
    latField: 'lat',
    lngField: 'lng',
    valueField: 'count'};

let heatmapLayer = new HeatmapOverlay(cfg);
mymap.addLayer(heatmapLayer); 

$.post( "admin.php?query=statssss", function( data ) {
    data = JSON.parse(data);

    //console.log(data);
    function loadTableData(items) {
      const table = document.getElementById("testBody2");
        
        let row = table.insertRow();
        let entries = row.insertCell(0);
        entries.innerHTML = items.users_count;
        let last_upload = row.insertCell(1);
        last_upload.innerHTML = items.entriesCount_requestType;
        let c = row.insertCell(2);
        c.innerHTML = items.entriesCount_responseStatus;
        let d = row.insertCell(3);
        d.innerHTML = items.unique_Domains;
        let e = row.insertCell(4);
        e.innerHTML = items.tUniqueUserISPs;
       }
      
      loadTableData(data);
    
});

$.post("admin_queries.php?query=timings", function( resp ) {
  resp = JSON.parse(resp);
  if (resp.status !== 200) return;
  const data = resp.data;
  setUpTimingsGraph(data['timings']);
});

function setUpTimingsGraph(data) {
  prepareTimingsData(data);

  const elemtnId_prefix = '#d-';
  const dropdowns = setUpTimingsSelectDropDowns(elemtnId_prefix, data);
  const chart = getTimingsChart();
  
  renderTimingsChart(chart, data);
  registerChartFilterEvents(elemtnId_prefix, chart, dropdowns, data, renderTimingsChart);           
}

function prepareTimingsData(data) {
  // cast to propper types
 data.forEach(function (entry) {
     entry.content_type = (entry.content_type === null ? 'UNKNOWN' : entry.content_type);
     entry.startedDateTime = new Date(entry.startedDateTime);
     entry.wait = parseInt(entry.wait);
 });
}

function setUpTimingsSelectDropDowns(elementId_prefix, data) {
  const isps = new Set();
  const contentTypes = new Set();
  const methods = new Set();
  const days = new Set();

  data.forEach(function (entry) {
      // find all the unique criteria for aggregations
      isps.add(entry.user_isp);
      contentTypes.add(entry.content_type);
      methods.add(entry.method);
      days.add(getWeekDayFromDate(entry.startedDateTime));
  });

  const dropdowns = { isps, contentTypes, methods, days };
  rednerDropdowns(elementId_prefix, dropdowns);
  return dropdowns;
}

function getTimingsChart() {
  let labels = [];
  for (let i = 0; i < 24; ++i) {
      labels.push(i);
  }
  return chart = new Chart(document.getElementById('timings-chart'), {
      type: 'bar',
      data: {
          labels,
          datasets: null
      },
      options: {
          scales: {
              yAxes: [{
                  ticks: {
                      beginAtZero: true
                  }
              }]
          }
      }
  });
}

function renderTimingsChart(chart, data) {
  // Create 24 buckets for every hour of the day
  let buckets = new Array(24);
  for (let i = 0; i < 25; ++i) {
      buckets[i] = [];
  }

  for (let entry of data) {
      const bucketIdx = entry.startedDateTime.getHours();
      buckets[bucketIdx].push(entry.wait);
  }

  // calculate the average wait time
  const avgs = buckets.map(function (bucket) {
      const sum = bucket.reduce(function (acc, value) {
          return acc + value;
      }, 0);
      return sum / bucket.length;
  });

  // create dataset, and update the chart
  const dataset = {
      label: 'Average wait time (ms)',
      data: avgs,
      borderWidth: 1
  };

  // set the new dataset, call the chart to update itself
  chart.data.datasets = [dataset];
  chart.update();
}

function registerChartFilterEvents(elementId_prefix, chart, dropdowns, data, renderClb) {
  for (let id in dropdowns) {
      const elementId = elementId_prefix + id;
      $(elementId).change(function () {
          const filteredData = filterChartData(elementId_prefix, dropdowns, data);
          renderClb(chart, filteredData);
      });
  }
}

function getWeekDayFromDate(date) {
  const weekday = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
  return weekday[date.getDay()];
}

function renderDropdown(elementId, dropdownValues) {
  let html = '';
  for (value of dropdownValues) {
      html += `<option selected value='${value}'>${value}</option>`;
  }
  $(elementId).html(html);
}

function rednerDropdowns(elementId_prefix, dropdowns) {
  for (let id in dropdowns) {
      const dropdownValues = dropdowns[id];
      renderDropdown(elementId_prefix + id, dropdownValues);
  }
}

function logout(){
  sessionStorage.clear();
  if (sessionStorage.getItem("user_name") == null){
      location.replace("index.html");
  }
}