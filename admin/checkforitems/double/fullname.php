<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin.php"); ?>

<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

@set_time_limit(360);

global $APPLICATION;
$APPLICATION->SetTitle(Loc::getMessage("PAGE_TITLE"));

$iBlock_Type = 'catalog';

?>

<h3 style="margin-top: 30px;">Дубликаты полных названий элементов в информационных блоках каталога:</h3>

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
    'CODE',
    'NAME'
);

$iBlocks_Elements_Code = array();
$iBlocks_Elements_Iblock = array();
$iBlocks_Elements_Name = array();
$iBlocks_Elements_Property_Full_Name = array();
$element_Result = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelect);
while ($element_Object = $element_Result->GetNextElement()) {
    $arFields = $element_Object->GetFields();

    $iBlocks_Elements_Code[$arFields['ID']] = $arFields['CODE'];
    $iBlocks_Elements_Iblock[$arFields['ID']] = $arFields['IBLOCK_ID'];
    $iBlocks_Elements_Name[$arFields['ID']] = $arFields['NAME'];

    $element_Order = array();
    $element_Filter = array(
        'CODE' => 'FULL_NAME'
    );
    $element_Property_Result = CIBlockElement::GetProperty($arFields['IBLOCK_ID'], $arFields['ID'], $element_Order, $element_Filter);
    if ($element_Property_Array = $element_Property_Result->GetNext()) {
        $iBlocks_Elements_Property_Full_Name[$arFields['ID']] = $element_Property_Array['VALUE'];
    }
}

$iBlocks_Elements_Repeat_Count = 0;

if (!empty($iBlocks_Elements_Property_Full_Name)) {
    $iBlocks_Elements_Repeat = array_count_values($iBlocks_Elements_Property_Full_Name);
    foreach ($iBlocks_Elements_Repeat as $iBlocks_Elements_Value => $iBlocks_Elements_Count) {
        if ($iBlocks_Elements_Count > 1) {
            $iBlocks_Elements_Repeat_Ids = array_keys($iBlocks_Elements_Property_Full_Name, $iBlocks_Elements_Value);
            ?>

            <div style="margin-bottom: 10px;">
                <div>
                    <div>
                        Найдены дубликаты полных названий: <strong><?= $iBlocks_Elements_Value ?></strong>
                    </div>
                    <div>
                        Количество элементов с данным полным названием: <strong><?= $iBlocks_Elements_Count ?></strong>
                    </div>
                    <div>
                        Ссылки на редактирование элементов с данным полным названием:<br/>
                        <?
                        $iBlocks_Elements_Repeat_Ids_Count = count($iBlocks_Elements_Repeat_Ids);
                        $iteration_Count = 0;
                        ?>
                        <?
                        foreach ($iBlocks_Elements_Repeat_Ids as $iBlocks_Elements_Repeat_Id): ?>
                            <?
                            $iteration_Count++; ?>
                            <a href="iblock_element_edit.php?IBLOCK_ID=<?= $iBlocks_Elements_Iblock[$iBlocks_Elements_Repeat_Id] ?>&type=<?= $iBlock_Type ?>&ID=<?= $iBlocks_Elements_Repeat_Id ?>&lang=<?= LANGUAGE_ID ?>"
                               target="_blank"><?= $iBlocks_Elements_Value ?></a>
                            <?
                            if ($iteration_Count < $iBlocks_Elements_Repeat_Ids_Count): ?>
                                <br/>
                            <?
                            endif; ?>
                        <?
                        endforeach; ?>
                    </div>
                </div>
            </div>

            <?

            $iBlocks_Elements_Repeat_Count++;
        }
    }
}

?>

<?
if ($iBlocks_Elements_Repeat_Count === 0): ?>
    <div style="margin-bottom: 10px;">
        Дубликаты не найдены.
    </div>
<?
endif; ?>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>
