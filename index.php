<?php
include_once("functions.php");

session_start();

if (!isset($_SESSION["user"])) {
    $_SESSION["user"] = [
        "name" => "",
        "phone" => "",
        "email" => "",
        "message" => ""
    ];

    $arUserData = [
        "name" => "",
        "phone" => "",
        "email" => "",
        "message" => ""
    ];
} else {
    $arUserData = $_SESSION["user"];
}

$arValidationErrors = [];

$feedback = "";

if (isset($_POST["send"])) {
    $arUserData = postDataHandler();

    if ($arUserData !== $_SESSION["user"]) {
        $arValidationErrors = handleValidation($arUserData);

        if (!$arValidationErrors) {
            $_SESSION["user"] = $arUserData;
            $arValidationErrors = [];
        }
    } else {
        $arValidationErrors = [];
    }
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="./style.css" />
    <title>Форма</title>
</head>

<body class="content">
    <form class="content__form" method="post">
        <h1 class="content__header">Форма</h1>

        <input class="content__input" type="text" name="name" value="<?= $arUserData["name"] ?>" placeholder="Александр" required />
        <?php
        $arNameValidationResult = handleError("name", $arValidationErrors);
        echo "<p class='content__feedback $arNameValidationResult[1]'>$arNameValidationResult[0]</p>";
        ?>

        <input class="content__input" type="tel" name="phone" value="<?= $arUserData["phone"] ?>" placeholder="+79275643843" required />
        <?php
        $arPhoneValidationResult = handleError("phone", $arValidationErrors);
        echo "<p class='content__feedback $arPhoneValidationResult[1]'>$arPhoneValidationResult[0]</p>";
        ?>

        <input class="content__input" type="email" name="email" value="<?= $arUserData["email"] ?>" placeholder="alex85@mail.ru" required />
        <?php
        $arEmailValidationResult = handleError("email", $arValidationErrors);
        echo "<p class='content__feedback $arEmailValidationResult[1]'>$arEmailValidationResult[0]</p>";
        ?>

        <textarea class="content__textarea" name="message" placeholder="Текст сообщения" required><?= $arUserData["message"] ?></textarea>
        <?php
        $arMessageValidationResult = handleError("message", $arValidationErrors);
        echo "<p class='content__feedback $arMessageValidationResult[1]'>$arMessageValidationResult[0]</p>";
        ?>

        <button class="content__button" type="submit" name="send">Отправить</button>
    </form>
</body>

</html>