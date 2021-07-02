<?php
    /**
     * Template Name: Virus US-Mexico Data Challenge
     * Template Post Type: post, page
     * Contributors: Anthony & Thy Nguyen
     *
     * @package WordPress
     * @subpackage Twenty_Twenty
     * @since Twenty Twenty 1.0
     
     http://todaystatistics.com/index.php/covinitor_data_challenge_2020
     */
    
    get_header();
    $speciallang = array("fr", "de"); // should not delete _ in 2 lang 
    $slug = str_replace("index.php/","" , urldecode(trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/')));
    echo "<script>console.log('" . json_encode($slug) . "');</script>";
    // covinitor_data_challenge_2020
     
    if (strpos(substr($slug,0,6),'/')) {
        $langcode = explode("/", $slug)[0];
    } else {
        $langcode = "en";
    }
    
    echo "<script>console.log('" . json_encode($langcode) . "');</script>";
    
    // get tail
    if (strpos(substr($slug,0,6),'/')){
        $slug = explode("/", $slug)[1];
    }
    
    // realname_slug
    $explodedurl = explode("_", $slug); // explodedurl: an arr having 2 vars
    $explodeslug = explode('_coronavirus-cases-us-city_', $slug);
    
    
    if (!in_array($langcode, $speciallang)){
        $namelist = ucwords(str_replace( "_" ," ",str_replace( "-" ," ",$explodeslug[0])));
        $firstexplodedurl = ucwords(str_replace( "_" ," ",str_replace( "-" ," ",$explodedurl[0])));   // Sant-Julia-de-Loria
        $secondexplodedurl = ucwords(str_replace( "_" ," ",str_replace( "-" ," ",$explodedurl[1]))); // weather-today
    } else{
        $namelist = ucwords(str_replace( "_" ," ",$explodeslug[0]));
        $firstexplodedurl = $explodedurl[0];   // Sant-Julia-de-Loria
        $secondexplodedurl = $explodedurl[1]; // weather-today
    } 
?>

<main id="site-content" role="main">

<?php
    if ( have_posts() ) {     
        while ( have_posts() ) {
            the_post();          
            get_template_part( 'template-parts/content' );
        }
    }   
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://todaystatistics.com/wp-content/themes/twentytwenty/assets/js/kshared/gauge.js"></script>
<link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" />
<script src="http://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="https://todaystatistics.com/wp-content/themes/twentytwenty/assets/js/kshared/jquery.csv.js"></script>
<script type="text/javascript">

$(document).ready( function () {
    $.getJSON("https://api.covid19api.com/dayone/country/mx",
        function(data) {
            var table = $('#myTable').DataTable({columnDefs: [ {targets: [0,1,2,3,4,5,6], className: 'dt-body-right', orderSequence: ["desc", "asc" ]}], responsive:true });
            var Country = data[0].Country; // ok
            $(".Country").append(Country);
                if(data[0].Province == "") {
                    date = data[0].Date.replace("T00:00:00Z","");
                    table.row.add(
                        [date, data[0].Confirmed,data[0].Confirmed, data[0].Deaths, data[0].Deaths,data[0].Active, data[0].Recovered ]).draw();
                    for(var i = 1 ; i < data.length; i++) {
                        date = data[i].Date.replace("T00:00:00Z","");
                        table.row.add([  date,data[i].Confirmed,data[i].Confirmed - data[i-1].Confirmed, data[i].Deaths, data[i].Deaths - data[i-1].Deaths,
                                        data[i].Active,data[i].Recovered ]).draw(); 
                    }
                    table.columns( [ 0 ] ).order( 'desc' ).draw();
                } else { 
                    var totalCase = 0; //= data[0].Confirmed;
                    var totalDeath = 0; //= data[0].Deaths;
                    var totalRecover = 0; //= data[0].Recovered;
                    var totalActive = 0; //= data[0].Active;
                    var newCase = 0;// = totalCase;
                    var newDeath = 0;// = totalDeath;
                    var date;// = data[0].Date.replace("T00:00:00Z","");
                    var start = 0;
                    for(var i = 0 ; i < data.length-1; i++) {
                        start = i;
                        newCase = totalCase; // reset
                        newDeath = totalDeath; // reset
                        // find all same date
                        while(data[i].Date == data[i+1].Date) {
                            totalCase = data[start].Confirmed;
                            totalDeath = data[start].Deaths;
                            totalRecover = data[start].Recovered;
                            totalActive = data[start].Active;
                            i++;
                        }
                        // if loop runs
                        if (start != i) {
                            for(var x = start+1; x < i+1; x++) {
                                totalCase += data[x].Confirmed;
                                totalDeath += data[x].Deaths;
                                totalRecover += data[x].Recovered;
                                totalActive += data[x].Active;
                            } // loop count
                            newCase = totalCase - newCase;
                            newDeath = totalDeath - newDeath;
                            if(newDeath < 0)
                            newDeath=0;
                            if(newCase<0)
                            newCase=0;
                            date = data[start].Date.replace("T00:00:00Z","");
                            table.row.add([date,totalCase,newCase,totalDeath,newDeath,totalActive,totalRecover]).draw();
                            table.columns( [ 0 ] ).order( 'desc' ).draw();
                        } else {
                            // loop k run
                            newCase = data[start].Confirmed - newCase;
                            newDeath = data[start].Deaths - newDeath;
                            totalCase = data[start].Confirmed;
                            totalDeath = data[start].Deaths;
                            totalRecover = data[start].Recovered;
                            totalActive = data[start].Active;
                            date = data[start].Date.replace("T00:00:00Z","");
                            if(newDeath < 0)
                            newDeath="";
                            if(newCase <0)
                            newCase="";
                            table.row.add([ date,totalCase,newCase,totalDeath,newDeath,totalActive,totalRecover ]).draw();
                            table.columns( [ 0 ] ).order( 'desc' ).draw();
                        }
                            
                    }
                            
                }
                            
            });
                  
            $.getJSON("https://corona-api.com/countries/us",
                    function(datas) {
                    var table = $('#myTable1').DataTable({columnDefs: [ {targets: [ 0,1,2,3,4,5,6], className: 'dt-body-right', orderSequence: [ "desc", "asc" ]}], responsive:true });
                    for(var i=0; i < datas.data.timeline.length-1; i++) {
                        var date = datas.data.timeline[i].date;
                        var totalCase=datas.data.timeline[i].confirmed;
                        var newCase=datas.data.timeline[i].new_confirmed;
                        var totalDeath=datas.data.timeline[i].deaths;
                        var newDeath=datas.data.timeline[i].new_deaths;
                        var totalActive=datas.data.timeline[i].active;
                        var totalRecover=datas.data.timeline[i].recovered;
                        table.row.add([ date,totalCase,newCase,totalDeath,newDeath,totalActive,totalRecover ]).draw();
                        table.columns( [ 0 ] ).order( 'desc' ).draw();
                    } 
                });          
            });
</script>

<style>
body {
    background:white!important;
}

table, th, td {
    border: 1px solid grey;
}


th {
    text-align: left;
    padding: 8px;
    background-color: #0672ee;
    color: white;
}


.custom-background {
    background-image: url(https://todaystatistics.com/wp-content/themes/twentytwenty/assets/js/kshared/datachallenge/vr1.jpg)!important;
    background-attachment: fixed!important;
    background-position: center!important;
    background-repeat: no-repeat!important;
    background-size: cover!important;
}


@media screen and (min-width: 501px)
.graphic-wrapper .rating {
width: 25%;
}

.graphic-wrapper .rating {
width: 25%;
display: inline-block;
    text-align:left;
    float:left;
    padding-right:2%;
}

.graphic-wrapper .rating.red b {
    background-color: var(--red);
}

.graphic-wrapper .rating.orange b {
    background-color: var(--orange);
}

.graphic-wrapper .rating.yellow b {
    background-color: var(--yellow);
}

.graphic-wrapper .rating.green b {
    background-color: var(--green);
}

.graphic-wrapper .rating ul {
margin: 0;
padding: 0;
    list-style-type: none;
}

* {
    box-sizing: border-box;
    -webkit-tap-highlight-color: transparent;
}

li {
    display: list-item;
    text-align: -webkit-match-parent;
}

.graphic-wrapper .rating li + li {
    margin-top: 9px;
}

.graphic-wrapper .rating li {
    margin: 0;
    padding: 0;
    font-size: 13px;
}

ul {
display: block;
    list-style-type: disc;
    margin-block-start: 1em;
    margin-block-end: 1em;
    margin-inline-start: 0px;
    margin-inline-end: 0px;
    padding-inline-start: 40px;
}

.graphic-wrapper .rating b {
    background-color: #ccc;
    color: #fff;
    padding: 10px;
    border-radius: 4px;
    font-weight: bold;
    text-align: center;
    display: block;
    margin-right: 5px;
}

.graphic-wrapper .rating h3 {
    font-size: 13px;
    text-align: center;
    font-weight: normal;
    text-transform: uppercase;
    padding-top: 0;
    margin-top:10px;
    line-height: 1.2;
    letter-spacing: 0.05em;
    -webkit-font-smoothing: antialiased;
}

body {
    --red: red;
    --orange: orange;
    --yellow: gold;
    --green: green;
}

.rating .states{
    display:none;
}

.column {
    float: left;
    width: 50%;
    padding: 30px;
}

main {
    margin-left:5%;
    margin-right:5%;
}


.sidenav {
  height: 100%;
  width: 0;
  position: fixed;
  z-index: 100;
  top: 0;
  left: 0;
  background-color: #111;
  overflow-x: hidden;
  transition: 0.5s;
  padding-top: 60px;
}

.sidenav a {
  padding: 0px 0px 15px 40px;
  text-decoration: none;
  font-size: 12px;
  color: #818181;
  display: block;
  transition: 0.2s;
}

.sidenav a:hover {
  color: #f1f1f1;
}

.sidenav .closebtn {
  position: absolute;
  top: 0;
  right: 25px;
  font-size: 36px;
  margin-left: 50px;
}

@media screen and (max-height: 500px) {
  .sidenav {padding-top: 0px;}
  .sidenav a {font-size: 15px;}
}

#site-header{background-color: transparent;}
</style>

<div style="text-align:center">
<h1 style="text-align:center"><img style="display:inline-block"  src="https://todaystatistics.com/wp-content/themes/twentytwenty/assets/images/cvlogo4.png"></h1>
<h3>US-Mexico Border COVID-19 Risk Monitor</h3>
</div>

<div id="mySidenav" class="sidenav">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
  <a href="#risklevel">COVID-19 Risk Level</a>
  <a href="#mapinteract">Interact with Maps</a>
  <a href="#dailycaserisk">Risk Levels in Border Communities Over Time</a>
  <a href="#risktoday">US-Mexico Border Risk Level Today</a>
  <a href="#bordercrossing">US-Mexico Border Crossings in 19-20</a>
  <a href="#trendseasonality">Trends and Seasonality Tests for US COVID-19 Daily Cases</a>
  <a href="#traveldecision">Travel Decisions based on Risk Level</a>
  <a href="#latestnews">COVID-19 Latest News</a>
  <a href="#dailycase">COVID-19 Daily Cases by County/Municipality</a>
  <a href="#source">Sources</a>
</div>
<span style="font-size:30px;cursor:pointer" onclick="openNav()">&#9776; Menu</span>

<script>
function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}
</script>

<a name="risklevel"></a>
<h2>What is COVID-19 Risk Level?</h2>

<p>According to the Harvard Global Health Institute and Brown School of Public Health, risk-levels of COVID-19 are computed by daily new cases per 100,000 people (7-day rolling average). </p>
<a style="display:block" href="https://globalepidemics.org/wp-content/uploads/2020/06/key_metrics_and_indicators_v4.pdf
" target="_blank">Learn more</a>
<div class="graphic-wrapper" style="text-align:center;margin-top:30px;margin-bottom:30px">

<div class="graphic ratings" style="overflow:hidden">
<div class="rating green">
<h3><b>Green</b></h3>
<ul>
<li class="states"><strong>States:</strong> 1</li>
<li class="range"><strong>Definition:</strong> &lt;1 daily new case per 100,000 people </li>
<li class="indicates"><strong>Indicates:</strong> close to containment</li>
<li class="intervention"><strong>Interventions:</strong> Continue testing, contact tracing, limiting gatherings, and isolating infected people to suppress outbreaks.</li>
</ul>
</div>



<div class="rating yellow">
<h3><b>Yellow</b></h3>
<ul>
<li class="states"><strong>States:</strong> 34</li>
<li class="range"><strong>Definition:</strong> 1-9 daily new cases per 100,000 people </li>
<li class="indicates"><strong>Indicates:</strong> potential community spread</li>
<li class="intervention"><strong>Interventions:</strong> Continue testing, contact tracing, and social distancing, as well as masking, isolating, and other measures.</li>
</ul>
</div>


<div class="rating orange">
<h3><b>Orange</b></h3>
<ul>
<li class="states"><strong>States:</strong> 14</li>
<li class="range"><strong>Definition:</strong> 10-24 daily new cases per 100,000 people </li>
<li class="indicates"><strong>Indicates:</strong> escalating community spread</li>
<li class="intervention"><strong>Interventions:</strong> Stay-at-home orders may be necessary, unless it is possible to surge testing and contact tracing capability.</li>
</ul>
</div>

<div class="rating red">
<h3><b>Red</b></h3>
<ul>
<li class="states"><strong>States:</strong> 2</li>
<li class="range"><strong>Definition:</strong> 25+ daily new cases per 100,000 people </li>
<li class="indicates"><strong>Indicates:</strong> unchecked community spread</li>
<li class="intervention"><strong>Interventions:</strong> Stay-at-home orders, widespread testing, and mask mandate. Increased contact tracing are necessary.</li>
</ul>
</div>

</div>
<div class="footer" style="text-align:left;font-size:11px;display:none">
<p>Source: <a href="https://globalepidemics.org/key-metrics-for-covid-suppression/" target="_blank">Harvard Global Health Institute</a>, <a href="https://github.com/CSSEGISandData/COVID-19" target="_blank">Center for Systems Science and Engineering at Johns Hopkins University</a> (as of June 29)</p>
<p>Credit: Alyson Hurt/NPR</p>
</div>
</div>
<span>Press the play button in the map below to see Risk Levels over time</span>
<span><img style="border:1px solid black;border-radius:5px;position: relative;top: 17px;left: 10px;display:inline-block" src="https://todaystatistics.com/wp-content/themes/twentytwenty/assets/js/kshared/datachallenge/play.gif"></span>

<p>&nbsp</p>

<iframe src="https://kepler.gl/demo/map?mapUrl=https://dl.dropboxusercontent.com/s/jp5pcy9eapz7psr/keplergl_mmuqrah.json" style="border:0px #ffffff none;" name="myiFrame" scrolling="no" frameborder="1" marginheight="0px" marginwidth="0px" height="768px" width="100%" allowfullscreen></iframe>
<p>&nbsp</p>
<p>The US COVID-19 data are publicly available and aggregated by NY Times. This data is compiled from authoritative state and local governments and health departments in the United States. The data begins with the first reported coronavirus case in Washington State on Jan. 21, 2020. The Mexico COVID-19 data is published by the Mexico government.</p>
<p><span>United States source: </span><span><a href="https://github.com/nytimes/covid-19-data" target="_blank">The New York Times</a></span><br><span>Mexico source: </span><span><a href="https://coronavirus.gob.mx/datos/#DownZCSV" target="_blank">Gobierno de México</a></span></p>
<a name="mapinteract"></a>
<h2>How to interact with the map?</h2>
<p>By pressing the bottom play button, you can observe all data over time. In this example, we show daily COVID-19 cases per 100,000 population for over 3200 US counties since Jan 20, 2020.</p>
<p>&nbsp</p>

<iframe width="560" height="315" src="https://www.youtube.com/embed/zHz85WgGuOU?version=3&loop=1&autoplay=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

<a name="dailycaserisk"></a>
<h3>COVID-19 Daily Cases & Risk Levels in Border Counties/Municipalities</h3>
<p>"Daily cases per 100,000 people" or "The 7-day moving average of new cases" was calculated to smooth expected variations in daily counts. Total cases were reported by American & Mexican state and territorial jurisdictions, starting from January 21, 2020.
</p>
<img src="https://todaystatistics.com/wp-content/themes/twentytwenty/assets/js/kshared/datachallenge/usmxcasespop2.png">

<br>
<p><span>United States source: </span><span><a href="https://github.com/nytimes/covid-19-data" target="_blank">The New York Times</a></span><br><span>Mexico source: </span><span><a href="https://coronavirus.gob.mx/datos/#DownZCSV" target="_blank">Gobierno de México</a></span></p>

<a name="risktoday"></a>
<h2>US-Mexico Border Communities Risk Level Today</h2>
<div class="container" style="text-align:center">
<div style="display:inline-block;margin:5%">

<div style="text-align:center" id="preview-textfield" class="preview-textfield"></div>
<canvas id="demo" height="600" width="600" style="width: 300px; height: 300px;"></canvas>
<div style="text-align:center"><p>US border counties daily cases per 100,000 people</p><p>Updated Sep 22, 2020</p></div>

</div>

<div style="display:inline-block;margin:5%">
<div style="text-align:center" id="preview-textfield2" class="preview-textfield2"></div>
<canvas id="demo2" height="600" width="600" style="width: 300px; height: 300px;"></canvas>
<div style="text-align:center"><p>Mexico border municipalities daily cases per 100,000 people</p><p>Updated Sep 22, 2020</p></div>

</div>
<div style="text-align:left">
<p><span>United States source: </span><span><a href="https://github.com/nytimes/covid-19-data" target="_blank">The New York Times</a></span><br><span>Mexico source: </span><span><a href="https://coronavirus.gob.mx/datos/#DownZCSV" target="_blank">Gobierno de México</a></span></p>
</div>
</div>

<a name="bordercrossing"></a>
<h2>US-Mexico Border Crossings in 2019-2020</h2>
<p>The Bureau of Transportation Statistics (BTS) provides inbound crossing data across the U.S.-Canada and U.S.-Mexico borders at the port level. We summarize the number of passengers and pedestrians from January 2019 to July 2020.</p>
<span>Press the play button in the following map to see Risk Levels over time</span>
<span><img style="border:1px solid black;border-radius:5px;position: relative;top: 17px;left: 10px;display:inline-block" src="https://todaystatistics.com/wp-content/themes/twentytwenty/assets/js/kshared/datachallenge/play.gif"></span>

<p>&nbsp</p>
<iframe src="https://kepler.gl/demo/map?mapUrl=https%3A%2F%2Fdl.dropboxusercontent.com%2Fs%2Fwczmdcytuznlvej%2Fkeplergl_slkg9wv.json" style="border:0px #ffffff none;" name="myiFrame" scrolling="no" frameborder="1" marginheight="0px" marginwidth="0px" height="768px" width="100%" allowfullscreen></iframe>
<p>&nbsp</p>
<p><span>Source: </span><span><a href="https://www.bts.gov/content/border-crossingentry-data" target="_blank">The Bureau of Transportation Statistics</a></span></p>
<h3>Border crossing passengers of all US-Mexico border ports</h3>
<img src="https://todaystatistics.com/wp-content/themes/twentytwenty/assets/js/kshared/datachallenge/allport.png">
<p>&nbsp</p>
<p><span>Source: </span><span><a href="https://www.bts.gov/content/border-crossingentry-data" target="_blank">The Bureau of Transportation Statistics</a></span></p>

<a name="trendseasonality"></a>
<h3>Trends and Seasonality Tests for US COVID-19 Daily Cases</h3>
<p>By the Dickey-Fuller test, we see data of COVID-19 time series shows a trend. <small>(More info about this trend will be mentioned below). </small> <br>In particular, based on the autocorrelation plot of the first 30 days, the trend is strong with no apparent seasonality.</p>
<img src="https://todaystatistics.com/wp-content/themes/twentytwenty/assets/js/kshared/datachallenge/seasonality1.png">
<p>&nbsp</p>
<p><span>United States source: </span><span><a href="https://github.com/nytimes/covid-19-data" target="_blank">The New York Times</a></p>
<br>
<p>To make the process more stationary, we subtract the time series from itself with a lag of one day. <br> This would give us the following plot.</p>
<img src="https://todaystatistics.com/wp-content/themes/twentytwenty/assets/js/kshared/datachallenge/seasonality2.png">
<p>&nbsp</p>
<p><span>United States source: </span><span><a href="https://github.com/nytimes/covid-19-data" target="_blank">The New York Times</a></span></p>
<p>With the stationary data, we see the autocorrelation plot looks like a sinusoidal function, which implies seasonality. We can count the period in the plot above and witness a 7-day period.</p>

<p>According to a study of Tel Aviv University, the 7-day periodicity of COVID-19 is due to weekend gatherings and the approximation of 5-day incubation period.</p>
<p style="background-color:lightgrey;padding:15px"><em>&ldquo;Susceptible/older people may become infected at higher rates during weekend-days compared to weekdays, as a result of increased social interactions with younger relatives or friends. Under this assumption, it follows that these vulnerable individuals exhibit clinical signs of COVID-19 infection at higher rates ~5 days after the weekend, on Thursday-Friday&hellip; In the US, the low range of daily deaths in the period assessed is about 1700, while the high range is about 2500. We believe that social distancing may lower such differences.&rdquo;</em></p>

<p dir="ltr">&nbsp;</p>
<p dir="ltr" style="text-align:center">Reference</p>
<p dir="ltr"><em>Ricon-Becker, I., Tarrasch, R., Blinder, P., Ben-Eliyahu, S. (2020). &ldquo;A seven-day cycle in COVID-19 infection and mortality rates: Are intergenerational social interactions on the weekends killing susceptible people?&rdquo;. <a href="https://www.medrxiv.org/content/10.1101/2020.05.03.20089508v1.full.pdf." aria-invalid="true" target="_blank">https://www.medrxiv.org/content/10.1101/2020.05.03.20089508v1.full.pdf.</a> Tel Aviv University. Retrieved on Sep 23, 2020.</em></p>

<a name="traveldecision"></a>
<h2>How to Make Your Travel Decisions Based on Risk Level?</h2>
<img src="https://todaystatistics.com/wp-content/themes/twentytwenty/assets/js/kshared/datachallenge/mxtraveladvisor.png">
<p>Source: <a title="Mexico Travel Advisory" href="https://travel.state.gov/content/travel/en/traveladvisories/traveladvisories/mexico-travel-advisory.html" target="_blank">U.S. Department of State - Bureau of Consular Affairs</a></p>
<p>Even though Mexican municipalities have a risk-level of Green (< 1 daily new case per 100k people, showing a close to containment of Coronavirus), the travel advisory said you should reconsider travelling. In particular, the CDC issued a travel alert for Mexico with a level 3 of Travel Health Notice during COVID-19. Some regions in Mexico still face an increasing trend of daily new cases, whereas others report a high volume of hospitalization. You should stay at home, practice social distancing, and follow guidelines of the authority.</p>
<p>For the United States, the risk level at border counties is Orange (from 10 to 24 daily new cases per 100k people). This means there is an escalating spread in the community. Moreover, the country's overal deaths surpassed 200,000 people, and the CDC also issued a level 3 of Travel Health Notice. Level 3 means the COVID-19 risk is high. Thus, we must obey shelter-in-place rules, wear masks in public places, avoid close contact, and continue testing plus contact tracing.

<p>Source: <a title="CDC map and travel notices" href="https://www.cdc.gov/coronavirus/2019-ncov/travelers/map-and-travel-notices.html" target="_blank">Centers for Disease Control and Prevention</a></p>

<script>
window.onload = function() {
    
    var opts = {
    angle: -0.25,
    lineWidth: 0.2,
    radiusScale:0.9,
    pointer: {
    length: 0.6,
    strokeWidth: 0.05,
    color: '#000000'
    },
    staticLabels: {
    font: "10px sans-serif",
    labels: [0, 1, 10, 25,35],
    fractionDigits: 0
    },
    staticZones: [
        {strokeStyle: "green", min: 0, max: 1},
        {strokeStyle: "gold", min: 1, max: 10},
        {strokeStyle: "orange", min: 10, max: 25},
        {strokeStyle: "red", min: 25, max: 35}
        ],
    limitMax: false,
    limitMin: false,
    highDpiSupport: true
    };
    var target = document.getElementById('demo'); // your canvas element
    var gauge = new Gauge(target).setOptions(opts); // create sexy gauge!
    
    var target2 = document.getElementById('demo2'); // your canvas element
    var gauge2 = new Gauge(target2).setOptions(opts); // create sexy gauge!
    
    document.getElementById("preview-textfield").className = "preview-textfield";
    document.getElementById("preview-textfield2").className = "preview-textfield2";
    
    gauge.setTextField(document.getElementById("preview-textfield"));
    gauge2.setTextField(document.getElementById("preview-textfield2"));
    
    
    gauge.maxValue = 35; // set max gauge value
    gauge.setMinValue(0);  // set min value
    gauge.set(13.75); // set actual value
    
    
    gauge2.maxValue = 35; // set max gauge value
    gauge2.setMinValue(0);  // set min value
    gauge2.set(0.64); // set actual value
}
</script>

    <a name="latestnews"></a>
    <h2>COVID-19 Latest News</h2>
    <div class="column">
    <h4>English News</h4>
    <div id="outlinks"></div>
    <?php
        global $wpdb;
        $result = $wpdb->get_results ( "SELECT * FROM tableEN WHERE topic='COVID-19' ORDER BY RAND() LIMIT 12");
    ?>

    <script>
    var resultSQL = <?php echo json_encode($result)?>;
    var outlink ="";
    for(var i = 0; i < resultSQL.length; i++) {
        /// console.log(resultSQL[i]);
        outlink +=  '<i class="fas fa-ambulance"' + 'style="font-size:24px;color:#0672ee"' + 'alt role="presentation"' + '"></i>';
        outlink += ' ' + resultSQL[i]["source"] + '<br>';
        //outlink += '<a href="' + resultSQL[i]["url"] + '" target="_blank">';
        //outlink += resultSQL[i]["title"] + '</a><br>'; // links
        outlink += resultSQL[i]["description"] + '<br>';
        outlink += (resultSQL[i]["pubdate"]) + '<br><br><br>';
        document.getElementById("outlinks").innerHTML = outlink;
    }
    </script>
    </div>
    </div>
    <div class="column">
    <h4>Spanish News</h4>
    <div id="outlinks1"></div>
    <?php
        global $wpdb;
        $result = $wpdb->get_results ( "SELECT * FROM tableES WHERE topic='COVID-19' ORDER BY RAND() LIMIT 12");
    ?>
     <script>
    var resultSQL = <?php echo json_encode($result)?>;
    var outlink ="";
    for(var i = 0; i < resultSQL.length; i++) {
        /// console.log(resultSQL[i]);
        outlink +=  '<i class="fas fa-ambulance"' + 'style="font-size:24px;color:#0672ee"' + 'alt role="presentation"' + '"></i>';
        outlink += ' ' + resultSQL[i]["source"] + '<br>';
        //outlink += '<a href="' + resultSQL[i]["url"] + '" target="_blank">';
        //outlink += resultSQL[i]["title"] + '</a><br>'; // links
        outlink += resultSQL[i]["description"] + '<br>';
        outlink += (resultSQL[i]["pubdate"]) + '<br><br><br>';
        document.getElementById("outlinks1").innerHTML = outlink;
    }
    </script>
    </div>
    </div>

<div>
<h2>COVID-19 daily cases by county/municipality</h2> <!-- counties / muni -->
<a name="dailycase"></a>
<div class="row">
<div class="column">
<h4>United States</h4>
<div style="display:inline;"><img class="flag" style="vertical-align: middle;display:inline;" src="https://todaystatistics.com/wp-content/themes/twentytwenty/assets/images/flag/flag-of-United-states.png" border="1 px solid #aaa"></div>

<p><b>California</b></p>
<p><a title="San Diego County Coronavirus Cases Today" href="https://todaystatistics.com/index.php/san-diego-county-coronavirus-cases-today/">San Diego County Coronavirus Cases Today</a></p>
<p><a title="Imperial County Coronavirus Cases Today" href="https://todaystatistics.com/index.php/imperial-county-coronavirus-cases-today/">Imperial County Coronavirus Cases Today</a></p>

<p><b>Arizona</b></p>
<p><a title="Yuma County Coronavirus Cases Today" href=" https://todaystatistics.com/index.php/yuma-county-az-coronavirus-cases-today/
">Yuma County Coronavirus Cases Today</a></p>
<p><a title="Pima County Coronavirus Cases Today" href="https://todaystatistics.com/index.php/pima-county-coronavirus-cases-today/">Pima County Coronavirus Cases Today</a></p>
<p><a title="Santa Cruz County Coronavirus Cases Today" href="https://todaystatistics.com/index.php/santa-cruz-county-az-coronavirus-cases-today/
">Santa Cruz County Coronavirus Cases Today</a></p>
<p><a title="Cochise County Coronavirus Cases Today" href="https://todaystatistics.com/index.php/cochise-county-coronavirus-cases-today/
">Cochise County Coronavirus Cases Today</a></p>

<p><b>New Mexico</b></p>
<p><a title="Hidalgo County Coronavirus Cases Today" href="https://todaystatistics.com/index.php/hidalgo-county-nm-coronavirus-cases-today/">Hidalgo County Coronavirus Cases Today</a></p>
<p><a title="Luna County Coronavirus Cases Today" href="https://todaystatistics.com/index.php/luna-county-coronavirus-cases-today/">Luna County Coronavirus Cases Today</a></p>
<p><a title="Dona Ana County Coronavirus Cases Today" href="https://todaystatistics.com/index.php/new-mexico_coronavirus-cases-us-state/">Dona Ana County Coronavirus Cases Today</a></p>

<p><b>Texas</b></p>
<p><a title="El Paso County Coronavirus Cases Today" href="https://todaystatistics.com/index.php/el-paso-county-tx-coronavirus-cases-today/">El Paso County Coronavirus Cases Today</a></p>
<p><a title="Hudspeth County Coronavirus Cases Today" href="https://todaystatistics.com/index.php/hudspeth-county-coronavirus-cases-today/">Hudspeth County Coronavirus Cases Today</a></p>
<p><a title="Jeff Davis County Coronavirus Cases Today" href="https://todaystatistics.com/index.php/jeff-davis-county-tx-coronavirus-cases-today/">Jeff Davis County Coronavirus Cases Today</a></p>
<p><a title="Presidio County Coronavirus Cases Today" href="https://todaystatistics.com/index.php/presidio-county-coronavirus-cases-today/">Presidio County Coronavirus Cases Today</a></p>
<p><a title="Brewster County Coronavirus Cases Today" href="https://todaystatistics.com/index.php/brewster-county-coronavirus-cases-today/">Brewster County Coronavirus Cases Today</a></p>
<p><a title="Terrell County Coronavirus Cases Today" href="https://todaystatistics.com/index.php/terrell-county-tx-coronavirus-cases-today/">Terrell County Coronavirus Cases Today</a></p>
<p><a title="Val Verde County Coronavirus Cases Today" href="https://todaystatistics.com/index.php/val-verde-county-coronavirus-cases-today/">Val Verde County Coronavirus Cases Today</a></p>
<p><a title="Kinney County Coronavirus Cases Today" href="https://todaystatistics.com/index.php/kinney-county-coronavirus-cases-today/">Kinney County Coronavirus Cases Today</a></p>
<p><a title="Maverick County Coronavirus Cases Today" href="https://todaystatistics.com/index.php/maverick-county-coronavirus-cases-today/">Maverick County Coronavirus Cases Today</a></p>
<p><a title="Webb County Coronavirus Cases Today" href="https://todaystatistics.com/index.php/webb-county-coronavirus-cases-today/">Webb County Coronavirus Cases Today</a></p>
<p><a title="Zapata County Coronavirus Cases Today" href="https://todaystatistics.com/index.php/zapata-county-coronavirus-cases-today/">Zapata County Coronavirus Cases Today</a></p>
<p><a title="Starr County Coronavirus Cases Today" href="https://todaystatistics.com/index.php/starr-county-coronavirus-cases-today/">Starr County Coronavirus Cases Today</a></p>
<p><a title="Hidalgo County Coronavirus Cases Today" href="https://todaystatistics.com/index.php/hidalgo-county-tx-coronavirus-cases-today/">Hidalgo County Coronavirus Cases Today</a></p>
<p><a title="Cameron County Coronavirus Cases Today" href="https://todaystatistics.com/index.php/cameron-county-tx-coronavirus-cases-today/">Cameron County Coronavirus Cases Today</a></p>
</div>

<div class="column">
<h4>Mexico</h4>
<div style="display:inline;"><img class="flag" style="vertical-align: middle;display:inline;" src="https://todaystatistics.com/wp-content/themes/twentytwenty/assets/images/flag/flag-of-Mexico.png" border="1 px solid #aaa"></div>
<br>

<p>Updated today in the following Mexican border states<br><small>(Coronavirus cases in a specific Mexican municipality are included in the link below)</small></p>
<p><b>Baja California </b></p>
<p>Infected: 19207</p>
<p>Deceased: 3529</p> <br>

<p><b>Sonora</b></p>
<p>Infected: 24418</p>
<p>Deceased: 2853</p> <br>

<p><b>Chihuahua</b></p>
<p>Infected: 10746</p>
<p>Deceased: 1358</p> <br>

<p><b>Coahuila</b></p>
<p>Infected: 26023</p>
<p>Deceased: 1841</p> <br>

<p><b>Nuevo León</b></p>
<p>Infected: 39220</p>
<p>Deceased: 2959</p> <br>

<p><b>Tamaulipas</b></p>
<p>Infected: 28713</p>
<p>Deceased: 2175</p> <br>

<br>
<p>More information can be found on</p>
<p><a title="Mexican Municipalities Coronavirus" href="https://coronavirus.gob.mx/datos/#DOView">Mexican Municipalities Coronavirus</a></p>
<p><a title="Mexico Coronavirus Cases Today" href="https://todaystatistics.com/index.php/mexico-coronavirus-cases-today/">Mexico Coronavirus Cases Today</a></p>
<br><br><br>

</div>
</div>

<h2>Daily COVID-19 statistics by countries</h2> <!-- mytable -->
<div style="text-align:center">
<h4>Mexico</h4>
<div style="display:inline;"><img class="flag" style="vertical-align: middle;display:inline;" src="https://todaystatistics.com/wp-content/themes/twentytwenty/assets/images/flag/flag-of-Mexico.png" border="1 px solid #aaa"></div>
</div>

<div style="overflow-x:auto">
<table id="myTable" class="display" style="overflow-x:auto">
<thead>
    <tr>
        <th>Date</th>
        <th>Total Cases</th>
        <th>New Cases</th>
        <th>Deaths</th>
        <th>New Deaths</th>
        <th>Active</th>
        <th>Recovered</th>
    </tr>
</thead>
<tbody>
</tbody>
</table>
</div>

<div style="text-align:center">
<h4>United States</h4>
<div style="display:inline;"><img class="flag" style="vertical-align: middle;display:inline;" src="https://todaystatistics.com/wp-content/themes/twentytwenty/assets/images/flag/flag-of-United-states.png" border="1 px solid #aaa"></div>
</div>
<div style="overflow-x:auto">
<table id="myTable1" class="display" style="overflow-x:auto">
<thead>
<tr>
<th>Date</th>
<th>Total Cases</th>
<th>New Cases</th>
<th>Deaths</th>
<th>New Deaths</th>
<th>Active</th>
<th>Recovered</th>
</tr>
</thead>
<tbody>
</tbody>
</table>
</div>

<br>
<a name="source"></a>
<h2>Sources</h2>
<p><span>United States source: </span><span><a href="https://github.com/nytimes/covid-19-data" target="_blank">The New York Times</a></span>
<br>
<span>Mexico source: </span><span><a href="https://coronavirus.gob.mx/datos/#DownZCSV" target="_blank">Gobierno de México</a></span>
<br>
<span>Analysis: </span><span><a href="https://towardsdatascience.com/the-complete-guide-to-time-series-analysis-and-forecasting-70d476bfe775" target="_blank">Time series analysis</a></span>
</p>

</main><!-- #site-content -->

<?php get_template_part( 'template-parts/footer-menus-widgets' ); ?>

<?php get_footer(); ?>
