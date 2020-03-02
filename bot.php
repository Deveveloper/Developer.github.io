<?php 

if (!isset($_REQUEST)) { 
return; 
} 

//Строка для подтверждения адреса сервера из настроек Callback API 
$confirmation_token = '0dfef22f'; 

//Ключ доступа сообщества 
$token = '8b6cf63c2d8626105996f8874e040f852c325ccd0b2081adc541a8166163635f0d082213ff296c6394ee1'; 

//Получаем и декодируем уведомление 
$data = json_decode(file_get_contents('php://input')); 

//Проверяем, что находится в поле "type" 
switch ($data->type) { 
//Если это уведомление для подтверждения адреса... 
case 'confirmation': 
//...отправляем строку для подтверждения 
echo $confirmation_token; 
break; 

//Если это уведомление о новом сообщении... 
case 'message_new': 
//...получаем id его автора 
$user_id = $data->object->message->from_id; 
//затем с помощью users.get получаем данные об авторе 
$user_info = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$user_id}&access_token={$token}&v=5.103")); 

//и извлекаем из ответа его имя 
$user_name = $user_info->response[0]->first_name; 

//С помощью messages.send отправляем ответное сообщение 
$request_params = array( 
'message' => "Hello, {$user_name}!", 
'peer_id' => $user_id, 
'access_token' => $token, 
'v' => '5.103', 
'random_id' => '0' 
); 

$get_params = http_build_query($request_params); 

file_get_contents('https://api.vk.com/method/messages.send?'. $get_params); 

//Возвращаем "ok" серверу Callback API 

echo('ok'); 

break; 

} 
?> 