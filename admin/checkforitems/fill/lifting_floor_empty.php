<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin.php"); ?>

<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

@set_time_limit(360);

global $APPLICATION;
$APPLICATION->SetTitle(Loc::getMessage("PAGE_TITLE"));

$iBlock_Type = 'catalog';
$maximum_Result = 50;

?>

<small>Максимальное количество найденных элементов: <?= $maximum_Result ?>.</small>

<h3 style="margin-top: 20px;">Элементы каталога с незаполненным свойством "Стоимость подъёма за этаж":</h3>

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
    'NAME'
);

$iBlocks_Elements_Name = array();
$iBlocks_Elements_Iblock = array();
$element_Result = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelect);
$result_Counter = 0;
while ($element_Object = $element_Result->GetNextElement()) {
    $arFields = $element_Object->GetFields();

    $element_Order = array();
    $element_Filter = array(
        'CODE' => 'CARGO_LIFTING_FLOOR'
    );
    $element_Property_Result = CIBlockElement::GetProperty($arFields['IBLOCK_ID'], $arFields['ID'], $element_Order, $element_Filter);
    if ($element_Property_Array = $element_Property_Result->GetNext()) {
        $property_Value = trim($element_Property_Array['VALUE']);
        if ($property_Value != '0') {
            if (empty($property_Value)) {
                $iBlocks_Elements_Name[$arFields['ID']] = $arFields['NAME'];
                $iBlocks_Elements_Iblock[$arFields['ID']] = $arFields['IBLOCK_ID'];
                $result_Counter++;
            }
        }
    }

    if ($result_Counter >= $maximum_Result) {
        break;
    }
}

$iBlocks_Elements_Repeat_Count = 0;

if (!empty($iBlocks_Elements_Name)) {
    foreach ($iBlocks_Elements_Name as $iBlocks_Elements_Name_Id => $iBlocks_Elements_Name_Name) {
        ?>

        <div style="margin-bottom: 10px;">
            <div>
                <div>
                    Свойство "Стоимость подъёма за этаж" не заполнено в элементе: <strong><?= $iBlocks_Elements_Name_Name ?></strong>
                </div>
                <div>
                    <a href="iblock_element_edit.php?IBLOCK_ID=<?= $iBlocks_Elements_Iblock[$iBlocks_Elements_Name_Id] ?>&type=<?= $iBlock_Type ?>&ID=<?= $iBlocks_Elements_Name_Id ?>&lang=<?= LANGUAGE_ID ?>"
                       target="_blank">
                        Перейти к редактированию элемента
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
        Все элементы каталога имеют заполненное свойство "Стоимость подъёма за этаж".
    </div>
<?
endif; ?>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>
