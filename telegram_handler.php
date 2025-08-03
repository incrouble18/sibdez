<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Обработка preflight запросов
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Настройки Telegram бота
define('BOT_TOKEN', '7620771145:AAFsoWWqyGBYn1FQeHTV2-tVr6PwMY-ypOY');
define('CHAT_ID', '-1002362798683'); // ID канала "Sib Dez Omsk Уведомления"

// Функция для отправки сообщения в Telegram
function sendTelegramMessage($message) {
    $url = 'https://api.telegram.org/bot' . BOT_TOKEN . '/sendMessage';
    
    $data = [
        'chat_id' => CHAT_ID,
        'text' => $message,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    
    return json_decode($result, true);
}

// Функция для форматирования сообщения
function formatMessage($data, $type = 'callback') {
    $emoji = [
        'callback' => '📞',
        'calculator' => '🧮',
        'contact' => '📧'
    ];
    
    $titles = [
        'callback' => 'ЗАЯВКА НА ОБРАТНЫЙ ЗВОНОК',
        'calculator' => 'РАСЧЕТ СТОИМОСТИ',
        'contact' => 'СООБЩЕНИЕ С САЙТА'
    ];
    
    $icon = $emoji[$type] ?? '📋';
    $title = $titles[$type] ?? 'НОВАЯ ЗАЯВКА';
    
    $message = "<b>{$icon} {$title}</b>\n\n";
    
    // Добавляем информацию в зависимости от типа
    if (isset($data['name']) && !empty($data['name'])) {
        $message .= "<b>👤 Имя:</b> " . htmlspecialchars($data['name']) . "\n";
    }
    
    if (isset($data['phone']) && !empty($data['phone'])) {
        $message .= "<b>📱 Телефон:</b> " . htmlspecialchars($data['phone']) . "\n";
    }
    
    if (isset($data['email']) && !empty($data['email'])) {
        $message .= "<b>📧 Email:</b> " . htmlspecialchars($data['email']) . "\n";
    }
    
    if (isset($data['message']) && !empty($data['message'])) {
        $message .= "<b>💬 Сообщение:</b> " . htmlspecialchars($data['message']) . "\n";
    }
    
    // Для калькулятора добавляем расчетные данные
    if ($type === 'calculator') {
        if (isset($data['pest_type']) && !empty($data['pest_type'])) {
            $message .= "<b>🐛 Тип вредителя:</b> " . htmlspecialchars($data['pest_type']) . "\n";
        }
        
        if (isset($data['room_count']) && !empty($data['room_count'])) {
            $message .= "<b>🏠 Количество комнат:</b> " . htmlspecialchars($data['room_count']) . "\n";
        }
        
        if (isset($data['area']) && !empty($data['area'])) {
            $message .= "<b>📐 Площадь:</b> " . htmlspecialchars($data['area']) . " м²\n";
        }
        
        if (isset($data['urgency']) && !empty($data['urgency'])) {
            $message .= "<b>⚡ Срочность:</b> " . htmlspecialchars($data['urgency']) . "\n";
        }
        
        if (isset($data['estimated_cost']) && !empty($data['estimated_cost'])) {
            $message .= "<b>💰 Примерная стоимость:</b> " . htmlspecialchars($data['estimated_cost']) . " ₽\n";
        }
    }
    
    // Добавляем время
    $message .= "\n<b>🕐 Время:</b> " . date('d.m.Y H:i:s') . "\n";
    $message .= "<b>🌐 Сайт:</b> СибДез Омск";
    
    return $message;
}

// Обработка POST запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Получаем данные
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (!$data) {
            throw new Exception('Некорректные данные');
        }
        
        // Определяем тип заявки
        $type = 'callback';
        if (isset($data['type'])) {
            $type = $data['type'];
        } elseif (isset($data['pest_type']) || isset($data['estimated_cost'])) {
            $type = 'calculator';
        } elseif (isset($data['message'])) {
            $type = 'contact';
        }
        
        // Валидация обязательных полей
        if (empty($data['phone']) && empty($data['email'])) {
            throw new Exception('Необходимо указать телефон или email');
        }
        
        // Форматируем и отправляем сообщение
        $message = formatMessage($data, $type);
        $result = sendTelegramMessage($message);
        
        if ($result && isset($result['ok']) && $result['ok']) {
            echo json_encode([
                'success' => true,
                'message' => 'Заявка успешно отправлена! Мы свяжемся с вами в ближайшее время.'
            ]);
        } else {
            throw new Exception('Ошибка отправки в Telegram');
        }
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    // GET запрос - показываем информацию
    echo json_encode([
        'status' => 'active',
        'bot_name' => '@sibdez_bot',
        'channel_id' => CHAT_ID,
        'message' => 'Telegram handler для СибДез Омск работает'
    ]);
}
?>

