<?php


namespace Algomonster\SpotOptionApi;


class SpotOptionApiClient {
    

    /**
     * API constants
     */    


    CONST NAME_FOR_MODULE  = 'MODULE';

    CONST NAME_FOR_COMMAND = 'COMMAND';


    /**
     * DepositsLog module constants
     */
    CONST MODULE_DEPOSITS_LOG = 'DepositsLog';

    CONST MODULE_DEPOSITS_LOG_COMMAND_VIEW = 'view';

    /**
     * @var string
     */
    public $username = '';

    /**
     * @var string
     */
    public $password = '';

    /**
     * @var string
     */
    public $protocol = 'http';

    /**
     * @var int
     */
    public $port = 80;

    /**
     * @var string
     */
    public $server = '';

    public $script = '';

    /**
     * @var string Some string which be added to URL
     */
    public $addToRequest = '';


    protected function sendRequest($data){
        $apiData = array(
            'api_username' => $this->username,
            'api_password' => $this->password,
        );

        foreach($data as $variable => $value){
            $apiData[$variable] = $value;
        }
        $url = $this->protocol . "://" . $this->server ."/". $this->script;


        $apiData = http_build_query($apiData);

        if ($this->addToRequest){
            $apiData .= ("&" . $this->addToRequest);
        }

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $apiData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PORT, $this->port);

        // For bank de binary

        $response = curl_exec($ch);

        if ($response === false){
            $message = curl_error($ch);
            $code = curl_errno($ch);
            throw new Exception("CURL error: $code. " . $message);
        }

        curl_close($ch);

        $result = $this->processResult($response, $apiData);

        return $result;
    }

   
    

    
    public function getDepositsLog()
    {
        $data = [
            static::NAME_FOR_MODULE => static::MODULE_DEPOSITS_LOG,
            static::NAME_FOR_COMMAND => static::MODULE_DEPOSITS_LOG_COMMAND_VIEW,
        ];
        $response = $this->sendRequest($data);
        $result = $response[static::MODULE_DEPOSITS_LOG];

        return $result;
    }


}