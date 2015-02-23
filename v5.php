<!DOCTYPE html> 
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="css/style1.css" /> 
		<link rel="stylesheet" type="text/css" href="css/scrollpath.css" /> 
		<link rel="stylesheet" type="text/css" href="css/scrollpath-style.css" /> 
		<link rel="stylesheet" type="text/css" href="css/scrollpath-style2.css" /> 

		<script type="text/javascript" src="js/lib/prefixfree.min.js"></script>

		<meta name="description" content="The plugin that lets you define custom scroll paths" /> 
		<title>DevConf.cz SocialWall</title>
	</head>
	<body>
	  <div class="slider">
		<div id="loading_transition">
			 <iframe id="loading_iframe" style="border: 0; width: 100%; height: 100%"></iframe>
		</div>
		<div class="wrapper">
			<div id="scroll0"></div>
			<div id="scroll1"></div>
			<div id="scroll2"></div>
			<div id="scroll3"></div>
			<div id="scroll4"></div>
			<div id="scroll5"></div>
			<div id="scroll6"></div>
			<div id="scroll7"></div>
		</div>
	</div>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/lib/jquery.easing.js"></script>
	<script type="text/javascript" src="js/jquery.scrollpath.js"></script>
	<script type="text/javascript">



///////////////////////////////////////////

var news_feed_cache = [];
var lastId = 0;
var maxTime = 0;
var scrollState = 0;

function nextScrollState() {
  scrollState = (scrollState+1)%8;
  var targetStr = "scroll"+scrollState; 
  $.fn.scrollPath("scrollTo", targetStr, 1500, "easeInOutSine");
  if (scrollState == 1) {
    for (var i = 5; i < 8; i++) {
      cache_to_scroll(i);
    }
  }
  if (scrollState == 6) {
    for (var i = 0; i < 4; i++) {
      cache_to_scroll(i);
    }
  } 
}



function format_time(t) {
  var now = Math.floor(new Date().getTime()/1000);
  var t1 = new Date(t*1000);

//return (t/86400) + " " + (now/86400) + " " + t1.toLocaleDateString() + " " +t1.toLocaleTimeString();
  if (Math.floor(t/86400) < Math.floor(now/86400)) {
  return t1.getDate() + '.' + (t1.getMonth()+1) + "." + t1.getFullYear();
  } else {
    return pad2(t1.getHours()) + ":" + pad2(t1.getMinutes());
  }

}

function pad2(i) {
  if (i > 9) return i;
  return "0"+i;
}

function downloadFeed() {
  $.get( "./news_api_json.php", function( data ) {
    var maxOne = 0;
    $.each(data, function( index, item ) {
      if ((item.image !== undefined) && (item.image !== '')) {
        if (maxTime < item.time) {
          news_feed_cache.push(item);
        }
        maxTime = Math.max(maxOne, maxTime);
      }
    });
    for (var i = 0; i < 8; i++) {
      cache_to_scroll(i);
    }
  });
}


function cache_to_scroll(scroll_index) {
  if (news_feed_cache.length == 0) {
    return;
  }

  lastId = (lastId > 0) ? (lastId - 1) : (news_feed_cache.length - 1);
  var item = news_feed_cache[ lastId ];

  var str = '';
  
  var style = "";
  if ((item.image !== undefined) && (item.image !== '')) {
    var style = "style=\"background-image: url("+item.image+")\"";
//          str += "<img src=\"" + item.image + "\"/ class=\"feed_image\">";
  }
  str += "<div class=\"feed_item\" " + style + ">"
  str += "<img src=\"img/" + item.type + ".png\" class=\"feed_type\"/>";
  str += "<img src=\"" + item.avatar + "\" class=\"feed_avatar img-rounded\"/>";
  str += "  <div class=\"feed_author\">" + item.author + "</div> ";
  str += "  <div class=\"feed_time\">" + format_time(item.time) + "</div>";
  str += "<div class=\"feed_text\">" + item.text + "</div>";
  str += "</div>"

  $( "#scroll"+scroll_index ).html(str);

}

var wholePageUpdate = 0;
function checkPageStatus(firstRun) {
  if (firstRun === undefined) {
    firstRun = false;
  }
  $.get( "status.php", function( data ) {
    if (firstRun) {
      wholePageUpdate = data.last_update
    }
    if (wholePageUpdate != data.last_update) {
      wholePageUpdate = data.last_update
      location.reload(); 
    }
  });
}

var lastBanner = -1;
function switchCss() {
  var url = "";
  $.get( "pages.php", function( data ) {
    lastBanner = ((parseInt(lastBanner)+1) % parseInt(data.length));
    var delayTime = 10;
    if (data.length > 0) {
      $('#loading_iframe').attr('src', data[lastBanner].url);
      delayTime = (parseInt(data[lastBanner].time)+1)*1000;
    }
  $('#loading_transition').fadeIn(1000).delay(delayTime).fadeOut(1000, function(){
console.log('blank loaded');
      $('#loading_iframe').attr('src', './pages/blank.html');
  });


    
  });

}


$( document ).ready(function() { 

$.fn.scrollPath("getPath")
    .moveTo(400, 50, {name: "scroll0"})
    .lineTo(400, 800, {name: "scroll1"})
    .arc(200, 1200, 400, -Math.PI/2, Math.PI/2, true)
    .lineTo(600, 1600, { name: "scroll2" })
    .lineTo(1750, 1600, { name: "scroll3" })
    .arc(1800, 1000, 600, Math.PI/2, 0, true, {rotate: Math.PI/2 })
    .lineTo(2400, 750, { name: "scroll4" })
    .rotate(3*Math.PI/2, { name: "scroll5" })
    .lineTo(2400, -700, { name: "scroll6" })
    .arc(2250, -700, 150, 0, -Math.PI/2, true)
    .lineTo(1350, -850, { name: "scroll7" })
    .arc(1300, 50, 900, -Math.PI/2, -Math.PI, true, {rotate: Math.PI*2, name: "scroll8"});

    // We're done with the path, let's initate the plugin on our wrapper element
    $(".wrapper").scrollPath({drawPath: true, wrapAround: true, scrollBar: false,});

//	$(".sp-canvas").toggle();


  checkPageStatus(true);
  downloadFeed();
  switchCss();
  setInterval(nextScrollState, 15000);
  setInterval(downloadFeed, 300000);
  setInterval(switchCss, 300000);
  setInterval(checkPageStatus, 5000);
//  $.fn.scrollPath("scrollTo", "scroll5", 500, "easeInOutSine");
});
	</script>
	</body>
</html>
