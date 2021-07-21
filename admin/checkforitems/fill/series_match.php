<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin.php"); ?>

<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

@set_time_limit(360);

global $APPLICATION;
$APPLICATION->SetTitle(Loc::getMessage("PAGE_TITLE"));

?>

<h3 style="margin-top: 30px;">Элементы каталога, имеющие несоответствие между указанными производителем и серией:</h3>

<?

$arOrder = array();
$arFilter = array(
    'IBLOCK_TYPE' => 'catalog',
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
$manufacturers_Id = array();
$series_Id = array();
$manufacturers_Names_By_Current_Series = array();

// Элементы каталога (Товары)
$catalog_Element_Result = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelect);
while ($catalog_Element_Array = $catalog_Element_Result->GetNext()) {
    $element_Order = array();
    $element_Filter = array(
        'CODE' => 'MANUFACTURER_NEW'
    );
    // Свойство "Производитель" элемента каталога
    $manufacturer_Element_Property_Result = CIBlockElement::GetProperty($catalog_Element_Array['IBLOCK_ID'], $catalog_Element_Array['ID'], $element_Order, $element_Filter);
    if ($manufacturer_Element_Property_Array = $manufacturer_Element_Property_Result->GetNext()) {
        if (!empty($manufacturer_Element_Property_Array['VALUE'])) {
            $element_Order = array();
            $element_Filter = array(
                'CODE' => 'SERIES_NEW'
            );
            // Свойство "Серия" элемента каталога
            $series_Element_Property_Result = CIBlockElement::GetProperty($catalog_Element_Array['IBLOCK_ID'], $catalog_Element_Array['ID'], $element_Order, $element_Filter);
            if ($series_Element_Property_Array = $series_Element_Property_Result->GetNext()) {
                if (!empty($series_Element_Property_Array['VALUE'])) {
                    $elementId = $series_Element_Property_Array['VALUE'];
                    $bElementOnly = true;
                    $arSelect = array(
                        'ID',
                        'NAME'
                    );
                    // Производитель серии
                    $manufacturer_Element_Result = CIBlockElement::GetElementGroups($elementId, $bElementOnly, $arSelect);
                    if ($manufacturer_Element_Fields = $manufacturer_Element_Result->GetNext()) {
                        if ($manufacturer_Element_Property_Array['VALUE'] != $manufacturer_Element_Fields['ID']) {
                            $iBlocks_Elements_Name[$catalog_Element_Array['ID']] = $catalog_Element_Array['NAME'];
                            $iBlocks_Elements_Iblock[$catalog_Element_Array['ID']] = $catalog_Element_Array['IBLOCK_ID'];
                            $manufacturers_Id[$catalog_Element_Array['ID']] = $manufacturer_Element_Property_Array['VALUE'];
                            $series_Id[$catalog_Element_Array['ID']] = $series_Element_Property_Array['VALUE'];
                            $manufacturers_Names_By_Current_Series[$catalog_Element_Array['ID']] = $manufacturer_Element_Fields['NAME'];
                        }
                    }
                }
            }
        }
    }
}

$iBlocks_Elements_Repeat_Count = 0;

if (!empty($iBlocks_Elements_Name)) {
    foreach ($iBlocks_Elements_Name as $iBlocks_Elements_Name_Id => $iBlocks_Elements_Name_Name) {
        $manufacturer_Section_Result = CIBlockSection::GetByID($manufacturers_Id[$iBlocks_Elements_Name_Id]);
        $manufacturer_Section_Name = '';
        if ($manufacturer_Section_Array = $manufacturer_Section_Result->GetNext()) {
            $manufacturer_Section_Name = $manufacturer_Section_Array['NAME'];
        }

        $series_Section_Result = CIBlockElement::GetByID($series_Id[$iBlocks_Elements_Name_Id]);
        $series_Section_Name = '';
        if ($series_Section_Array = $series_Section_Result->GetNext()) {
            $series_Section_Name = $series_Section_Array['NAME'];
        }

        ?>

        <div style="margin-bottom: 10px;">
            <div>
                <div>
                    Серия не соответствует производителю в элементе: <strong><?= $iBlocks_Elements_Name_Name ?></strong>.
                </div>
                <?
                if (!empty($manufacturer_Section_Name) && !empty($series_Section_Name)): ?>
                    <div>
                        Указаны: производитель - <strong><?= $manufacturer_Section_Name ?></strong>, серия - <strong><?= $series_Section_Name ?></strong>.
                    </div>
                <?
                endif; ?>
                <div>
                    Указанная серия относится к производителю <strong><?= $manufacturers_Names_By_Current_Series[$iBlocks_Elements_Name_Id] ?></strong>.
                </div>
                <div>
                    <a href="iblock_element_edit.php?IBLOCK_ID=<?= $iBlocks_Elements_Iblock[$iBlocks_Elements_Name_Id] ?>&type=catalog&ID=<?= $iBlocks_Elements_Name_Id ?>&lang=<?= LANGUAGE_ID ?>"
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
        Все элементы каталога имеют соответствие между указанными производителем и серией.
    </div>
<?
endif; ?>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>
