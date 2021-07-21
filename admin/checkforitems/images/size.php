<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin.php"); ?>

<?
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
?>

<? if(CModule::IncludeModule('iblock')): ?>

	<?
	
	global $APPLICATION;
	$APPLICATION->SetTitle(Loc::getMessage("PAGE_TITLE"));
	
	include_once __DIR__ . '/../../../classes/CheckResultHelper.php';
	include_once __DIR__ . '/../../../classes/MiscHelper.php';
	
	@set_time_limit(360);
	
	$check_Name = 'images_size';
	$part_Elements_Limit = 300;
	$max_Result_Count = 50;
	
	?>

	<style type="text/css">
		#new-check-button:active {
			height: 29px !important;
		}
		
		.check-input-control-wrapper {
			border: 0px;
			padding-left: 0px;
		}
		
		.check-input-control-wrapper .check-input-control {
			width: 230px;
		}
		
		.check-input-control-wrapper .check-input-control[type="number"] {
			padding: 5px;
			border-radius: 4px;
			border-width: 1px;
		}
		
		.check-info {
			color: orangered;
			font-weight: bold;
		}
		
		.check-info > p {
			margin: 0px;
		}
		
		.check-info ul {
			margin: 0px !important;
		}
		
		.check-info ul > li {
			font-weight: normal;
		}
		
		.check-info .check-info-value {
			font-weight: bold;
		}
		
		.error-value {
			color: red;
		}
	</style>
	
	<div style="margin-bottom: 20px;">
		<small><?= Loc::getMessage('PAGE_DESCRIPTION') ?></small>
		<br />
		<small><?= Loc::getMessage('MAX_RESULT_SIZE') ?> <?= $max_Result_Count ?>.</small>
	</div>

	<div class="check-info">
		<p>
			Все поля обязательны для заполнения.
		</p>
		<p>
			Укажите целочисленные значения.
		</p>
		<p>
			Максимальное значение должно быть больше минимального.
		</p>
	</div>
	
	<fieldset class="check-input-control-wrapper">
		<input class="check-input-control" type="number" min="0" max="9999" id="min-width" name="min-width" value="" placeholder="Минимальная ширина (Пиксели)" />
		<span class="check-input-control-separator">X</span>
		<input class="check-input-control" type="number" min="0" max="9999" id="min-height" name="min-height" value="" placeholder="Минимальная высота (Пиксели)" />
	</fieldset>
	<fieldset class="check-input-control-wrapper">
		<input class="check-input-control" type="number" min="0" max="9999" id="max-width" name="max-width" value="" placeholder="Максимальная ширина (Пиксели)" />
		<span class="check-input-control-separator">X</span>
		<input class="check-input-control" type="number" min="0" max="9999" id="max-height" name="max-height" value="" placeholder="Максимальная высота (Пиксели)" />
	</fieldset>
	<fieldset class="check-input-control-wrapper">
		<input class="check-input-control" type="number" min="0" max="999999" id="max-size" name="max-size" value="" placeholder="Максимальный вес (Килобайты)" />
	</fieldset>
	<input type="hidden" id="part" name="part" value="1" />
	<input type="hidden" id="checked-elements-count" name="checked-elements-count" value="0" />
	<input type="hidden" id="total-elements-invalid-size-count" name="total-elements-invalid-size-count" value="0" />
	<button id="new-check-button" name="new-check-button" class="adm-btn adm-btn-save" style="" onclick="checkElementsImages(true); return false;">
		<?= Loc::getMessage('START_CHECK_BUTTON_TEXT') ?>
	</button>

	<div id="result-info"></div>
	
	<div id="result-data">
		<?
		
		if(isset($_GET['mode']) && $_GET['mode'] == 'check') {
			$APPLICATION->RestartBuffer();
			
			$error_Messages_Html = '';
			$part = $_GET['part'];
			$checked_Elements_Count = $_GET['checked_elements_count'];
			$total_Elements_Invalid_Size_Count = $_GET['total_elements_invalid_size_count'];
			
			$min_Width = $_GET['min_width'];
			$min_Height = $_GET['min_height'];
			$max_Width = $_GET['max_width'];
			$max_Height = $_GET['max_height'];
			$max_Size = $_GET['max_size'];
			
			if($part == '1') {
				$delete_Result = CheckResultHelper::Delete('checkforitems_images_format', 'CheckforitemsImagesSize');
				if(is_array($delete_Result)) {
					foreach($delete_Result as $error_Message) {
						$admin_Message_Error = new CAdminMessage(array('MESSAGE' => 'Ошибка удаления', 'DETAILS' => $error_Message, 'TYPE' => 'ERROR', 'HTML' => TRUE));
						$error_Messages_Html .= $admin_Message_Error->Show();
					}
				}
			}
			
			$arOrder = array();
			$arFilter = array(
				'IBLOCK_TYPE' => array('catalog', 'manufacturers'),
				'ACTIVE' => 'Y'
			);
			$arGroupBy = FALSE;
			$arNavStartParams = array(
				'nPageSize' => $part_Elements_Limit,
				'iNumPage' => $part,
				'checkOutOfRange' => TRUE
			);
			$arSelect = array(
				'ID',
				'IBLOCK_ID',
				'IBLOCK_TYPE_ID',
				'DETAIL_PICTURE',
				'DETAIL_TEXT',
				'DETAIL_TEXT_TYPE',
				'NAME'
			);
			
			$elements_Count = 0;
			$elements_Invalid_Size_Count = 0;
			$catalog_Element_Result = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelect);
			
			while(($catalog_Element_Array = $catalog_Element_Result->GetNext()) && ($total_Elements_Invalid_Size_Count + $elements_Invalid_Size_Count < $max_Result_Count)) {
				$is_Element_Invalid_Size = FALSE;
				
				$check_Elements_Result = array();
				$check_Elements_Result['ID'] = '';
				$check_Elements_Result['CODE'] = '';
				$check_Elements_Result['NAME'] = '';
				$check_Elements_Result['IBLOCK_ID'] = '';
				$check_Elements_Result['IBLOCK_TYPE_ID'] = '';
				
				$check_Elements_Result['DETAIL_PICTURE_DETECTED'] = FALSE;
				$check_Elements_Result['DETAIL_PICTURE_DATA'] = array();
				
				$check_Elements_Result['DETAIL_TEXT_PICTURE_DETECTED'] = FALSE;
				$check_Elements_Result['DETAIL_TEXT_PICTURE_DATA'] = array();
				
				$check_Elements_Result['MORE_PICTURE_DETECTED'] = FALSE;
				$check_Elements_Result['MORE_PICTURE_DATA'] = array();
				
				// Детальная картинка >>>
				$field_Content = CFile::GetPath($catalog_Element_Array['DETAIL_PICTURE']);
				$field_Content = trim($field_Content);
				if(!empty($field_Content)) {
					$check_Element_Result = array();
					
					$image_Path = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $field_Content;
					$image_Size = getimagesize($image_Path);
					if(isset($image_Size)) {
						$image_Width = $image_Size[0];
						$image_Height = $image_Size[1];
						
						if($image_Width < $min_Width || $image_Height < $min_Height || $image_Width > $max_Width || $image_Height > $max_Height) {
							$is_Element_Invalid_Size = TRUE;
							$check_Elements_Result['DETAIL_PICTURE_DETECTED'] = TRUE;
							$check_Element_Result['DETAIL_PICTURE_SIZE'] = $image_Width . 'X' . $image_Height;
						}
					}
					
					$file_Path = $_SERVER['DOCUMENT_ROOT'] . $field_Content;
					$file_Size = filesize($file_Path);
					if(isset($file_Size)) {
						$max_Size_Bytes = MiscHelper::convertKbToBytes($max_Size);
						if($file_Size > $max_Size_Bytes) {
							$is_Element_Invalid_Size = TRUE;
							$check_Elements_Result['DETAIL_PICTURE_DETECTED'] = TRUE;
							$check_Element_Result['DETAIL_PICTURE_WEIGHT'] = MiscHelper::convertToReadableSize($file_Size);
						}
					}
					
					if(isset($check_Element_Result['DETAIL_PICTURE_SIZE']) || isset($check_Element_Result['DETAIL_PICTURE_WEIGHT'])) {
//						$check_Element_Result['DETAIL_PICTURE_NAME'] = pathinfo($field_Content, PATHINFO_BASENAME);
						$check_Element_Result['DETAIL_PICTURE_NAME'] = $field_Content;
						array_push($check_Elements_Result['DETAIL_PICTURE_DATA'], $check_Element_Result);
					}
				}
				// <<< Детальная картинка
				
				// Детальное описание >>>
//				if($catalog_Element_Array['DETAIL_TEXT_TYPE'] == 'html') {
//					$preg_Img_Pattern = "/<img[^>]*src=[\"']([^\"']+)[\"'][^>]*>/i";
//					$preg_Img_Results = array();
//					if(preg_match_all($preg_Img_Pattern, $catalog_Element_Array['DETAIL_TEXT'], $preg_Img_Results) && isset($preg_Img_Results[1])) {
//						foreach($preg_Img_Results[1] as $image_Path_Content) {
//							$check_Element_Result = array();
//
//							$image_Path = $image_Path_Content;
//							if(stristr($image_Path, 'http') === FALSE) {
//								$image_Path = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $image_Path;
//
//								$file_Path = $_SERVER['DOCUMENT_ROOT'] . $image_Path_Content;
//								$file_Size = filesize($file_Path);
//								if(isset($file_Size)) {
//									$max_Size_Bytes = MiscHelper::convertKbToBytes($max_Size);
//									if($file_Size > $max_Size_Bytes) {
//										$is_Element_Invalid_Size = TRUE;
//										$check_Elements_Result['DETAIL_TEXT_PICTURE_DETECTED'] = TRUE;
//										$check_Element_Result['DETAIL_TEXT_PICTURE_WEIGHT'] = MiscHelper::convertToReadableSize($file_Size);
//									}
//								}
//							}
//
//							$image_Size = getimagesize($image_Path);
//							if(isset($image_Size)) {
//								$image_Width = $image_Size[0];
//								$image_Height = $image_Size[1];
//
//								if($image_Width < $min_Width || $image_Height < $min_Height || $image_Width > $max_Width || $image_Height > $max_Height) {
//									$is_Element_Invalid_Size = TRUE;
//									$check_Elements_Result['DETAIL_TEXT_PICTURE_DETECTED'] = TRUE;
//									$check_Element_Result['DETAIL_TEXT_PICTURE_SIZE'] = $image_Width . 'X' . $image_Height;
//								}
//							}
//
//							if(isset($check_Element_Result['DETAIL_TEXT_PICTURE_WEIGHT']) || isset($check_Element_Result['DETAIL_TEXT_PICTURE_SIZE'])) {
////								$check_Element_Result['DETAIL_TEXT_PICTURE_NAME'] = pathinfo($image_Path_Content, PATHINFO_BASENAME);
//								$check_Element_Result['DETAIL_TEXT_PICTURE_NAME'] = $image_Path_Content;
//								array_push($check_Elements_Result['DETAIL_TEXT_PICTURE_DATA'], $check_Element_Result);
//							}
//						}
//					}
//				}
				// <<< Детальное описание
				
				// Дополнительные изображения >>>
				$arPropertyOrder = array();
				$arPropertyFilter = array(
					'CODE' => 'MORE_PHOTO'
				);
				$element_Property_Result = CIBlockElement::GetProperty($catalog_Element_Array['IBLOCK_ID'], $catalog_Element_Array['ID'], $arPropertyOrder, $arPropertyFilter);
				while($element_Property_Array = $element_Property_Result->GetNext()) {
					$field_Content = CFile::GetPath($element_Property_Array['VALUE']);
					$field_Content = trim($field_Content);
					if(!empty($field_Content)) {
						$check_Element_Result = array();
						
						$image_Path = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $field_Content;
						$image_Size = getimagesize($image_Path);
						if(isset($image_Size)) {
							$image_Width = $image_Size[0];
							$image_Height = $image_Size[1];
							
							if($image_Width < $min_Width || $image_Height < $min_Height || $image_Width > $max_Width || $image_Height > $max_Height) {
								$is_Element_Invalid_Size = TRUE;
								$check_Elements_Result['MORE_PICTURE_DETECTED'] = TRUE;
								$check_Element_Result['MORE_PICTURE_SIZE'] = $image_Width . 'X' . $image_Height;
							}
						}
						
						$file_Path = $_SERVER['DOCUMENT_ROOT'] . $field_Content;
						$file_Size = filesize($file_Path);
						if(isset($file_Size)) {
							$max_Size_Bytes = MiscHelper::convertKbToBytes($max_Size);
							if($file_Size > $max_Size_Bytes) {
								$is_Element_Invalid_Size = TRUE;
								$check_Elements_Result['MORE_PICTURE_DETECTED'] = TRUE;
								$check_Element_Result['MORE_PICTURE_WEIGHT'] = MiscHelper::convertToReadableSize($file_Size);
							}
						}
						
						if(isset($check_Element_Result['MORE_PICTURE_SIZE']) || isset($check_Element_Result['MORE_PICTURE_WEIGHT'])) {
//							$check_Element_Result['MORE_PICTURE_NAME'] = pathinfo($field_Content, PATHINFO_BASENAME);
							$check_Element_Result['MORE_PICTURE_NAME'] = $field_Content;
							array_push($check_Elements_Result['MORE_PICTURE_DATA'], $check_Element_Result);
						}
					}
				}
				// <<< Дополнительные изображения
				
				if($is_Element_Invalid_Size) {
					if(!CheckResultHelper::IsExist('checkforitems_images_size', 'CheckforitemsImagesSize', 'UF_CHECK_IMAGE_SIZE', $catalog_Element_Array['ID'])) {
						$check_Elements_Result['ID'] = $catalog_Element_Array['ID'];
						$check_Elements_Result['CODE'] = $catalog_Element_Array['CODE'];
						$check_Elements_Result['NAME'] = $catalog_Element_Array['NAME'];
						$check_Elements_Result['IBLOCK_ID'] = $catalog_Element_Array['IBLOCK_ID'];
						$check_Elements_Result['IBLOCK_TYPE_ID'] = $catalog_Element_Array['IBLOCK_TYPE_ID'];
						
						$check_Elements_Result = serialize($check_Elements_Result);
						$append_Result = CheckResultHelper::Append('checkforitems_images_size', 'CheckforitemsImagesSize', array('UF_CHECK_IMAGE_SIZE' => $check_Elements_Result));
						if(is_array($append_Result)) {
							foreach($append_Result as $error_Message) {
								$admin_Message_Error = new CAdminMessage(array('MESSAGE' => 'Ошибка записи', 'DETAILS' => $error_Message, 'TYPE' => 'ERROR', 'HTML' => TRUE));
								$error_Messages_Html .= $admin_Message_Error->Show();
							}
						}
					}
					
					$elements_Invalid_Size_Count++;
				}
				
				$elements_Count++;
			}
			
			$total_Elements_Invalid_Size_Count += $elements_Invalid_Size_Count;
			$checked_Elements_Count += $elements_Count;
			if($total_Elements_Invalid_Size_Count >= $max_Result_Count || $elements_Count == 0) {
				
				$delete_Result = CheckResultHelper::Delete('checkforitems_common', 'CheckforitemsCommon', 'UF_CHECK_COM_TYPE', $check_Name);
				if(is_array($delete_Result)) {
					foreach($delete_Result as $error_Message) {
						$admin_Message_Error = new CAdminMessage(array('MESSAGE' => 'Ошибка удаления', 'DETAILS' => $error_Message, 'TYPE' => 'ERROR', 'HTML' => TRUE));
						$error_Messages_Html .= $admin_Message_Error->Show();
					}
				}
				
				$check_Data = array(
					'time' => time(),
					'min_width' => $min_Width,
					'min_height' => $min_Height,
					'max_width' => $max_Width,
					'max_height' => $max_Height,
					'max_size' => $max_Size
				);
				$check_Data = serialize($check_Data);
				$append_Data = array('UF_CHECK_COM_TYPE' => $check_Name, 'UF_CHECK_COM_DATA' => $check_Data);
				$append_Result = CheckResultHelper::Append('checkforitems_common', 'CheckforitemsCommon', $append_Data);
				if(is_array($append_Result)) {
					foreach($append_Result as $error_Message) {
						$admin_Message_Error = new CAdminMessage(array('MESSAGE' => 'Ошибка записи', 'DETAILS' => $error_Message, 'TYPE' => 'ERROR', 'HTML' => TRUE));
						$error_Messages_Html .= $admin_Message_Error->Show();
					}
				}
				
				$note_Messages_Html = new CAdminMessage(array('MESSAGE' => 'Проверка завершена', 'DETAILS' => "Проверено элементов: <strong>$checked_Elements_Count</strong>", 'TYPE' => 'OK', 'HTML' => TRUE));
				echo CUtil::PhpToJSObject(array('part' => 'done', 'html' => $error_Messages_Html . $note_Messages_Html->Show()));
				exit();
			}
			else {
				$note_Messages_Html = new CAdminMessage(array('MESSAGE' => 'Шаг: ' . $part . '. Проверено элементов: ' . $checked_Elements_Count . '. Найдено: ' . $total_Elements_Invalid_Size_Count . '.', 'DETAILS' => 'Дождитесь окончания проверки элементов', 'TYPE' => 'OK', 'HTML' => TRUE));
				echo CUtil::PhpToJSObject(array('part' => $part + 1, 'checked_Elements_Count' => $checked_Elements_Count, 'total_Elements_Invalid_Size_Count' => $total_Elements_Invalid_Size_Count, 'html' => $error_Messages_Html . $note_Messages_Html->Show()));
				exit();
			}
		}
		else {
			if(isset($_GET['mode']) && $_GET['mode'] == 'result') {
				$APPLICATION->RestartBuffer();
				ob_start();
			}
			
			$last_Check_Date = '';
			$last_Check_Min_Width = 0;
			$last_Check_Min_Height = 0;
			$last_Check_Max_Width = 0;
			$last_Check_Max_Height = 0;
			$last_Check_Max_Size = 0;
			$last_Check_Data_Result = CheckResultHelper::Get('checkforitems_common', 'CheckforitemsCommon', 'UF_CHECK_COM_DATA', array('UF_CHECK_COM_TYPE' => $check_Name));
			if(!empty($last_Check_Data_Result) || is_array($last_Check_Data_Result)) {
				$last_Check_Data = $last_Check_Data_Result[0];
				$last_Check_Data = unserialize($last_Check_Data);
				
				$last_Check_Date = (empty($last_Check_Data['time']) ? '' : $last_Check_Data['time']);
				$last_Check_Min_Width = (empty($last_Check_Data['min_width']) ? 0 : $last_Check_Data['min_width']);
				$last_Check_Min_Height = (empty($last_Check_Data['min_height']) ? 0 : $last_Check_Data['min_height']);
				$last_Check_Max_Width = (empty($last_Check_Data['max_width']) ? 0 : $last_Check_Data['max_width']);
				$last_Check_Max_Height = (empty($last_Check_Data['max_height']) ? 0 : $last_Check_Data['max_height']);
				$last_Check_Max_Size = (empty($last_Check_Data['max_size']) ? 0 : $last_Check_Data['max_size']);
			}
			
			if(empty($last_Check_Date)) {
				$last_Check_Date = 'не определены';
			}
			else {
				$last_Check_Date = date('d.m.Y - H:i:s (e P)', $last_Check_Date);
			}
			
			?>
			
			<div class="check-info" style="margin-top: 20px;">
				<p>
					<?= Loc::getMessage('TIME_LABEL') ?> <?= $last_Check_Date ?>.
				</p>
				<? if($last_Check_Date != 'не определены'): ?>
					<p style="margin-top: 15px;">
						Значения, указанные при последней проверке:
						<ul>
							<li>Минимальная ширина: <span class="check-info-value"><?= $last_Check_Min_Width ?></span></li>
							<li>Минимальная высота: <span class="check-info-value"><?= $last_Check_Min_Height ?></span></li>
							<li>Максимальная ширина: <span class="check-info-value"><?= $last_Check_Max_Width ?></span></li>
							<li>Максимальная высота: <span class="check-info-value"><?= $last_Check_Max_Height ?></span></li>
							<li>Максимальный вес: <span class="check-info-value"><?= $last_Check_Max_Size ?></span></li>
						</ul>
					</p>
				<? endif; ?>
			</div>
			
			<?
			
			$check_Elements_Results = CheckResultHelper::Get('checkforitems_images_size', 'CheckforitemsImagesSize', 'UF_CHECK_IMAGE_SIZE');
			if(empty($check_Elements_Results) || !is_array($check_Elements_Results)) {
				print '<strong style="display: block; margin-top: 20px;">' . Loc::getMessage('EMPTY_RESULT_TEXT') . '</strong>';
			}
			else {
				
				?>
				
				<h3 style="margin-top: 20px;"><?= Loc::getMessage('TITLE_RESULT_TEXT') ?></h3>
				<div style="margin-bottom: 20px;">Количество найденных элементов: <?= count($check_Elements_Results) ?></div>
				
				<?
				
				foreach($check_Elements_Results as $check_Elements_Result) {
					$check_Elements_Result = unserialize($check_Elements_Result);
					
					?>
					
					<? if($check_Elements_Result !== FALSE): ?>
						<div style="margin-bottom: 10px;">
							<div>
								<div>
									В элементе
									<?= ($check_Elements_Result['IBLOCK_TYPE_ID'] == 'catalog' ? 'каталога' : ($check_Elements_Result['IBLOCK_TYPE_ID'] == 'manufacturers' ? 'производителей' : '')) ?>
									<strong><?= $check_Elements_Result['NAME'] ?></strong>
									найдено изображение, имеющее неверное разрешение или вес.
								</div>
								<? if($check_Elements_Result['DETAIL_PICTURE_DETECTED']): ?>
									<div>
										В поле "Детальная картинка":
										<ul style="margin: 0px !important;">
											<? foreach($check_Elements_Result['DETAIL_PICTURE_DATA'] as $detail_picture_data): ?>
												<li>
													Изображение <a href="<?= $detail_picture_data['DETAIL_PICTURE_NAME'] ?>" target="_blank"><?= $detail_picture_data['DETAIL_PICTURE_NAME'] ?></a>
													<?= (!empty($detail_picture_data['DETAIL_PICTURE_SIZE']) ? (', разрешение <span class="error-value">' . $detail_picture_data['DETAIL_PICTURE_SIZE'] . '</span>') : '') ?>
													<?= (!empty($detail_picture_data['DETAIL_PICTURE_WEIGHT']) ? (', вес <span class="error-value">' . $detail_picture_data['DETAIL_PICTURE_WEIGHT'] . '</span>') : '') ?>
													.
												</li>
											<? endforeach; ?>
										</ul>
									</div>
								<? endif; ?>
								<? if($check_Elements_Result['DETAIL_TEXT_PICTURE_DETECTED']): ?>
									<div>
										В поле "Детальное описание":
										<ul style="margin: 0px !important;">
											<? foreach($check_Elements_Result['DETAIL_TEXT_PICTURE_DATA'] as $detail_text_picture_data): ?>
												<li>
													Изображение <a href="<?= $detail_text_picture_data['DETAIL_TEXT_PICTURE_NAME'] ?>" target="_blank"><?= $detail_text_picture_data['DETAIL_TEXT_PICTURE_NAME'] ?></a>
													<?= (!empty($detail_text_picture_data['DETAIL_TEXT_PICTURE_SIZE']) ? (', разрешение <span class="error-value">' . $detail_text_picture_data['DETAIL_TEXT_PICTURE_SIZE'] . '</span>') : '') ?>
													<?= (!empty($detail_text_picture_data['DETAIL_TEXT_PICTURE_WEIGHT']) ? (', вес <span class="error-value">' . $detail_text_picture_data['DETAIL_TEXT_PICTURE_WEIGHT'] . '</span>') : '') ?>
													.
												</li>
											<? endforeach; ?>
										</ul>
									</div>
								<? endif; ?>
								<? if($check_Elements_Result['MORE_PICTURE_DETECTED']): ?>
									<div>
										В свойстве "Дополнительное изображение":
										<ul style="margin: 0px !important;">
											<? foreach($check_Elements_Result['MORE_PICTURE_DATA'] as $more_picture_detected): ?>
												<li>
													Изображение <a href="<?= $more_picture_detected['MORE_PICTURE_NAME'] ?>" target="_blank"><?= $more_picture_detected['MORE_PICTURE_NAME'] ?></a>
													<?= (!empty($more_picture_detected['MORE_PICTURE_SIZE']) ? (', разрешение <span class="error-value">' . $more_picture_detected['MORE_PICTURE_SIZE'] . '</span>') : '') ?>
													<?= (!empty($more_picture_detected['MORE_PICTURE_WEIGHT']) ? (', вес <span class="error-value">' . $more_picture_detected['MORE_PICTURE_WEIGHT'] . '</span>') : '') ?>
													.
												</li>
											<? endforeach; ?>
										</ul>
									</div>
								<? endif; ?>
								<div>
									<a href="iblock_element_edit.php?IBLOCK_ID=<?= $check_Elements_Result['IBLOCK_ID'] ?>&type=<?= $check_Elements_Result['IBLOCK_TYPE_ID'] ?>&ID=<?= $check_Elements_Result['ID'] ?>&lang=<?= LANGUAGE_ID ?>" target="_blank">
										Перейти к редактированию элемента
									</a>
								</div>
							</div>
						</div>
					<? endif; ?>
					
					<?
					
				}
			}
			
			if(isset($_GET['mode']) && $_GET['mode'] == 'result') {
				$result_Html = ob_get_contents();
				ob_end_clean();
				echo CUtil::PhpToJSObject(array('html' => $result_Html));
				exit();
			}
		}
		
		?>
	</div>
	
	<script type="text/javascript">
		function checkElementsImages(is_First) {
			var min_Width = parseInt(BX("min-width").value, 10);
			var max_Width = parseInt(BX("max-width").value, 10);
			var min_Height = parseInt(BX("min-height").value, 10);
			var max_Height = parseInt(BX("max-height").value, 10);
			var max_Size = parseInt(BX("max-size").value, 10);
			
			if(is_First) {
				if(Number.isInteger(min_Width) && Number.isInteger(max_Width) && Number.isInteger(min_Height) && Number.isInteger(max_Height) && Number.isInteger(max_Size)) {
					if(min_Width >= max_Width) {
						alert("Максимальное значение ширины должно быть больше минимального значения ширины!");
						return false;
					}
					if(min_Height >= max_Height) {
						alert("Максимальное значение высоты должно быть больше минимального значения высоты!");
						return false;
					}
				}
				else {
					alert("Не все поля заполнены заполнены корректными значениями!");
					return false;
				}
			}
			
			var reqParams = "&min_width=" + min_Width + "&max_width=" + max_Width + "&min_height=" + min_Height + "&max_height=" + max_Height + "&max_size=" + max_Size;
			
			var part = BX("part").value;
			var checked_Elements_Count = BX("checked-elements-count").value;
			var total_Elements_Invalid_Size_Count = BX("total-elements-invalid-size-count").value;
			
			BX.adjust(BX("result-data"), {html: ""});
			BX.adjust(BX("new-check-button"), {props: {disabled: true}});
			BX.adjust(BX("min-width"), {props: {disabled: true}});
			BX.adjust(BX("min-height"), {props: {disabled: true}});
			BX.adjust(BX("max-width"), {props: {disabled: true}});
			BX.adjust(BX("max-height"), {props: {disabled: true}});
			BX.adjust(BX("max-size"), {props: {disabled: true}});
			
			BX.ajax.loadJSON("<?= $APPLICATION->GetCurUri("mode=check")?>&part=" + part + "&checked_elements_count=" + checked_Elements_Count + "&total_elements_invalid_size_count=" + total_Elements_Invalid_Size_Count + reqParams, function (data){
				BX.adjust(BX("result-info"), {html:data.html});
				if(data.part == "done") {
					BX("part").value = 1;
					BX("checked-elements-count").value = 0;
					BX("total-elements-invalid-size-count").value = 0;
					
					BX.adjust(BX("new-check-button"), {props: {disabled: false}});
					BX.adjust(BX("min-width"), {props: {disabled: false}});
					BX.adjust(BX("min-height"), {props: {disabled: false}});
					BX.adjust(BX("max-width"), {props: {disabled: false}});
					BX.adjust(BX("max-height"), {props: {disabled: false}});
					BX.adjust(BX("max-size"), {props: {disabled: false}});
					
					BX.ajax.loadJSON("<?= $APPLICATION->GetCurUri("mode=result")?>", function (data){
						BX.adjust(BX("result-data"), {html:data.html});
					});
				}
				else {
					BX("part").value = data.part;
					BX("checked-elements-count").value = data.checked_Elements_Count;
					BX("total-elements-invalid-size-count").value = data.total_Elements_Invalid_Size_Count;
					var int_Random = Math.floor(Math.random() * 2000 + 1) + 1000;
					setTimeout(checkElementsImages(false), int_Random);
				}
			});
		}
	</script>

<? endif; ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>
