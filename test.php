<?php
session_start();

include_once("gamifyws.php");

/**
 * Set the game's namespace
 */
$ns = "demo";
$api_key = "d7fa79e4b54614bd4befcb25edf07d3d";
$api_secret = "641dc005bb8c0333b8ecabde819bbe6b";
/**
 * Instantiate
 */


$gamify = new \GamifyWS\GamifyWS($ns, $api_key, $api_secret);

/**
 * Set the Salt
 */

if(!isset($_SESSION['salt']))
{
    $json           = $gamify->get_salt();
    $json_object    = json_decode($json);
    if(!isset($json_object->data->salt) || (trim($json_object->data->salt) == ""))
    {
        echo "<pre>";
        var_dump($json_object);
        exit;
    }

    $_SESSION['salt'] = $json_object->data->salt;
}

/**
 * Set the token
 */
$token = $gamify->get_token($_SESSION['salt']);

/**
 * Get Scheme Details
 */
$scheme_details    = $gamify->get_scheme_details();

/**
 * Get Scheme Objects (with optional params)
 */

$params = array();
$params['limit']        = 1000;   // Optional - INTEGER
$params['offset']       = 0;   // Optional - INTEGER
$params['active_only']  = 1;   // Optional - BOOLEAN - not available in all calls
//$params['name']         = "Logs";   // Optional - STRING

$actions        = $gamify->get_actions();           // or.. $gamify->get_actions($params);
$action_groups  = $gamify->get_action_groups();     // or.. $gamify->get_action_groups($params);
$badges         = $gamify->get_badges();            // or.. $gamify->get_badges($params);
$levels         = $gamify->get_levels();            // or.. $gamify->get_levels($params);
$users          = $gamify->get_users();             // or.. $gamify->get_users($params);
$events         = $gamify->get_events();            // or.. $gamify->get_events($params);


/**
 * Get listings for 'awardable' items.
 */

$params = array(); // Resetting $params for use in this section.

//$params['action']           = "Logs";
$actions_awarded            = $gamify->get_awarded('actions', $params);         // $params is optional
//$params['action_group']     = "Log";
$action_groups_awarded      = $gamify->get_awarded('action_groups', $params);   // $params is optional

//$params['badge']            = "Badge";
$badges_awarded             = $gamify->get_awarded('badges', $params);          // $params is optional

//$params['user_id']          = "A user ID";
$points_awarded             = $gamify->get_awarded('points', $params);          // $params is optional


/**
 * Get the full leaderboard, or a snapshot of it.
 */
$params['snapshot']         = "true";                               // Optional - Sets the reply to be only a snapshot
$params['user_id']          = "user id";                            // Needs snapshot=true.
$params['places_offset']    = "2";                                  // Optional - default is 2.
$leaderboard                = $gamify->get_leaderboard($params);    // $params is optional
// echo $leaderboard;

/**
 * Create Items for your game
 */

// Action
$params                     = array();
$params['action_name']      = "User Logs In";
$params['token']            = $token;
// $reply                      = $gamify->create_action($params); // $params required

// Action Group - Simple
$params                         = array();
$params['action_group_name']    = "User Logs In 3 Times";
$params['actions']              = "A,User Logs In,3";
$params['token']                = $token;
// $reply                        = $gamify->create_action_group($params); // $params required

// Action Group - Compound
$params                         = array();
$params['action_group_name']    = "User Logs In 3 Times and Leaves Review";
$params['actions']              = "AG,User Logs In 3 Times,1|A,User Leaves Review,1"; // Assumes 'User Leaves Review' is an existing action - create it before running this code!
$params['token']                = $token;
// $reply                          = $gamify->create_action_group($params); // $params required


// Level
$params                 = array();
$params['level_name']   = "Starfleet Ensign";
$params['points']       = 1;
$params['token']        = $token;
// $reply                  = $gamify->create_level($params); // $params required


// Badge
$params                 = array();
$params['badge_name']   = "Starfleet Academy Entrant Badge";
$params['badge_type']   = "points";                             // points or action_group
$params['badge_value']  = 1;
$params['url']          = "http://path_to_your_badge_image.png"; // Must be a png. Avoid latency, go for smaller images. No copyrighted images!
$params['token']        = $token;
// $reply                  = $gamify->create_badge($params); // $params required



/**
 * Register an event (record an action)
 */
$params                 = array();
$params['action']       = "User Logs In"; // User Leaves Review
$params['user_id']      = "unique user id";
$params['element_id']   = time();           // Optional - using unique element_id's allow actions to be attained multiple times.
$params['token']        = $token;
// $reply                  = $gamify->register_event($params); // $params required



/**
 * Update items within your game
 */

// Action
$params                     = array();
$params['action_name']      = "User Logs In To Website";
//$params['new_name']         = "User Logs In To Website";
$params['update']           = "true"; // API only accepts 'true' as a value for an update
$params['active']           = 0; // Anything but 1 deactivates the object.
$params['token']            = $token;
// echo $reply                      = $gamify->create_action($params); // $params required

// and so on - use the same methods as you would have to create the object, except nominate it and use the 'update' flag.

