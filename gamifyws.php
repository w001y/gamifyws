<?php

namespace gamifyws;

/**
 * GAMIFY.WS
 *
 * This is a PHP library for the gamify.ws Gamification Web service API.
 *
 * Questions? Tweet me @w001y - I'll get back to you asap..
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
    var $master_url = "http://gamify.ws";

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
     * Connect to the GamifyWS API for a given account.
     *
     * @param string $ns        Your GamifyWS namespace
     * @param string $secure    Whether or not this should use a secure connection
     */
    function __construct($ns, $api_key, $api_secret, $secure=false) {
        $this->secure   = $secure;
        $this->api_url  = $this->master_url."/api/v" . $this->version . "/";
        $this->ns       = $ns;
        $this->api_key  = $api_key;
        $this->api_secret = $api_secret;
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
     * Get awarded actions.
     *
     * @param array $params - please see doco for allowed params. Used for filtering & searching.
     * @return json
     */
    public function get_awarded($object, $params = array())
    {
        $params['verb']     = 'GET';
        return $this->api($object."_awarded", $params);
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
     * Create or update an action.
     *
     * Simply add an 'update' param to update an existing object.
     *
     * @param $params - please see doco for required params.
     * @return json
     */
    public function create_action($params)
    {
        $params['verb']     = 'POST';
        return $this->api('actions', $params);
    }




    /**
     * Create an action group.
     *
     * Simply add an 'update' param to update an existing object.
     *
     * @param $params - please see doco for required params.
     * @return json
     */
    public function create_action_group($params)
    {
        $params['verb']     = 'POST';
        return $this->api('action_groups', $params);
    }


    /**
     * Create a level.
     *
     * Simply add an 'update' param to update an existing object.
     *
     * @param $params - please see doco for required params.
     * @return json
     */
    public function create_level($params)
    {
        $params['verb']     = 'POST';
        return $this->api('levels', $params);
    }


    /**
     * Create a badge.
     *
     * Simply add an 'update' param to update an existing object.
     *
     * @param $params - please see doco for required params.
     * @return json
     */
    public function create_badge($params)
    {
        $params['verb']     = 'POST';
        return $this->api('badges', $params);
    }

    /**
     * Register an event.
     *
     * @param $params - please see doco for required params.
     * @return json
     */
    public function register_event($params)
    {
        $params['verb']     = 'POST';
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

        if($params['verb'] == "POST")
        {
            $url = $this->api_url.$params['object'];
        }
        else
        {
            $url = $this->api_url.$params['object']."/".$this->ns;
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


        /*echo $url."<br />";
        var_dump($fields_string);
        echo "<br />----<br />";
        echo $output;
        echo "<br />----<br />";*/


        // close curl resource to free up system resources
        curl_close($ch);

        return $output;
    }


}