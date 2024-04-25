<?php
function postDataHandler(): array
{
    return [
        "id" => $_POST["id"],
        "ip" => $_POST["ip"],
        "name" => $_POST["name"],
        "dateOfBirth" => $_POST["dateOfBirth"],
        "phone" => $_POST["phone"], 
        "email" => $_POST["email"], 
        "message" => $_POST["message"]
    ];
}

function validateIp(string $ip): bool
{
    return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);
}

function validateName(string $name): bool
{
    $arOptions = [
        "options" => [
            "regexp" => '/^([а-яА-ЯёЁ]{3,})$/su'
        ]
    ];

    return filter_var($name, FILTER_VALIDATE_REGEXP, $arOptions);
}

function validateDateOfBirth(string $date): bool
{
    $input = strtotime($date);
    $now = strtotime("now");
    $tenYearsBeforeNow = strtotime("-10 years", $now);
    $ninetyYearsBeforeNow = strtotime("-90 years", $now);

    return ($input <= $tenYearsBeforeNow && $input >= $ninetyYearsBeforeNow);
}

function validateEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone(string $phone): bool
{
    $phoneWithoutWhitespaces = preg_replace('/\s+/', "", $phone);

    $arOptions = [
        "options" => [
            "regexp" => '/^(\+7[0-9]{10})$/s'
        ]
    ];

    return filter_var($phoneWithoutWhitespaces, FILTER_VALIDATE_REGEXP, $arOptions);
}

function validateMessage(string $message): bool
{
    $arOptions = [
        "options" => [
            "regexp" => '/(https|http|www):\/\/{1,}.{1,}/s'
        ]
    ];

    return ($message === filter_var($message, FILTER_SANITIZE_FULL_SPECIAL_CHARS) && !filter_var($message, FILTER_VALIDATE_REGEXP, $arOptions));
}

function sanitizeName(string $name): string
{
    return preg_replace('/[a-z0-9\s]/i', "", $name);
}

function sanitizeEmail(string $email): string
{
    return filter_var($email, FILTER_SANITIZE_EMAIL);
}

function sanitizePhone(string $phone): string
{
    return filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
}

function sanitizeMessage(string $message): string
{
    return filter_var(
        $message,
        FILTER_CALLBACK,
        [
            "options" => function ($value) {
                $value = strip_tags($value);
                return preg_replace('/(http|https|www):\/\/\S+/si', '', $value);
            }
        ]
    );
}

function handleValidation(array $arUserData): array
{
    $arValidationErrors = [];

    if (!validateIp($arUserData["ip"])) {
        $arValidationErrors["ip"] = ["IP-адрес не принадлежит допустимому диапазону: от 0.0.0.0 до 255.255.255.255.", "content__feedback_type_error"];
    } else {
        $arValidationErrors["ip"] = ["Валидация пройдена успешно", "content__feedback_type_success"];
    }

    if (!validateName($arUserData["name"])) {
        $arValidationErrors["name"] = ["Поле должно содержать не менее 3х букв и быть заполнено кириллицей. Данные после очистки: " . sanitizeName($arUserData["name"]), "content__feedback_type_error"];
    } else {
        $arValidationErrors["name"] = ["Валидация пройдена успешно", "content__feedback_type_success"];
    }

    if (!validateDateOfBirth($arUserData["dateOfBirth"])) {
        $arValidationErrors["dateOfBirth"] = ["Дата рождения не может быть более 90 и менее 10 лет назад", "content__feedback_type_error"];
    } else {
        $arValidationErrors["dateOfBirth"] = ["Валидация пройдена успешно", "content__feedback_type_success"];
    }

    if (!validatePhone($arUserData["phone"])) {
        $arValidationErrors["phone"] = ["Поле не соответствует формату номера телефона (+7XXXXXXXXXX). Данные после очистки: " . sanitizePhone($arUserData["phone"]), "content__feedback_type_error"];
    } else {
        $arValidationErrors["phone"] = ["Валидация пройдена успешно", "content__feedback_type_success"];
    }

    if (!validateEmail($arUserData["email"])) {
        $arValidationErrors["email"] = ["Поле не соответствует формату email. Данные после очистки: " . sanitizeEmail($arUserData["email"]), "content__feedback_type_error"];
    } else {
        $arValidationErrors["email"] = ["Валидация пройдена успешно", "content__feedback_type_success"];
    }

    if (!validateMessage($arUserData["message"])) {
        $arValidationErrors["message"] = ["В сообщении должны отсутствовать html теги и ссылки. Данные после очистки: " . sanitizeMessage($arUserData["message"]), "content__feedback_type_error"];
    } else {
        $arValidationErrors["message"] = ["Валидация пройдена успешно", "content__feedback_type_success"];
    }

    return $arValidationErrors;
}

function handleError(string $key, array $arValidationErrors): array
{
    if (array_key_exists($key, $arValidationErrors)) { 
        return $arValidationErrors[$key];
    }
    return ["", ""];
}
