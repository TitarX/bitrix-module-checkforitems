<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin.php"); ?>

<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

@set_time_limit(360);

global $APPLICATION;
$APPLICATION->SetTitle(Loc::getMessage("PAGE_TITLE"));

$iBlock_Type = 'catalog';
$code_Pattern = "/^[-_0-9a-z]+$/";

?>

<div>
    <h3>Разрешённые символы в символьных кодах:</h3>
    <ol>
        <li>
            Латинские буквы в нижнем регистре
        </li>
        <li>
            Цифры
        </li>
        <li>
            Знаки "-" и "_"
        </li>
    </ol>
</div>

<h3 style="margin-top: 30px;">Ошибки в символьных кодах элементов информационных блоков каталога:</h3>

<?

$arOrder = array();
$arFilter = array(
    'IBLOCK_TYPE' => $iBlock_Type,
    'ACTIVE' => 'Y'
);
$arGroupBy = false;
$arNavStartParams = false;
$arSelect = array(
    'ID',
    'IBLOCK_ID',
    'CODE'
);

$iBlocks_Elements_Code = array();
$iBlocks_Elements_Iblock = array();
$element_Result = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelect);
while ($element_Object = $element_Result->GetNextElement()) {
    $arFields = $element_Object->GetFields();

    if (preg_match($code_Pattern, $arFields['CODE']) !== 1) {
        $iBlocks_Elements_Code[$arFields['ID']] = $arFields['CODE'];
        $iBlocks_Elements_Iblock[$arFields['ID']] = $arFields['IBLOCK_ID'];
    }
}

$iBlocks_Elements_Repeat_Count = 0;

if (!empty($iBlocks_Elements_Code)) {
    foreach ($iBlocks_Elements_Code as $iBlocks_Elements_Code_Id => $iBlocks_Elements_Code_Code) {
        ?>

        <div style="margin-bottom: 10px;">
            <div>
                <div>
                    Найдены ошибки в символьном коде: <strong><?= $iBlocks_Elements_Code_Code ?></strong>
                </div>
                <div>
                    <a href="iblock_element_edit.php?IBLOCK_ID=<?= $iBlocks_Elements_Iblock[$iBlocks_Elements_Code_Id] ?>&type=<?= $iBlock_Type ?>&ID=<?= $iBlocks_Elements_Code_Id ?>&lang=<?= LANGUAGE_ID ?>"
                       target="_blank">
                        Редактировать элемента с данным символьным кодом
                    </a>
                </div>
            </div>
        </div>

        <?
    }

    $iBlocks_Elements_Repeat_Count++;
}

?>

<?
if ($iBlocks_Elements_Repeat_Count === 0): ?>
    <div style="margin-bottom: 10px;">
        Ошибки в символный кодах не найдены.
    </div>
<?
endif; ?>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>
