<?php


namespace Algomonster\SpotOption;

use GuzzleHttp\Client;


class Client {
    

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

    private $httpClient;


    public function __construct($options = []){
        $this->configure($options);
    }


    public function configure($options){
        
        $unsetVars = [];

        foreach($this as $key => $value) {
            if (array_key_exists($key, $options)){
                $this->$key = $options->$key;
                if(empty($this->$key)){
                    $unsetVars[] = $key;
                }
            }
        }

        if(!empty($unsetVars)){
            throw new Exception("API client misconfiguration, missing parameters: " . join(",", $unsetVars));
        }

        $url = $this->protocol . "://" . $this->server;

        $this->httpClient = new GuzzleHttp\Client(['base_uri' => $url]);
    }


    protected function sendRequest($data){
        $apiData = array(
            'api_username' => $this->username,
            'api_password' => $this->password,
        );

        foreach($data as $variable => $value){
            $apiData[$variable] = $value;
        }
        
        $response = $httpClient->request('POST', $this->script, ['query'=>$apiData]);

        $code = $response->getStatusCode(); 
        $reason = $response->getReasonPhrase();
        $body = $response->getBody();

        if ($code != 200){
            throw new Exception("Requerst error:". $code . " " . $reason);
        }

        $resultString = (string) $body;

        $xmlString = $resultString;
        $xmlObject = simplexml_load_string($xmlString);
        $json = json_encode($xmlObject);
        $result = json_decode($json, TRUE);

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