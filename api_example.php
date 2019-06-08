<?php
echo '<pre>';
$result = get_auth('admin', 'admin');
$session_key = $result['session_key'];
print_r($result);
$ql_items = array(
    'topic_id' => 1,
    'city_id' => 1,
    'text' => 'Объявление из API',
    'price' => '5000000');
$result = native_insert('data', $ql_items,$session_key);
print_r($result);
$result = load_grid_columns('data', $session_key);
print_r($result);
echo '</pre>';

function load_grid_columns ($model_name, $session_key) {
    $queryUrl = 'http://estate.sitebill.ru/apps/api/rest.php';
    $params = array(
        'action' => 'model',
        'do' => 'load_grid_columns',
        'session_key' => $session_key,
        'model_name' => $model_name,
    );
    $result = executeHTTPRequest($queryUrl, $params);
    return $result;
}

function native_insert ( $model_name, $ql_items, $session_key ) {
    $queryUrl = 'http://estate.sitebill.ru/apps/api/rest.php';
    $params = array(
        'action' => 'model',
        'do' => 'native_insert',
        'session_key' => $session_key,
        'model_name' => $model_name,
        'ql_items' => $ql_items,
    );
    $result = executeHTTPRequest($queryUrl, $params);
    return $result;
}

function get_auth ( $login, $password ) {
    $queryUrl = 'http://estate.sitebill.ru/apps/api/rest.php';
    $params = array(
        'action' => 'oauth',
        'do' => 'login',
        'login' => $login,
        'password' => $password,
    );
    $result = executeHTTPRequest($queryUrl, $params);
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
