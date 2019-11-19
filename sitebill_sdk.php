<?php
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

    /**
     * Подключение к API
     * @return bool
     */
    function connect () {
        $result = $this->get_auth($this->login, $this->password);
        if ($result['success']) {
            $this->session_key = $result['session_key'];
            return true;
        }
        return false;
    }

    /**
     * Загрузка списка колонок для модели
     * @param $model_name
     * @return array|mixed
     */
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

    /**
     * Добавление записи к модели
     * @param $model_name
     * @param $ql_items
     * @return array|mixed
     */
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

    /**
     * Удаляем запись из таблицы
     * @param $model_name
     * @param $primary_key
     * @param $key_value
     * @return array|mixed
     */
    function delete ( $model_name, $primary_key, $key_value ) {
        $params = array(
            'action' => 'model',
            'do' => 'delete',
            'session_key' => $this->session_key,
            'model_name' => $model_name,
            'primary_key' => $primary_key,
            'key_value' => $key_value,
        );
        $result = $this->executeHTTPRequest($this->queryUrl, $params);
        return $result;
    }

    /**
     * Загружаем данные о записи из таблицы
     * @param $model_name
     * @param $primary_key
     * @param $key_value
     * @return array|mixed
     */
    function load_data ( $model_name, $primary_key, $key_value ) {
        $params = array(
            'action' => 'model',
            'do' => 'load_data',
            'session_key' => $this->session_key,
            'model_name' => $model_name,
            'primary_key' => $primary_key,
            'key_value' => $key_value,
        );
        $result = $this->executeHTTPRequest($this->queryUrl, $params);
        return $result;
    }


    /**
     * Получаем ключ session_key по логину и паролю
     * @param $login
     * @param $password
     * @return array|mixed
     */
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

    private function executeHTTPRequest ($queryUrl, array $params = array()) {
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
