<?php
echo '<pre>';
//Инициируем
$sitebill_sdk = new Sitebill_SDK('http://estate.sitebill.ru/apps/api/rest.php', 'admin', 'admin');
//Подключаемся
$sitebill_sdk->connect();

$ql_items = array(
    'topic_id' => 1,
    'city_id' => 1,
    'text' => 'Объявление из API',
    'price' => '5000000');
$result = $sitebill_sdk->native_insert('data', $ql_items);
print_r($result);
$result = $sitebill_sdk->load_grid_columns('data');
print_r($result);
echo '</pre>';

class Sitebill_SDK {
    private $session_key;
    private $queryUrl;
    private $login;
    private $password;

    function __construct( $queryUrl, $login, $password ) {
        $this->queryUrl = $queryUrl;
        $this->login = $login;
        $this->password = $password;
    }

    function connect () {
        $result = $this->get_auth($this->login, $this->password);
        if ($result['success']) {
            $this->session_key = $result['session_key'];
            return true;
        }
        return false;
    }

    function load_grid_columns ($model_name) {
        $params = array(
            'action' => 'model',
            'do' => 'load_grid_columns',
            'session_key' => $this->session_key,
            'model_name' => $model_name,
        );
        $result = $this->executeHTTPRequest($this->queryUrl, $params);
        return $result;
    }

    function native_insert ( $model_name, $ql_items ) {
        $params = array(
            'action' => 'model',
            'do' => 'native_insert',
            'session_key' => $this->session_key,
            'model_name' => $model_name,
            'ql_items' => $ql_items,
        );
        $result = $this->executeHTTPRequest($this->queryUrl, $params);
        return $result;
    }

    function get_auth ( $login, $password ) {
        $params = array(
            'action' => 'oauth',
            'do' => 'login',
            'login' => $login,
            'password' => $password,
        );
        $result = $this->executeHTTPRequest($this->queryUrl, $params);
        return $result;
    }

    function executeHTTPRequest ($queryUrl, array $params = array()) {
        $result = array();
        $queryData = http_build_query($params);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $queryUrl,
            CURLOPT_POSTFIELDS => $queryData,
        ));

        $curlResult = curl_exec($curl);
        curl_close($curl);

        if ($curlResult != '') $result = json_decode($curlResult, true);

        return $result;
    }
}

