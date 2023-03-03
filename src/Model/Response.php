<?php

namespace App\Model;
//Renommer succès en status
// Response Object
class Response {
    // define private variables
    private $_success;
    // create empty message array
    private $_message ="";
    // define data variable
    private $_data;
    // define http status code variable
    private $_httpStatusCode;
    // define cacheable variable and default to false (specifically say if we can cache response)
    private $_toCache = false;
    // create empty response data array - will be returned as json response
    private $_responseData = array();

    // define setSuccess flag method - true or false
    public function setSuccess($success) {
        $this->_success = $success;
    }

    // define addMessage method - can add any error or information info
    public function addMessage($message) {
        $this->_message = $message;
    }

    // define setData method - can be used to add any data
    public function setData($data) {
        $this->_data = $data;
    }

    // define setHttpStatusCode method - numeric status code
    public function setHttpStatusCode($httpStatusCode) {
        $this->_httpStatusCode = $httpStatusCode;
    }

    // define toCache flag method - true or false
    public function toCache($toCache) {
        $this->_toCache = $toCache;
    }

    // define the send method - this will send the built response object to the browser
    // in json format
    public function send() {
        // set response header contact type to json utf-8
        header('Content-type:application/json;charset=utf-8');

        // if response is cacheable then add http cache-control header with a timeout of 60 seconds
        // else set no cache
        if($this->_toCache) {
            header('Cache-Control: max-age=60');
        }
        else {
            header('Cache-Control: no-cache, no-store');
        }

        // if response is not set up correctly, e.g. not numeric in status code or success not true or false
        // send a error response
        //($this->_success !== false && $this->_success !== true )
        if(!is_numeric($this->_httpStatusCode) || $this->_success === null) {
            // set http status code in response header
            http_response_code(500);
            // set statusCode in json response
            //$this->_responseData['statusCode'] = 500;
            // set success flag in json response
            //$this->_responseData['success'] = false;
            // set custom error message
            if ($this->_success === null)
                $this->addMessage("Veuillez préciser si le status de succès est vrai ou faux dans votre construction");
            else {
                $this->addMessage("Le code de status http n'est pas numérique ou n'existe pas");
            }
            // set message in json response
            $this->_responseData['message'] = $this->_message;
        }
        else {
            // set http status code in response header
            http_response_code($this->_httpStatusCode);
            // set statusCode in json response
            //$this->_responseData['statusCode'] = $this->_httpStatusCode;
            // set success flag in json response
//            $this->_responseData['status'] = $this->_success;
            // set message in json response
            if($this->_message !== "")
                $this->_responseData['message'] = $this->_message;
            // set data array in json response
            if(http_response_code($this->_httpStatusCode) === 200 && $this->_data !== null)
                $this->_responseData['data'] = $this->_data;
        }

        // encode the responseData array to json response output
        echo json_encode($this->_responseData,JSON_UNESCAPED_UNICODE);
    }
}
