<?php
// TOP LEVEL VARIABLES

// SETUP: Fill in the following 4 variables
$base_url = ''; // Base LibCal URL
$api_key = ''; // LibCal API key
$client_id = ''; //LibCal client ID (found on the same page in LibCal as the API key)
$cal_ids = array('', '' ); //LibCal calendar IDs, in an array (strings of the ints)

// Start with no events
$events = array(); 
$fullevents = array();

// FUNCTIONS
// Authenticate
function getAccessToken($base_url, $key, $id){
        // Build HTTP request for AUTH token, return the token

        // TODO: Add a new function that stores the token in a cookie and checks if it exists and if it has expired before requesting a new one

        $url = $base_url . '/1.1/oauth/token';
        $data = array('client_id' => $id, 'client_secret' => $key, 'grant_type' => 'client_credentials');
        $options = array('http' => array( 'header' => "Content-type: application/x-www-form-urlencoded", 'method' => 'POST', 'content' => http_build_query($data)));
        $context = stream_context_create($options);

        $result = file_get_contents($url, false, $context);

        // Process request results
        if($result === FALSE){
            ('authorization failed');
        }else{
            $json = json_decode($result, true);

            $token = $json['access_token'];
            return $token;
        }
}

// Retrieve Events List
function getEventsResults($base_url, $token, $cal_id){
        // Build HTTP request for Events list for specific calendars

        $url = $base_url . '/1.1/events';
        $options = array('http' => array( 'method' => 'GET', 'header' => 'Content-Type:application/json', 'header'=> 'Authorization: Bearer '.$token));
        $context = stream_context_create($options);

        $result = file_get_contents($url."?cal_id=".$cal_id, false, $context);

        // Process the request results
        if ($result === FALSE){
                error_log('api request failed');
        }else{
                return $result;
        }
}

// MAIN

$token = getAccessToken($base_url, $api_key, $client_id);

// Get events from all calendars
foreach ($cal_ids as $key => $value) {
  $result = json_decode(getEventsResults($base_url, $token, $value), true);

  if($result['events'] != []){
        $events[] = $result; // Add the result to the events array
  }else{
        continue;
  }
}

// Merge the events from the different calendars into one array
foreach ($events as $key => $array){
  $fullevents = $array['events'];
}
$fullevents = array_reverse($fullevents);

// Prepare and output the JSON
$eventsJSON = json_encode($fullevents);
echo $eventsJSON;

?>