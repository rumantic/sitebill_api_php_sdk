<?php
include_once 'sitebill_sdk.php';
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

echo 'Добавляем запись<br>';
$result = $sitebill_sdk->native_insert('data', $ql_items);
$new_record_id = $result['data']['new_record_id'];
echo 'ID новой записи: '.$new_record_id.'<br>';
print_r($result);

echo 'Загружаем запись<br>';
$result = $sitebill_sdk->load_data('data', 'id', $new_record_id);
print_r($result);

echo 'Удаляем запись<br>';
$result = $sitebill_sdk->delete('data', 'id', $new_record_id);
print_r($result);

echo 'Получаем список колонок<br>';
$result = $sitebill_sdk->load_grid_columns('data');
print_r($result);
echo '</pre>';


