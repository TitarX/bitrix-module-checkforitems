<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin.php"); ?>

<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

@set_time_limit(360);

global $APPLICATION;
$APPLICATION->SetTitle(Loc::getMessage("PAGE_TITLE"));

$iBlock_Type = 'catalog';

?>

<h3 style="margin-top: 30px;">Дубликаты символьных кодов элементов в информационных блоках каталога:</h3>

<?

$arOrder = Array();
$arFilter = Array(
	'IBLOCK_TYPE' => $iBlock_Type,
	'ACTIVE' => 'Y'
);
$arGroupBy = FALSE;
$arNavStartParams = FALSE;
$arSelect = Array(
	'ID',
	'IBLOCK_ID',
	'CODE',
	'DETAIL_PICTURE'
);

$iBlocks_Elements_Code = array();
$iBlocks_Elements_Iblock = array();
$element_Result = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelect);
while($element_Object = $element_Result->GetNextElement()) {
	$arFields = $element_Object->GetFields();
	
	$iBlocks_Elements_Code[$arFields['ID']] = $arFields['CODE'];
	$iBlocks_Elements_Iblock[$arFields['ID']] = $arFields['IBLOCK_ID'];
	
	if(!empty($arFields['DETAIL_PICTURE'])) {
		$field_Content = CFile::GetPath($arFields['DETAIL_PICTURE']);
		$image_Path = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $field_Content;
		print $image_Path;
		print '<br />';
		print pathinfo($image_Path, PATHINFO_BASENAME);
		print '<br />';
		$image_Size = getimagesize($image_Path);
		if($image_Size === FALSE) {
			print 'False';
		}
		else {
			print '<pre>';
			print_r($image_Size);
			print '</pre>';
		}
		
		$file_Path = $_SERVER['DOCUMENT_ROOT'] . $field_Content;
		$file_Size = filesize($file_Path);
		print '<pre>';
		print $file_Size . ' B';
		print '</pre>';
		print '<pre>';
		print $file_Size / 1024 . ' Kb';
		print '</pre>';
		print '<pre>';
		print convertToReadableSize($file_Size);
		print '</pre>';
		
		print '<br />';
		print '-----------------------';
		print '<br />';
		print '<pre>';
		print_r($_SERVER);
		print '</pre>';
		break;
	}
}

$iBlocks_Elements_Repeat_Count = 0;

if(!empty($iBlocks_Elements_Code)) {
	$iBlocks_Elements_Repeat = array_count_values($iBlocks_Elements_Code);
	foreach($iBlocks_Elements_Repeat as $iBlocks_Elements_Value => $iBlocks_Elements_Count) {
		if($iBlocks_Elements_Count > 1) {
			$iBlocks_Elements_Repeat_Ids = array_keys($iBlocks_Elements_Code, $iBlocks_Elements_Value);
			?>
			
			<div style="margin-bottom: 10px;">
				<div>
					<div>
						Найдены дубликаты символьного кода: <strong><?= $iBlocks_Elements_Value ?></strong>
					</div>
					<div>
						Количество элементов с данным символьным кодом: <strong><?= $iBlocks_Elements_Count ?></strong>
					</div>
					<div>
						Ссылки на редактирование элементов с данным символьным кодом:<br/>
						<?
						$iBlocks_Elements_Repeat_Ids_Count = count($iBlocks_Elements_Repeat_Ids);
						$iteration_Count = 0;
						?>
						<? foreach($iBlocks_Elements_Repeat_Ids as $iBlocks_Elements_Repeat_Id): ?>
							<? $iteration_Count++; ?>
							<a href="iblock_element_edit.php?IBLOCK_ID=<?= $iBlocks_Elements_Iblock[$iBlocks_Elements_Repeat_Id] ?>&type=<?= $iBlock_Type ?>&ID=<?= $iBlocks_Elements_Repeat_Id ?>&lang=<?= LANGUAGE_ID ?>" target="_blank"><?= $iBlocks_Elements_Value ?></a>
							<? if($iteration_Count < $iBlocks_Elements_Repeat_Ids_Count): ?>
								<br/>
							<? endif; ?>
						<? endforeach; ?>
					</div>
				</div>
			</div>
			
			<?
			
			$iBlocks_Elements_Repeat_Count++;
		}
	}
}

?>

<? if($iBlocks_Elements_Repeat_Count === 0): ?>
	<div style="margin-bottom: 10px;">
		Дубликаты не найдены.
	</div>
<? endif; ?>

<?

function convertToReadableSize($size) {
	$base = log($size) / log(1024);
	$suffix = array('', ' Kb', ' Mb', ' Gb', ' Tb');
	$f_base = floor($base);
	return round(pow(1024, $base - floor($base)), 1) . $suffix[$f_base];
}

?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>
