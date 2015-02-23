<?php

require_once realpath(dirname(__FILE__) . '/init.php');
require_once realpath(dirname(__FILE__) . '/xml.php');

function get_config_value($key, $default) {
  global $db;
  $query = sprintf("SELECT * FROM feed_info WHERE `key` = '%s'",
    mysqli_escape_my($key)
  );
  if (!($result = mysqli_query($db, $query))) {
    throw new Exception(mysqli_error($db));
  }
  if ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    return $row['value'];
  }
  return $default; 
}

function set_config_value($key, $value) {
  global $db;
  $query = sprintf("SELECT * FROM feed_info WHERE `key` = '%s'",
    mysqli_escape_my($key)
  );
  if (!($result = mysqli_query($db, $query))) {
    throw new Exception(mysqli_error($db));
  }
  if ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $query = sprintf("UPDATE feed_info SET `value` = '%s' WHERE `key` = '%s' ",
      mysqli_escape_my($value),
      mysqli_escape_my($key)
    );
  } else {
    $query = sprintf("INSERT INTO feed_info (`key`, `value`) VALUES ('%s', '%s')",
      mysqli_escape_my($key),
      mysqli_escape_my($value)
    );
  }
  if (!($result = mysqli_query($db, $query))) {
    throw new Exception(mysqli_error($db));
  }
}

function get_news($services, $params, $info) {
  global $db, $config;

  $config_sha = sha1(json_encode($config));
  $config_sha_old = get_config_value('config_sha', '');
  $force_refresh = false;
  if ($config_sha != $config_sha_old) {
/*
    $query = "TRUNCATE `feed`";
    if (!($result = mysqli_query($db, $query))) {
      throw new Exception(mysqli_error($db));
    }
    */
    set_config_value('config_sha', $config_sha);
    $force_refresh = true;
  }

  $refresh = (int)get_config_value('last_cache_refresh', 0);
  $age = time() - $refresh;

  $query = "SELECT *, UNIX_TIMESTAMP(time) as time FROM `feed` ORDER BY `time` DESC";
  if (!($result = mysqli_query($db, $query))) {
    throw new Exception(mysqli_error($db));
  }
  $feed_cache = array();
  while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    array_push($feed_cache, $row);
  }
  $feed_cache = array_reverse($feed_cache);

  if (($age < $info['cache_max_age']) && !$force_refresh) {
    return $feed_cache;
  }

  set_config_value('last_cache_refresh', time());

  $feed = download_news($services, $params);

  $query = "START TRANSACTION";
  if (!($result = mysqli_query($db, $query))) {
    throw new Exception(mysqli_error($db));
  }

  // FIXME last_cache_time for certain type of feed
  $last_cache_time = end($feed_cache)['time'];
  foreach ($feed as $item) {
    $found = false;
    foreach ($feed_cache as $citem) {
      if ( (($citem['time'] == $item['time'])) && ($citem['type'] == $item['type']))  {
        $found = true;
      }
    }

    if (!$found) {
      $query = sprintf("INSERT INTO `feed` (`id`, `type`, `avatar`, `author`, `text`, `image`, `link`, `time`) VALUES (NULL, '%s', '%s', '%s', '%s', '%s', '%s', FROM_UNIXTIME(%s));",
        mysqli_escape_my($item['type']),
        mysqli_escape_my($item['avatar']),
        mysqli_escape_my($item['author']),
        mysqli_escape_my($item['text']),
        mysqli_escape_my($item['image']),
        mysqli_escape_my($item['link']),
        mysqli_escape_my($item['time'])
      );
      if (!($result = mysqli_query($db, $query))) {
        throw new Exception(mysqli_error($db));
      }
      array_push($feed_cache, $item);
    }

  }
  set_config_value('last_cache_refresh', time());
  $query = "COMMIT";
  if (!($result = mysqli_query($db, $query))) {
    throw new Exception(mysqli_error($db));
  }
  return $feed_cache;

}


function download_cache() {
  return json_decode(file_get_contents('cache/feed.json'), true);
}

function download_news($services, $params) {
  $feed = array();
  foreach ($services as $service) {
    if (!isset($service['type'])) {
      continue;
    }
    switch ($service['type']) {
      case 'test': 
        $items = download_test($service, $params);
      break;
      case 'rss':
        $items = download_rss($service, $params);
      break;
      case 'twitter_hashtag':
        $items = download_twitter_hashtag($service, $params);
      break;
      case 'twitter_homescreen':
        $items = download_twitter_homescreen($service, $params);
      break;
      case 'google_plus_hashtag':
        $items = download_google_plus_hashtag($service, $params);
      break;
      case 'facebook_hashtag':
        $items = download_facebook_hashtag($service, $params);
      break;
      case 'instagram_hashtag':
        $items = download_instagram_hashtag($service, $params);
      break;
      case 'vine_hashtag':
        $items = download_vine_hashtag($service, $params);
      break;
      case 'flickr_hashtag':
        $items = download_flickr_hashtag($service, $params);
      break;
      case 'tumblr_hashtag':
        $items = download_tumblr_hashtag($service, $params);
      break;
      case 'foursquare':
        $items = download_foursquare_fit($service, $params);
      break;
      default:
        throw new Exception('Unknown service '.print_r($service, true));
      break;
    }
    $feed = array_merge($feed, $items);

  }

  usort($feed, 'feed_sort_function');
  return $feed;

}

function feed_sort_function($a, $b) {
    if ($a['time'] == $b['time']) {
        return 0;
    }
    return ($a['time'] < $b['time']) ? -1 : 1;
}


function time2str($ts) {
    if(!ctype_digit($ts))
        $ts = strtotime($ts);

    $diff = time() - $ts;
    if($diff == 0)
        return 'now';
    elseif($diff > 0)
    {
        $day_diff = floor($diff / 86400);
        if($day_diff == 0)
        {
            if($diff < 60) return 'just now';
            if($diff < 120) return '1 minute ago';
            if($diff < 3600) return floor($diff / 60) . ' minutes ago';
            if($diff < 7200) return '1 hour ago';
            if($diff < 86400) return floor($diff / 3600) . ' hours ago';
        }
        if($day_diff == 1) return 'Yesterday';
        if($day_diff < 7) return $day_diff . ' days ago';
        if($day_diff < 31) return ceil($day_diff / 7) . ' weeks ago';
        if($day_diff < 60) return 'last month';
        return date('F Y', $ts);
    }
    else
    {
        $diff = abs($diff);
        $day_diff = floor($diff / 86400);
        if($day_diff == 0)
        {
            if($diff < 120) return 'in a minute';
            if($diff < 3600) return 'in ' . floor($diff / 60) . ' minutes';
            if($diff < 7200) return 'in an hour';
            if($diff < 86400) return 'in ' . floor($diff / 3600) . ' hours';
        }
        if($day_diff == 1) return 'Tomorrow';
        if($day_diff < 4) return date('l', $ts);
        if($day_diff < 7 + (7 - date('w'))) return 'next week';
        if(ceil($day_diff / 7) < 4) return 'in ' . ceil($day_diff / 7) . ' weeks';
        if(date('n', $ts) == date('n') + 1) return 'next month';
        return date('F Y', $ts);
    }
}

function  replaceUrlWithLinks($s) {
  return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank">$1</a>', $s);
}

////////////////////////////////////////////////////////////////////////////////

function download_test($service, $param) {
  $feed = array();
  if (0) {
  array_unshift($feed, array(
      'type'   => $service['type'],
      'avatar' => './img/avatar2.jpg',
      'author' => 'test service',
      'text' => 'This is RFC date "' .date('r', time()- 600). '" few minutes later',
      'image' => '',
      'link'   => '',
      'time' => (time()- 600),
    ));
  }

  array_unshift($feed, array(
      'type'   => $service['type'],
      'avatar' => './img/avatar1.jpg',
      'author' => 'test service',
      'text' => 'Hello world at ' .date('r'),
      'image' => '',
      'link'   => '',
      'time' => time(),
    ));

  return $feed;
}

////////////////////////////////////////////////////////////////////////////////

function download_rss($service, $param) {
  $url = $service['value']; // . "?lang=".$param['lang'];

  $stream_context = stream_context_create(
    array(
      'ssl' => array(
        'verify_peer' => FALSE,
      )
    )
  );
  $rss_xml = file_get_contents($url, NULL, $stream_context);
  $rss_data = xml2array($rss_xml);
  $rss = array();

  foreach (getSubtree(getSubtree($rss_data, 'rss'),'channel') as $channel) {
    if (!isset($channel['item'])) {
      continue;
    }
    $item = $channel['item'];

    $out = array(
      'type'   => $service['type'],
      'avatar' => $service['avatar'],
      'author' => $service['author'],
      'text' => ifSet(getSubtree($item, 'title')). ' '. strip_tags(ifSet(getSubtree($item, 'description'))),
      'image' => '',
      'link'   => ifSet(getSubtree($item, 'link')),
      'time' => strtotime(ifSet(getSubtree($item,'pubDate'))),
    );
    array_push($rss, $out);
  }

  return $rss;
}

////////////////////////////////////////////////////////////////////////////////

function download_twitter_homescreen($service, $params) {
  require_once realpath(dirname(__FILE__) . '/tmhOAuth.php');

    $service['auth']['host'] = 'api.twitter.com';
    $tmhOAuth = new tmhOAuth($service['auth']);

    $statuses_url = '1.1/statuses/user_timeline.json';
    $code = $tmhOAuth->request('GET', $tmhOAuth->url($statuses_url), array(
        'screen_name'=> $service['value'],
        'count'=> 30,
    ));
    $return = $tmhOAuth->response['response'];


    $return = json_decode($return, true);
//echo "<pre>".print_r($return, true)."</pre>";
    $rss = array();
    foreach ($return as $st) {
      if (!isset($st['user'])) {
        continue;
      }
      $out = array();
      $out['type'] = $service['type'];
      $out['avatar'] = $st['user']['profile_image_url'];
      $out['img'] = '';
      $out['author'] = "@".$st['user']['screen_name'];
      $out['text'] = replaceUrlWithLinks($st['text']);
      $out['time'] = strtotime($st['created_at']);
      $out['link'] = "https://twitter.com/".$st['user']['screen_name']."/status/".$st['id_str'];
      $out['image'] = isset($st['entities']['media'][0]['media_url']) ? $st['entities']['media'][0]['media_url'] : '';
      array_push($rss, $out);
//      $out['link'];
//      $out['time'];
    }

   return $rss;

}

////////////////////////////////////////////////////////////////////////////////

function download_twitter_hashtag($service, $params) {
  require_once realpath(dirname(__FILE__) . '/tmhOAuth.php');

    $service['auth']['host'] = 'api.twitter.com';
    $tmhOAuth = new tmhOAuth($service['auth']);

    $statuses_url = '1.1/search/tweets';
    $options = array(
      'q'=>urlencode($service['value']),
      'result_type' => 'recent',
      'count' => 50,
    );
    $code = $tmhOAuth->request('GET', $tmhOAuth->url($statuses_url), $options);
    $return = $tmhOAuth->response['response'];

    $return = json_decode($return, true);
    $rss = array();
    if (!isset($return)) {
       return $rss;
    }
    
    $without_retweets = isset($service['without_retweets']) && $service['without_retweets'];
    
    foreach ($return['statuses'] as $st) {
//    echo "<pre>".print_r($st, true)."</pre>";
      if ( $without_retweets  && (isset($st['retweeted_status']))) {
        continue;
      }
      $out = array();
      $out['type'] = $service['type'];
      $out['avatar'] = $st['user']['profile_image_url'];
      $out['author'] = "@".$st['user']['screen_name'];
      $out['text'] = replaceUrlWithLinks($st['text']);
      $out['time'] = strtotime($st['created_at']);
      $out['link'] = "https://twitter.com/".$st['user']['screen_name']."/status/".$st['id_str'];
      $out['image'] = isset($st['entities']['media'][0]['media_url']) ? $st['entities']['media'][0]['media_url'] : '';
      array_push($rss, $out);
    }

   return $rss;
}

////////////////////////////////////////////////////////////////////////////////

function download_google_plus_hashtag($service, $params) {
  require_once realpath(dirname(__FILE__) . '/google-api-php-client/autoload.php');
  $client = new Google_Client();
  $client->setDeveloperKey($service['auth']['api_key']);
  if (isset($service['cache_directory'])) {
    $client->setClassConfig('Google_Cache_File', array('directory' => $service['cache_directory']));
  }
  $gplus_svc = new Google_Service_Plus($client);
  
  $results = $gplus_svc->activities->search($service['value'])->getItems();

  $array = array();
  foreach ($results as $item) {
    $obj = $item->getObject();
    $att = $obj->getAttachments();
    unset($image);

    if (isset($att[0])) {
      switch ($att[0]->getObjectType()) {
        case 'photo':
          $image = $att[0]->getImage()->getUrl();
        break;
        case 'album':
          if (isset($att[0]->getThumbnails()[0])) {
            $image = $att[0]->getThumbnails()[0]->getImage()->getUrl();
          }
        break;
        case 'video':
          $image= $att[0]->getImage()->getUrl();
        break;
        case 'article':
        break;
      }
    }
//echo "<pre>".print_r($att->getType(), true)."</pre>";

    $out = array(
      'type'   => $service['type'],
      'avatar' => $item->getActor()->getImage()->getUrl(),
      'author' => $item->getActor()->getDisplayName(),
      'text' => $item->getTitle(),
      'image'=> isset($image) ? $image : '',
      'link'   => $obj->getUrl(),
      'time' => strtotime($item->getPublished()),
    );
    array_push($array, $out);
  }

  return $array;
}

////////////////////////////////////////////////////////////////////////////////


function download_flickr_hashtag($service, $params) {
  require_once realpath(dirname(__FILE__) . '/Phlickr/Api.php');
 
  $api = new Phlickr_Api($service['auth']['api_key'],$service['auth']['api_secret']);

  $response = $api->executeMethod('flickr.photos.search',
    array(
      'tags' => $service['value'],
      'per_page' => 12,
      'sort' => 'date-posted-desc',
      'media' => 'photos',
      'privacy_filter' => 1,
      'extras' => 'owner_name,date_upload,icon_server'
    )
  );

  $xml =  $response->xml;

  $array = array();
  foreach($xml->photos->photo as $photo) { 
//echo "<pre>".print_r($photo, true)."</pre>";
    $attr = $photo->attributes();
    $image = sprintf('http://farm%s.static.flickr.com/%s/%s_%s.jpg' , $attr->farm, $attr->server, $attr->id, $attr->secret);
    $link = sprintf('https://www.flickr.com/photos/%s/%s ', $attr->owner, $attr->id);
    $avatar = sprintf('http://farm%s.staticflickr.com/%s/buddyicons/%s.jpg', $attr->iconfarm, $attr->iconserver, $attr->owner);
    $out = array(
      'type'   => $service['type'],
      'avatar' => $avatar,
      'author' => $attr->ownername,
      'text' => $attr->title,
      'image' => $image,
      'link'   => $link,
      'time' => $attr->dateupload,
    );
//echo "<pre>".print_r($out, true)."</pre>";
    array_push($array, $out);
  }


  return $array;
}


////////////////////////////////////////////////////////////////////////////////

function download_instagram_hashtag($service, $params) {
  require_once realpath(dirname(__FILE__) . '/Instagram.php');

  $instagram = new Instagram($service['auth']);
  $array = array();

  $result = $instagram->getTagMedia($service['value']);
  foreach ($result->data as $media) {
    array_push($array, array(
      'type'   => $service['type'],
      'avatar' => $media->user->profile_picture,
      'author' => $media->user->username,
      'text' => $media->caption->text,
      'image' => $media->images->standard_resolution->url,
      'link'   => $media->link,
      'time' => $media->created_time,
    ));


  }




  return $array;
}

////////////////////////////////////////////////////////////////////////////////

function download_vine_hashtag($service, $params) {

  require_once realpath(dirname(__FILE__) . '/Vine-PHP/vine.php');

  Vine::login($service['auth']['user'], $service['auth']['password']);
  $videos = Vine::get_tag($service['value']);
  $array = array();

  foreach($videos as $video) {
//    echo "<pre>".print_r($video,true)."</pre>";
    array_push($array, array(
      'type'   => $service['type'],
      'avatar' => $video->avatarUrl,
      'author' => $video->username,
      'text' => $video->description,
      'image' => $video->thumbnailUrl,
      'link'   => $video->permalinkUrl,
      'time' => strtotime($video->created),
    ));
//	echo $video->videoUrl . "\n";
  }


  return $array;
}


////////////////////////////////////////////////////////////////////////////////

function download_facebook_hashtag($service, $params) {
  require_once realpath(dirname(__FILE__) . '/facebook-sdk/facebook.php');

  $facebook = new Facebook($service['auth']);

  $today = time()-86400;
  $response = $facebook->api('/v1.0/search?type=post&q='.urlencode($service['value']).'&limit=20&until='.$today, 'GET');

  $array = array();
  foreach ($response['data'] as $item) {
//    if (!in_array($item['type'], array('status', 'photo', 'link'))) continue;

    array_push($array, array(
      'type'   => $service['type'],
      'avatar' => 'http://graph.facebook.com/'.$item['from']['id'].'/picture',
      'author' => $item['from']['name'],
      'text' => isset($item['message']) ? $item['message'] : $item['name'],
      'image' => isset($item['picture']) ? $item['picture'] : '',
      'link'   => isset($item['link']) ? $item['link'] : '',
      'time' => strtotime($item['created_time']),
    ));

  }

  return $array;
}

////////////////////////////////////////////////////////////////////////////////

function download_tumblr_hashtag($service, $params) {
  return array();
}

////////////////////////////////////////////////////////////////////////////////

function download_foursquare_fit($service, $params) {

  $array = array();

  $data = json_decode(file_get_contents($service['value']), true);

  $event_start = strtotime($service['event_start']);
  $event_end =strtotime($service['event_end']);

  foreach ($data as $checkin) {


    if (!isset($checkin['createdAt'])) {
      continue;
    }
    if (!isset($checkin['venue'])) {
      continue;
    }
    if (!isset($checkin['user'])) {
      continue;
    }

    if ($checkin['createdAt'] < $event_start) {
      continue;
    }

    if ($checkin['createdAt'] > $event_end) {
      continue;
    }


    $checkin_message =  (isset($checkin['shout'])) ? $checkin['shout'] : 
      sprintf($service['default_checkin_message'], $checkin['venue']['name']);

    $firstName = isset($checkin['user']['firstName']) ? $checkin['user']['firstName'] : '';
    $lastName = isset($checkin['user']['lastName']) ? $checkin['user']['lastName'] : '';

    array_push($array, array(
      'type'   => $service['type'],
      'avatar' => $checkin['user']['photo']['prefix'].'64'.$checkin['user']['photo']['suffix'],
      'author' => $firstName. ' '.$lastName,
      'text' => $checkin_message,
      'image' => '',
      'link'   => 'https://foursquare.com/user/'.$checkin['user']['id'].'/checkin/'.$checkin['id'],
      'time' => $checkin['createdAt'],
    ));

  }

  return $array;
}

?>