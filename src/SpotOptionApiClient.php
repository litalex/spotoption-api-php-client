<?php
namespace Algomonster\SpotOption;


use GuzzleHttp\Client;


class ApiClient {
    

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

    CONST MODULE_DEPOSITS_LOG_NAME_FOR_FILTER_CUSTOMER_ID = 'FILTER[customerId]';

    CONST MODULE_DEPOSITS_LOG_NAME_FOR_FILTER_DATE_MIN = 'FILTER[date][min]';

    CONST MODULE_DEPOSITS_LOG_NAME_FOR_FILTER_DATE_MAX = 'FILTER[date][max]';

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
     * @var array additional brand-specific parameters to be added to request
     */
    public $addToRequest = [];


    private $httpClient;


    public function __construct($options = []){
        $this->configure($options);
    }


    public function configure($options){
        

        foreach($this as $key => $value) {
            if (array_key_exists($key, $options)){
                $this->$key = $options[$key];
            }
        }

        

        $url = $this->protocol . "://" . $this->server;

        $this->httpClient = new Client(['base_uri' => $url]);
    }


    protected function sendRequest($data){
        $apiData = array(
            'api_username' => $this->username,
            'api_password' => $this->password,
        );

        foreach($data as $variable => $value){
            $apiData[$variable] = $value;
        }
        foreach($this->addToRequest as $variable => $value){
            $apiData[$variable] = $value;
        }

        
        $response = $this->httpClient->request('POST', $this->script, ['query'=>$apiData]);

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

        if ($result["operation_status"] = "failed") {
            throw new \Exception("Error Processing Request", 1);       
        }

        return $result;
    }

   
    

    
    public function getDepositsLog($customerIds=[], $minTime="", $maxTime="")
    {
        $data = [
            static::NAME_FOR_MODULE => static::MODULE_DEPOSITS_LOG,
            static::NAME_FOR_COMMAND => static::MODULE_DEPOSITS_LOG_COMMAND_VIEW,
        ];
        
        if (!empty($customerIds)){
            if (count($customerIds) > 1) {
                foreach ($customerIds as $customerId) {
                   $data[static::MODULE_DEPOSITS_LOG_NAME_FOR_FILTER_CUSTOMER_ID . "[]"] = $customerId;
                }
            }
            else {
                $data[static::MODULE_DEPOSITS_LOG_NAME_FOR_FILTER_CUSTOMER_ID] = $customerId;
            }
        }

        if(!empty($minTime)) {
            $data[static::MODULE_DEPOSITS_LOG_NAME_FOR_FILTER_DATE_MIN] = $minTime;
        }
        if(!empty($maxTime)) {
            $data[static::MODULE_DEPOSITS_LOG_NAME_FOR_FILTER_DATE_MAX] = $maxTime;
        }

        $response = $this->sendRequest($data);
        $result = $response[static::MODULE_DEPOSITS_LOG];

        return $result;
    }


}