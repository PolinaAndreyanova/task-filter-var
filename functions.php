<?php
function postDataHandler(): array
{
    return [
        "name" => $_POST["name"],
        "phone" => $_POST["phone"], 
        "email" => $_POST["email"], 
        "message" => $_POST["message"]
    ];
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

    return ($message !== filter_var($message, FILTER_SANITIZE_FULL_SPECIAL_CHARS) && filter_var($message, FILTER_VALIDATE_REGEXP, $arOptions));
}

function handleValidation(array $arUserData): array
{
    $arValidationErrors = [];

    if (!validateName($arUserData["name"])) {
        $arValidationErrors["name"] = ["Поле должно содержать не менее 3х букв и быть заполнено кириллицей", "content__feedback_type_error"];
    } else {
        $arValidationErrors["name"] = ["Валидация пройдена успешно", "content__feedback_type_success"];
    }

    if (!validatePhone($arUserData["phone"])) {
        $arValidationErrors["phone"] = ["Поле не соответствует формату номера телефона (+7XXXXXXXXXX)", "content__feedback_type_error"];
    } else {
        $arValidationErrors["phone"] = ["Валидация пройдена успешно", "content__feedback_type_success"];
    }

    if (!validateEmail($arUserData["email"])) {
        $arValidationErrors["email"] = ["Поле не соответствует формату email", "content__feedback_type_error"];
    } else {
        $arValidationErrors["email"] = ["Валидация пройдена успешно", "content__feedback_type_success"];
    }

    if (!validateMessage($arUserData["message"])) {
        $arValidationErrors["message"] = ["В сообщении должны отсутствовать html теги и ссылки", "content__feedback_type_error"];
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
