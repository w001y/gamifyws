<?php

namespace gamifyws;

/**
 * GAMIFY.WS
 *
 * This is a PHP library for the gamify.ws Gamification Web service API.
 *
 * Questions? Tweet me @gamifyws - I'll get back to you asap..
 *
 * Reckon you can do a better job, or spin one up using a different language, or a certain framework?
 * Please do, that would rule - drop me a tweet! @w001y
 *
 */

class gamifyws {

    /**
     * API Version Number
     */
    var $version = "1";

    /**
     * API Domain URL
     */
    var $master_url = "https://gamify.ws";

    /**
     * Default to a 300 second timeout on server calls
     */
    var $timeout = 300;

    /**
     * Default to a 8K chunk size
     */
    var $chunkSize = 8192;

    /**
     * Cache the user api_key so we only have to log in once per client instantiation
     */
    var $api_url;


    var $secure = false;


    /**
     * The gamify.ws token
     */


    /**
     * Connect to the GamifyWS API for a given account.
     *
     * @param $ns
     * @param $api_key
     * @param $api_secret
     * @param bool $secure
     */
    function __construct($ns, $api_key, $api_secret, $secure=false) {
        $this->secure   = $secure;
        $this->api_url  = $this->master_url."/api/v" . $this->version . "/";
        $this->ns       = $ns;
        $this->api_key  = $api_key;
        $this->api_secret = $api_secret;


        if(!isset($_SESSION['salt']))
        {
            $json           = $this->get_salt();
            $json_object    = json_decode($json);
            if(!isset($json_object->data->salt) || (trim($json_object->data->salt) == ""))
            {
                echo $json_object;
                exit;
            }

            $_SESSION['salt'] = $json_object->data->salt;
        }

        $this->token = $this->get_token($_SESSION['salt']);

    }


    /**
     * Generate a token for use with the API.
     *
     * @param $salt     - Generate a salt using get_salt.
     * @return string
     */
    public function get_token($salt)
    {
        return sha1($this->api_secret.$salt);
    }

    /**
     * Request a salt from the API.
     *
     * @return json array marking success or failure.
     */
    public function get_salt()
    {
        $params = array();
        $params['object']   = 'salt';
        $params['verb']     = 'GET';
        return $this->api_send($this->api_url, $params);
    }

    /**
     * Get your API Key from an in-app call
     *
     * @return string
     */
    public function get_api_key()
    {
        return $this->api_key;
    }

    /**
     * Get your API Secret from an in-app call
     *
     * @return string
     */
    public function get_api_secret()
    {
        return $this->api_secret;
    }

    /**
     * Get your scheme details.
     *
     * @param array $params - please see doco for allowed params.
     * @return json
     */
    public function get_scheme_details($params = array())
    {
        $params['verb']     = 'GET';
        return $this->api('scheme', $params);
    }

    /**
     * Get actions.
     *
     * @param array $params - please see doco for allowed params. Used for filtering & searching.
     * @return json
     */
    public function get_actions($params = array())
    {
        $params['verb']     = 'GET';
        return $this->api('actions', $params);
    }


    /**
     * Get actions groups.
     *
     * @param array $params - please see doco for allowed params. Used for filtering & searching.
     * @return json
     */
    public function get_action_groups($params = array())
    {
        $params['verb']     = 'GET';
        return $this->api('action_groups', $params);
    }


    /**
     * Get badges.
     *
     * @param array $params - please see doco for allowed params. Used for filtering & searching.
     * @return json
     */
    public function get_badges($params = array())
    {
        $params['verb']     = 'GET';
        return $this->api('badges', $params);
    }


    /**
     * Get levels.
     *
     * @param array $params - please see doco for allowed params. Used for filtering & searching.
     * @return json
     */
    public function get_levels($params = array())
    {
        $params['verb']     = 'GET';
        return $this->api('levels', $params);
    }


    /**
     * Get users.
     *
     * @param array $params - please see doco for allowed params. Used for filtering & searching.
     * @return json
     */
    public function get_users($params = array())
    {
        $params['verb']     = 'GET';
        return $this->api('users', $params);
    }


    /**
     * Get events.
     *
     * @param array $params - please see doco for allowed params. Used for filtering & searching.
     * @return json
     */
    public function get_events($params = array())
    {
        $params['verb']     = 'GET';
        return $this->api('events', $params);
    }


    /**
     * Get awarded actions / action_groups / badges / levels / points.
     *
     * @param $object_type
     * @param int $limit
     * @param int $offset
     * @param null $user_id
     * @param $object_name
     * @return mixed|string
     */
    public function get_awarded($object_type, $limit = 100, $offset = 0, $user_id = null, $object_name = null)
    {
        $params['verb']         = 'GET';
        $params['limit']        = $limit;
        $params['offset']       = $offset;
        $params['user_id']      = $user_id;

        $object = substr($object_type,0,-1);
        $params[$object] = $object_name;

        return $this->api($object_type."_awarded", $params);
    }


    /**
     * Get leaderboard.
     *
     * @param array $params - please see doco for allowed params. Used for filtering & searching.
     * @return json
     */
    public function get_leaderboard($params = array())
    {
        $params['verb']     = 'GET';
        return $this->api('leaderboard', $params);
    }


    /**
     * Create an action.
     *
     * @param $action_name
     * @return mixed|string
     */
    public function create_action($action_name)
    {
        $params                 = array();
        $params['verb']         = 'POST';
        $params['token']        = $this->token;
        $params['action_name']  = $action_name;
        return $this->api('actions', $params);
    }


    /**
     *
     * Update an action.
     *
     * @param $action_name
     * @param null $new_name
     * @return mixed|string
     */
    public function update_action($action_name, $new_name = null)
    {
        $params                 = array();
        $params['verb']         = 'POST';
        $params['token']        = $this->token;
        $params['action_name']  = $action_name;
        $params['update']       = 'true';
        $params['new_name']     = $new_name;
        return $this->api('actions', $params);
    }


    /**
     *
     * Deactivate an action.
     *
     * @param $action_name
     * @return mixed|string
     */
    public function deactivate_action($action_name)
    {
        $params                 = array();
        $params['verb']         = 'POST';
        $params['token']        = $this->token;
        $params['action_name']  = $action_name;
        $params['update']       = 'true';
        $params['active']       = '0';
        return $this->api('actions', $params);
    }


    /**
     *
     * Activate an action.
     *
     * @param $action_name
     * @return mixed|string
     */
    public function activate_action($action_name)
    {
        $params                 = array();
        $params['verb']         = 'POST';
        $params['token']        = $this->token;
        $params['action_name']  = $action_name;
        $params['update']       = 'true';
        $params['active']       = '1';
        return $this->api('actions', $params);
    }


    /**
     * Create an action group.
     */

    /**
     * @param $action_group_name
     * @param null $actions
     *
     * $actions needs to be a pipe-delimited list of actions. Actions themselves are comma delimited lists in the following format:
     *
     * [type (A or AG for action or action group],[Action Name],[Number of attainments to fulfil action group]
     *
     * Simple example:
     * A,User Logs In,3
     *
     * Advanced Example:
     * A,User Logs In,3|AG,Signup Action Group,1|A,User Validates Account,1
     *
     *
     * @param string $multi
     *
     * BE VERY CAREFUL With $multi - setting it to '1' allows the action to be attained multiple times, possibly giving the user a lot of points.
     *
     * @return mixed|string
     */
    public function create_action_group($action_group_name, $actions = null, $multi = '0')
    {
        $params['verb']                 = 'POST';
        $params['token']                = $this->token;
        $params['action_group_name']    = $action_group_name;
        $params['actions']              = $actions;
        $params['multi']                = $actions;
        return $this->api('action_groups', $params);
    }

    /**
     * Update an action group name
     *
     * @param $action_group_name
     * @param null $new_name
     * @return mixed|string
     */
    public function update_action_group($action_group_name, $new_name = null)
    {
        $params['verb']                 = 'POST';
        $params['token']                = $this->token;
        $params['action_group_name']    = $action_group_name;
        $params['new_name']             = $new_name;
        $params['update']               = 'true';
        return $this->api('action_groups', $params);
    }

    /**
     * Update an action group's actions.
     *
     * Note that your actions as specified here are appended to the existing actions.
     * If you need to delete actions from the action group, you are best rebuilding the action group from the ground up.
     *
     * @param $action_group_name
     * @param null $actions
     * @return mixed|string
     */
    public function update_action_group_actions($action_group_name, $actions = null)
    {
        $params['verb']                 = 'POST';
        $params['token']                = $this->token;
        $params['action_group_name']    = $action_group_name;
        $params['actions']              = $actions;
        $params['update']               = 'true';
        return $this->api('action_groups', $params);
    }


    public function deactivate_action_group($action_group_name)
    {
        $params                         = array();
        $params['verb']                 = 'POST';
        $params['token']                = $this->token;
        $params['action_group_name']    = $action_group_name;
        $params['update']               = 'true';
        $params['active']               = '0';
        return $this->api('actions', $params);
    }

    public function activate_action_group($action_group_name)
    {
        $params                         = array();
        $params['verb']                 = 'POST';
        $params['token']                = $this->token;
        $params['action_group_name']    = $action_group_name;
        $params['update']               = 'true';
        $params['active']               = '1';
        return $this->api('actions', $params);
    }


    /**
     * Create a level.
     *
     * @param $level_name
     * @param $num_points
     * @return mixed|string
     */
    public function create_level($level_name, $num_points)
    {
        $params['verb']             = 'POST';
        $params['token']            = $this->token;
        $params['level_name']       = $level_name;
        $params['points']           = $num_points;
        return $this->api('levels', $params);
    }


    public function update_level($level_name, $new_level_name)
    {
        $params['verb']             = 'POST';
        $params['token']            = $this->token;
        $params['level_name']       = $level_name;
        $params['new_name']         = $new_level_name;
        $params['update']           = 'true';
        return $this->api('levels', $params);
    }

    public function update_level_points($level_name, $num_points)
    {
        $params['verb']             = 'POST';
        $params['token']            = $this->token;
        $params['level_name']       = $level_name;
        $params['points']           = $num_points;
        $params['update']           = 'true';
        return $this->api('levels', $params);
    }

    public function deactivate_level($level_name)
    {
        $params['verb']             = 'POST';
        $params['token']            = $this->token;
        $params['level_name']       = $level_name;
        $params['active']           = "0";
        $params['update']           = 'true';
        return $this->api('levels', $params);
    }

    public function activate_level($level_name)
    {
        $params['verb']             = 'POST';
        $params['token']            = $this->token;
        $params['level_name']       = $level_name;
        $params['active']           = "1";
        $params['update']           = 'true';
        return $this->api('levels', $params);
    }


    /**
     * Create a badge.
     *
     * @param $badge_name
     * @param $url - URL of the PNG resource (most be a PNG!)
     * @param $badge_type - "points" or "action_group"
     * @param $badge_value - if $badge_type is points, must be a positive integer. If action_group, supply action group name.
     * @return mixed|string
     */
    public function create_badge($badge_name, $url, $badge_type, $badge_value)
    {
        $params['verb']             = 'POST';
        $params['token']            = $this->token;
        $params['badge_name']       = $badge_name;
        $params['url']              = $url;
        $params['badge_type']       = $badge_type;
        $params['badge_value']      = $badge_value;
        return $this->api('badges', $params);
    }


    public function update_badge($badge_name, $new_name)
    {
        $params['verb']             = 'POST';
        $params['token']            = $this->token;
        $params['badge_name']       = $badge_name;
        $params['update']           = 'true';
        $params['new_name']         = $new_name;
        return $this->api('badges', $params);
    }

    public function update_badge_image($badge_name, $url)
    {
        $params['verb']             = 'POST';
        $params['token']            = $this->token;
        $params['badge_name']       = $badge_name;
        $params['update']           = 'true';
        $params['url']              = $url;
        return $this->api('badges', $params);
    }

    public function update_badge_type($badge_name, $badge_type, $new_value)
    {
        $params['verb']             = 'POST';
        $params['token']            = $this->token;
        $params['badge_name']       = $badge_name;
        $params['update']           = 'true';
        $params['badge_type']       = $badge_type;
        $params['badge_value']      = $new_value;
        return $this->api('badges', $params);
    }

    public function deactivate_badge($badge_name)
    {
        $params['verb']             = 'POST';
        $params['token']            = $this->token;
        $params['badge_name']       = $badge_name;
        $params['update']           = 'true';
        $params['active']           = '0';
        return $this->api('badges', $params);
    }

    public function activate_badge($badge_name)
    {
        $params['verb']             = 'POST';
        $params['token']            = $this->token;
        $params['badge_name']       = $badge_name;
        $params['update']           = 'true';
        $params['active']           = '1';
        return $this->api('badges', $params);
    }


    /**
     * Register an event.
     *
     * Note that in order for the API to allow users to carry out actions more than once, $element_id must be set.
     *
     * @param $action
     * @param $user_id
     * @param null $user_name
     * @param null $element_id
     * @param null $user_icon_url
     * @return mixed|string
     */
    public function register_event($action, $user_id, $element_id = null, $user_name = null, $user_icon_url = null)
    {
        $params['verb']             = 'POST';
        $params['token']            = $this->token;
        $params['action']           = $action;
        $params['user_id']          = $user_id;
        $params['user_name']        = $user_name;
        $params['element_id']       = $element_id;
        $params['user_icon_url']    = $user_icon_url;
        return $this->api('events', $params);
    }







    /**
     * PRIVATE FUNCTIONS
     *
     *    .. probably best not piddle about with the code underneath this code block.
     *
     */


    private function api($object, $params)
    {
        $params['ns'] 	    = $this->ns;
        $params['object'] 	= $object;

        // Required $params: object, verb, token (if POST or recording data to API)

        return $this->api_send($this->api_url, $params);
    }


    private function api_send($url, $params)
    {
        $object = $params['object'];

        // Disallow the object param from being sent to the API
        unset($params['object']);

        $fields_string = "";
        foreach($params as $key=>$value) {

            if($key != 'verb') // Disallow the HTTP verb from going across
            {
                if(is_array($value))
                {
                    foreach($value as $minikey => $minvalue)
                    {
                        $fields_string .= $key.'['.$minikey.']'.'='.$minvalue.'&';
                    }
                }
                else
                {
                    $fields_string .= $key.'='.urlencode($value).'&';
                }
            }
        }

        // Lop off the RHS ampersand
        $fields_string = substr($fields_string, 0, -1);


        // create curl resource
        $ch = curl_init();

        // SSL
        curl_setopt($ch, CURLOPT_SSLVERSION,3);

        if($params['verb'] == "POST")
        {
            $url = $this->api_url.$object;
        }
        else
        {
            $url = $this->api_url.$object."/".$this->ns;
        }

        if($params['verb'] == "POST")
        {
            curl_setopt($ch,CURLOPT_POST, count($params));
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

        }
        else
        {
            $url = $url."?".$fields_string;
        }

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);



        // $output contains the output string
        $output = curl_exec($ch);

        $error = curl_error($ch);

        /*
        // A bit of debug
        echo $url."<br />";
        var_dump($fields_string);
        echo "<br />----<br />";
        echo $output;
        echo "<br />----<br />";*/


        // close curl resource to free up system resources
        curl_close($ch);


        if(isset($error) && (trim($error) != ""))
        {
            $output = $error;
        }


        return $output;
    }


}