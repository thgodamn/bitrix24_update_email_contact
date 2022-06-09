<?php
$isSeedBitrixContacts       = ! empty($_GET['seedBitrixContacts']) && $_GET['seedBitrixContacts'] === 'yes';
$isSeedDbWithBitrixContacts = ! empty($_GET['seedDbWithBitrixContacts']) && $_GET['seedDbWithBitrixContacts'] === 'yes';
require_once 'vendor/autoload.php';
require_once 'Inc/Classes/Bitrix.php';
$bitrix = new sd\Bitrix();
if ($isSeedBitrixContacts) {
    require_once 'Inc/Generators/Email.php';
    require_once 'Inc/Generators/BitrixUser.php';

    $generatorEmail  = new sd\Generators\Email();
    $generatedEmails = $generatorEmail->get(30000);

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
