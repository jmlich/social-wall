<?php
  require('./news_api.php');
 date_default_timezone_set('Europe/Prague');

$now = date('H:i');

$title = 'Social wall';

$content = <<<EOF

<div class="col-sm-8" role="main">
<section class="section-normal section-link" >
<div id="feed">
  <div id="feed_content"></div>
<div style="clear:both;">&nbsp;</div>
</div>
</section>

</div>

<div class="col-sm-4" role="main">
  <img src="./img/devconf_logo_square.png" class="logo item_dark"/>
  <img src="./img/devconf_logo_square.png" class="logo item_light"/>


  <div id="Timer">$now</div>

  <div id="schedule"></div>

</div>

<div id="partners_footer">
  <div id="partners_footer_inner">
  </div>
</div>


<!-- div id="loading_transition">
  <iframe id="loading_iframe" style="border: 0; width: 100%; height: 100%"></iframe>
</div -->


<script>

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

function format_time_simple(t) {
  var t1 = new Date(t*1000);
  return pad2(t1.getHours()) + ":" + pad2(t1.getMinutes());
}

format_time_simple

function pad2(i) {
  if (i > 9) return i;
  return "0"+i;
}


function downloadFeed() {
  $.get( "./news_api_json.php", function( data ) {
    var maxOne = 0;
    data.reverse();
    $.each(data, function( index, item ) {
      if (maxTime < item.time) {
        var itemId = "feedItem"+lastId;
        lastId++;
        var str = '';

        str += "<div class=\"feed_item\" id=\""+itemId+"\"> ";

        str += "<img src=\"img/" + item.type + ".png\" class=\"feed_type\"/>";
        str += "<img src=\"" + item.avatar + "\" class=\"feed_avatar img-rounded\"/>";
        str += "<div class=\"feed_title\">";
        str += "  <div class=\"feed_author\">" + item.author + "</div> ";
        str += "  <div class=\"feed_time\">" + format_time(item.time) + "</div>";
        str += "</div>";

        str += "<div class=\"feed_text\">" + item.text + "</div>";

        if ((item.image !== undefined) && (item.image !== '')) {
          str += "<img src=\"" + item.image + "\"/ class=\"feed_image\">";
        }
//  str += "<br/><a href=\"" + item.link + "\">link</a>";
        str += "</div>";

        $( "#feed_content" ).prepend( str );
        $( "#"+itemId ).hide().fadeIn('slow');
      }
        maxOne = Math.max(maxOne, item.time);
    })
    maxTime = Math.max(maxOne, maxTime);
//    if (lastId > 1000) { location.reload(); }
//    $( "#feed_content" ).html(str);
  });
}

var lastId=0
var maxTime = 0;



setInterval(function() {
  var t1 = new Date();
    $('#Timer').text(pad2(t1.getHours()) + ":" + pad2(t1.getMinutes()));
}, 1000);


var old_schedule_content = "";
function downloadSchedule() {
  $.get( "./sched.org/?json", function( data ) {

    var str = "";
    var now = Math.floor(new Date().getTime()/1000);

    $.each(data.sessions, function( index, item ) {
        if (item.event_end > now) {
        var spkrs = item.speakers.join(", ");
          str += "<div class=\"schedule_item\">";
          str += "<div class=\"schedule_item_inner\" style=\"border-color: "+item.room_color+"\">";
          str += "<div class=\"schedule_time\">"+format_time_simple(item.event_start) + "<br/>" + format_time_simple(item.event_end) + "<br/>" + item.room_short + "</div>";
          str += "<div class=\"schedule_content\"><strong>"+ spkrs + "</strong>"+ ((spkrs != "") ? ": " : "") + item.topic + "</div>"
          str += "<div style=\"clear: both\"></div>";
          str += "</div></div>";
        }
    }); // .each
    if (old_schedule_content != str) {
      $( "#schedule" ).fadeOut(1000, function() {
        $( "#schedule" ).html( str );
      }).fadeIn(1000);
    }
    old_schedule_content = str;

  }); // .get


}


function downloadPartners() {
  $.get( "./partneri_api.php", function( data ) {
    var str = '';
    data.sort(function(){
         return ( Math.round( Math.random() ) - 0.5 )
       })
    $.each(data, function( index, item ) {
        str += "<a href=\""+item.href+"\"><img src=\""+item.img+"\" alt=\""+item.title+"\"/></a>";
    })
    $( "#partners_footer_inner" ).fadeOut(1000, function() { 
      $( "#partners_footer_inner" ).html(str) 
    }).fadeIn();
  });
}

var cssState = 0;
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
  $('#loading_transition').fadeIn(1000, function() { 
//      $( "#partners_footer_inner" ).html(str) 

   switch (cssState) {
     case 0:
       $('link[href="css/style1.css"]').attr('href','css/style2.css');
       cssState = 1;
     break;
     case 1:
       $('link[href="css/style2.css"]').attr('href','css/style1.css');
       cssState = 0;
     break;
   }

  }).delay(delayTime).fadeOut(1000, function(){
    $('#loading_iframe').attr('src', './pages/blank.html');
  });


    
  });
//$('#loading_transition').attr('display', 'visible');

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

downloadFeed();
downloadSchedule();
//switchCss();
checkPageStatus(true);
//downloadPartners();
//setInterval(downloadPartners, 120000);
setInterval(downloadFeed, 15000);
setInterval(downloadSchedule, 20000);
// setInterval(switchCss, 600000);
setInterval(checkPageStatus, 5000);
       $('link[href="css/style1.css"]').attr('href','css/style2.css');


</script>


EOF;

/*
foreach ($feed as $item) {
  $content .= "<div class=\"col-sm-3\"><div class=\"feed_item\">";
//  $content .= "<pre>".print_r($item, true)."</pre>";
  $content .= "<img src=\"img/".$item['type'].".png\" class=\"feed_type\"/>\n";
  $content .= "<img src=\"".$item['avatar']."\" class=\"feed_avatar img-rounded\"/>\n";
  $content .= "<div class=\"feed_title\">";
  $content .= "  <div class=\"feed_author\">".$item['author']."</div> ";
  $content .= "  <div class=\"feed_time\">".time2str($item['time'])."</div>";
  $content .= "</div>\n";
  $content .= "<div class=\"feed_text\">".$item['text']."</div>";
  $content .= (isset($item['image']) && ($item['image'] != '')) ? "<img src=\"".$item['image']."\"/ class=\"feed_image\">\n" : '';
  $content .= "<a href=\"".$item['link']."\">link</a>";
  $content .= "</div>";
  $content .= "</div>\n\n";
}
*/

require('template.php');
?>