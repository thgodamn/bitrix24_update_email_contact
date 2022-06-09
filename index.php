<?php
$isSeedBitrixContacts       = ! empty($_GET['seedBitrixContacts']) && $_GET['seedBitrixContacts'] === 'yes';
$isSeedDbWithBitrixContacts = ! empty($_GET['seedDbWithBitrixContacts']) && $_GET['seedDbWithBitrixContacts'] === 'yes';
$isUpdateContacts = ! empty($_GET['isUpdateContacts']) && $_GET['isUpdateContacts'] === 'yes';
require_once 'vendor/autoload.php';
require_once 'Inc/Classes/Bitrix.php';
$bitrix = new sd\Bitrix();
if ($isSeedBitrixContacts) {
    require_once 'Inc/Generators/Email.php';
    require_once 'Inc/Generators/BitrixUser.php';

    $generatorEmail  = new sd\Generators\Email();
    $generatedEmails = $generatorEmail->get(10);

    $generatorBitrixUser = new sd\Generators\BitrixUser();
    $generatedUsers      = $generatorBitrixUser->get($generatedEmails);

    $bitrix->addContacts($generatedUsers);
    echo '<p>Контакты успешно добавлены</p>';
}
if ($isSeedDbWithBitrixContacts) {
    require_once 'Inc/Classes\DB.php';
    $db    = new sd\DB();
    $users = $bitrix->getUsers();
    $db->storeUsers($users);
    echo '<p>База успешно заполнена</p>';
}
if ($isUpdateContacts) {
    require_once 'Inc/Classes\DB.php';
    $db    = new sd\DB();

    //удалить email'ы с префиксом temp_9999
    $db->clearEmailUsers('/^[_a-z0-9-]+[\.[_a-z0-9-]+]*_temp_[0-9]{4}@[a-z0-9-]+[\.[a-z0-9-]+]*\.[a-z]{2,3}$/');
    $bitrix->updateContacts();
    echo '<p>Контакты обновлены</p>';
}
?>
<div>
	<a href="/?seedBitrixContacts=yes">
		Заполнить базу Bitrix
	</a>
</div>
<div>
	<a href="/?seedDbWithBitrixContacts=yes">
		Заполнить локальную базу контактами из Bitrix
	</a>
</div>
<div>
    <a href="/?isUpdateContacts=yes">
        Заполнить базу Bitrix обновлеными контактами
    </a>
</div>
