<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin.php"); ?>

<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
?>

<?
if (CModule::IncludeModule('iblock')): ?>

    <?

    global $APPLICATION;
    $APPLICATION->SetTitle(Loc::getMessage('PAGE_TITLE'));

    include_once __DIR__ . '/../../../classes/CheckResultHelper.php';

    @set_time_limit(360);

    $check_Name = 'images_format';
    $part_Elements_Limit = 600;
    $max_Result_Count = 50;

    ?>

    <style type="text/css">
        #new-check-button:active {
            height: 29px !important;
        }

        .check-info {
            color: orangered;
            font-weight: bold;
        }

        .check-info > p {
            margin: 0px;
        }
    </style>

    <div style="margin-bottom: 20px;">
        <small><?= Loc::getMessage('PAGE_DESCRIPTION') ?></small>
        <br/>
        <small><?= Loc::getMessage('MAX_RESULT_SIZE') ?> <?= $max_Result_Count ?>.</small>
    </div>

    <button id="new-check-button" name="new-check-button" class="adm-btn adm-btn-save" onclick="checkElementsImages(); return false;">
        <?= Loc::getMessage('START_CHECK_BUTTON_TEXT') ?>
    </button>
    <input type="hidden" id="part" name="part" value="1"/>
    <input type="hidden" id="checked-elements-count" name="checked-elements-count" value="0"/>
    <input type="hidden" id="total-elements-invalid-format-count" name="total-elements-invalid-format-count" value="0"/>
    <div id="result-info"></div>

    <div id="result-data">
        <?

        if (isset($_GET['mode']) && $_GET['mode'] == 'check') {
            $APPLICATION->RestartBuffer();

            $error_Messages_Html = '';
            $part = $_GET['part'];
            $checked_Elements_Count = $_GET['checked_elements_count'];
            $total_Elements_Invalid_Format_Count = $_GET['total_elements_invalid_format_count'];

            if ($part == '1') {
                $delete_Result = CheckResultHelper::Delete('checkforitems_images_format', 'CheckforitemsImagesFormat');
                if (is_array($delete_Result)) {
                    foreach ($delete_Result as $error_Message) {
                        $admin_Message_Error = new CAdminMessage(array('MESSAGE' => 'Ошибка удаления', 'DETAILS' => $error_Message, 'TYPE' => 'ERROR', 'HTML' => true));
                        $error_Messages_Html .= $admin_Message_Error->Show();
                    }
                }
            }

            $arOrder = array();
            $arFilter = array(
                'IBLOCK_TYPE' => array('catalog', 'manufacturers'),
                'ACTIVE' => 'Y'
            );
            $arGroupBy = false;
            $arNavStartParams = array(
                'nPageSize' => $part_Elements_Limit,
                'iNumPage' => $part,
                'checkOutOfRange' => true
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
            $elements_Invalid_Format_Count = 0;
            $catalog_Element_Result = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelect);

            while (($catalog_Element_Array = $catalog_Element_Result->GetNext()) && ($total_Elements_Invalid_Format_Count + $elements_Invalid_Format_Count < $max_Result_Count)) {
                $is_Element_Invalid_Format = false;

                $check_Elements_Result = array();
                $check_Elements_Result['ID'] = '';
                $check_Elements_Result['CODE'] = '';
                $check_Elements_Result['NAME'] = '';
                $check_Elements_Result['IBLOCK_ID'] = '';
                $check_Elements_Result['IBLOCK_TYPE_ID'] = '';
                $check_Elements_Result['DETAIL_PICTURE_DETECTED'] = false;
                $check_Elements_Result['DETAIL_TEXT_PICTURE_DETECTED'] = false;
                $check_Elements_Result['MORE_PICTURE_DETECTED'] = false;

                // Детальная картинка >>>
                $field_Content = CFile::GetPath($catalog_Element_Array['DETAIL_PICTURE']);
                $field_Content = trim($field_Content);
                if (!empty($field_Content)) {
                    $picture_Extension = pathinfo($field_Content, PATHINFO_EXTENSION);
                    $picture_Extension = strtolower($picture_Extension);
                    if (!empty($picture_Extension) && $picture_Extension !== 'jpg' && $picture_Extension !== 'jpeg' && $picture_Extension !== 'jpe') {
                        $is_Element_Invalid_Format = true;
                        $check_Elements_Result['DETAIL_PICTURE_DETECTED'] = true;
                    }
                }
                // <<< Детальная картинка

                // Детальное описание >>>
                if ($catalog_Element_Array['DETAIL_TEXT_TYPE'] == 'html') {
                    $preg_Img_Pattern = "/<img[^>]*src=[\"']([^\"']+)[\"'][^>]*>/i";
                    $preg_Img_Results = array();
                    if (preg_match_all($preg_Img_Pattern, $catalog_Element_Array['DETAIL_TEXT'], $preg_Img_Results) && isset($preg_Img_Results[1])) {
                        foreach ($preg_Img_Results[1] as $preg_Img_Result) {
                            $picture_Extension = pathinfo($preg_Img_Result, PATHINFO_EXTENSION);
                            $picture_Extension = strtolower($picture_Extension);
                            if (!empty($picture_Extension)) {
                                if ($picture_Extension !== 'jpg' && $picture_Extension !== 'jpeg' && $picture_Extension !== 'jpe') {
                                    $is_Element_Invalid_Format = true;
                                    $check_Elements_Result['DETAIL_TEXT_PICTURE_DETECTED'] = true;
                                    break;
                                }
                            }
                        }
                    }
                }
                // <<< Детальное описание

                // Дополнительные изображения >>>
                $arPropertyOrder = array();
                $arPropertyFilter = array(
                    'CODE' => 'MORE_PHOTO'
                );
                $element_Property_Result = CIBlockElement::GetProperty($catalog_Element_Array['IBLOCK_ID'], $catalog_Element_Array['ID'], $arPropertyOrder, $arPropertyFilter);
                while ($element_Property_Array = $element_Property_Result->GetNext()) {
                    $field_Content = CFile::GetPath($element_Property_Array['VALUE']);
                    $field_Content = trim($field_Content);
                    if (!empty($field_Content)) {
                        $picture_Extension = pathinfo($field_Content, PATHINFO_EXTENSION);
                        $picture_Extension = strtolower($picture_Extension);
                        if (!empty($picture_Extension) && $picture_Extension !== 'jpg' && $picture_Extension !== 'jpeg' && $picture_Extension !== 'jpe') {
                            $is_Element_Invalid_Format = true;
                            $check_Elements_Result['MORE_PICTURE_DETECTED'] = true;
                            break;
                        }
                    }
                }
                // <<< Дополнительные изображения

                if ($is_Element_Invalid_Format) {
                    if (!CheckResultHelper::IsExist('checkforitems_images_format', 'CheckforitemsImagesFormat', 'UF_CHECK_IMAGE_FORM', $catalog_Element_Array['ID'])) {
                        $check_Elements_Result['ID'] = $catalog_Element_Array['ID'];
                        $check_Elements_Result['CODE'] = $catalog_Element_Array['CODE'];
                        $check_Elements_Result['NAME'] = $catalog_Element_Array['NAME'];
                        $check_Elements_Result['IBLOCK_ID'] = $catalog_Element_Array['IBLOCK_ID'];
                        $check_Elements_Result['IBLOCK_TYPE_ID'] = $catalog_Element_Array['IBLOCK_TYPE_ID'];

                        $check_Elements_Result = serialize($check_Elements_Result);
                        $append_Result = CheckResultHelper::Append(
                            'checkforitems_images_format',
                            'CheckforitemsImagesFormat',
                            array('UF_CHECK_IMAGE_FORM' => $check_Elements_Result)
                        );
                        if (is_array($append_Result)) {
                            foreach ($append_Result as $error_Message) {
                                $admin_Message_Error = new CAdminMessage(array('MESSAGE' => 'Ошибка записи', 'DETAILS' => $error_Message, 'TYPE' => 'ERROR', 'HTML' => true));
                                $error_Messages_Html .= $admin_Message_Error->Show();
                            }
                        }
                    }

                    $elements_Invalid_Format_Count++;
                }

                $elements_Count++;
            }

            $total_Elements_Invalid_Format_Count += $elements_Invalid_Format_Count;
            $checked_Elements_Count += $elements_Count;
            if ($total_Elements_Invalid_Format_Count >= $max_Result_Count || $elements_Count == 0) {
                $delete_Result = CheckResultHelper::Delete('checkforitems_common', 'CheckforitemsCommon', 'UF_CHECK_COM_TYPE', $check_Name);
                if (is_array($delete_Result)) {
                    foreach ($delete_Result as $error_Message) {
                        $admin_Message_Error = new CAdminMessage(array('MESSAGE' => 'Ошибка удаления', 'DETAILS' => $error_Message, 'TYPE' => 'ERROR', 'HTML' => true));
                        $error_Messages_Html .= $admin_Message_Error->Show();
                    }
                }

                $check_Data = array('UF_CHECK_COM_TYPE' => $check_Name, 'UF_CHECK_COM_DATA' => time());
                $append_Result = CheckResultHelper::Append('checkforitems_common', 'CheckforitemsCommon', $check_Data);
                if (is_array($append_Result)) {
                    foreach ($append_Result as $error_Message) {
                        $admin_Message_Error = new CAdminMessage(array('MESSAGE' => 'Ошибка записи', 'DETAILS' => $error_Message, 'TYPE' => 'ERROR', 'HTML' => true));
                        $error_Messages_Html .= $admin_Message_Error->Show();
                    }
                }

                $note_Messages_Html = new CAdminMessage(
                    array('MESSAGE' => 'Проверка завершена', 'DETAILS' => "Проверено элементов: <strong>$checked_Elements_Count</strong>", 'TYPE' => 'OK', 'HTML' => true)
                );
                echo CUtil::PhpToJSObject(array('part' => 'done', 'html' => $error_Messages_Html . $note_Messages_Html->Show()));
                exit();
            } else {
                $note_Messages_Html = new CAdminMessage(
                    array(
                        'MESSAGE' => 'Шаг: ' . $part . '. Проверено элементов: ' . $checked_Elements_Count . '. Найдено: ' . $total_Elements_Invalid_Format_Count . '.',
                        'DETAILS' => 'Дождитесь окончания проверки элементов',
                        'TYPE' => 'OK',
                        'HTML' => true
                    )
                );
                echo CUtil::PhpToJSObject(
                    array(
                        'part' => $part + 1,
                        'checked_Elements_Count' => $checked_Elements_Count,
                        'total_Elements_Invalid_Format_Count' => $total_Elements_Invalid_Format_Count,
                        'html' => $error_Messages_Html . $note_Messages_Html->Show()
                    )
                );
                exit();
            }
        } else {
            if (isset($_GET['mode']) && $_GET['mode'] == 'result') {
                $APPLICATION->RestartBuffer();
                ob_start();
            }

            $last_Check_Date = '';
            $check_Data_Results = CheckResultHelper::Get('checkforitems_common', 'CheckforitemsCommon', 'UF_CHECK_COM_DATA', array('UF_CHECK_COM_TYPE' => $check_Name));
            if (!empty($check_Data_Results) || is_array($check_Data_Results)) {
                $last_Check_Date = $check_Data_Results[0];
            }
            if (empty($last_Check_Date)) {
                $last_Check_Date = 'не определены';
            } else {
                $last_Check_Date = date('d.m.Y - H:i:s (e P)', $last_Check_Date);
            }

            ?>

            <div class="check-info" style="margin-top: 20px;">
                <?= Loc::getMessage('TIME_LABEL') ?> <?= $last_Check_Date ?>.
            </div>

            <?

            $check_Elements_Results = CheckResultHelper::Get('checkforitems_images_format', 'CheckforitemsImagesFormat', 'UF_CHECK_IMAGE_FORM');
            if (empty($check_Elements_Results) || !is_array($check_Elements_Results)) {
                print '<strong style="display: block; margin-top: 20px;">' . Loc::getMessage('EMPTY_RESULT_TEXT') . '</strong>';
            } else {
                ?>

                <h3 style="margin-top: 20px;"><?= Loc::getMessage('TITLE_RESULT_TEXT') ?></h3>
                <div style="margin-bottom: 20px;">Количество элементов: <?= count($check_Elements_Results) ?></div>

                <?

                foreach ($check_Elements_Results as $check_Elements_Result) {
                    $check_Elements_Result = unserialize($check_Elements_Result);

                    ?>

                    <?
                    if ($check_Elements_Result !== false): ?>
                        <div style="margin-bottom: 10px;">
                            <div>
                                <div>
                                    В элементе
                                    <?= ($check_Elements_Result['IBLOCK_TYPE_ID'] == 'catalog' ? 'каталога' : ($check_Elements_Result['IBLOCK_TYPE_ID'] == 'manufacturers' ? 'производителей' : '')) ?>
                                    <strong><?= $check_Elements_Result['NAME'] ?></strong>
                                    найдено изображение, имеющее не JPEG формат.
                                </div>
                                <?
                                if ($check_Elements_Result['DETAIL_PICTURE_DETECTED']): ?>
                                    <div>
                                        В поле "Детальная картинка".
                                    </div>
                                <?
                                endif; ?>
                                <?
                                if ($check_Elements_Result['DETAIL_TEXT_PICTURE_DETECTED']): ?>
                                    <div>
                                        В поле "Детальное описание".
                                    </div>
                                <?
                                endif; ?>
                                <?
                                if ($check_Elements_Result['MORE_PICTURE_DETECTED']): ?>
                                    <div>
                                        В свойстве "Дополнительное изображение".
                                    </div>
                                <?
                                endif; ?>
                                <div>
                                    <a href="iblock_element_edit.php?IBLOCK_ID=<?= $check_Elements_Result['IBLOCK_ID'] ?>&type=<?= $check_Elements_Result['IBLOCK_TYPE_ID'] ?>&ID=<?= $check_Elements_Result['ID'] ?>&lang=<?= LANGUAGE_ID ?>"
                                       target="_blank">
                                        Перейти к редактированию элемента
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?
                    endif; ?>

                    <?
                }
            }

            if (isset($_GET['mode']) && $_GET['mode'] == 'result') {
                $result_Html = ob_get_contents();
                ob_end_clean();
                echo CUtil::PhpToJSObject(array('html' => $result_Html));
                exit();
            }
        }

        ?>
    </div>

    <script type="text/javascript">
        function checkElementsImages() {
            BX.adjust(BX("result-data"), {html: ""});
            BX.adjust(BX("new-check-button"), {props: {disabled: true}});
            var part = BX("part").value;
            var checked_Elements_Count = BX("checked-elements-count").value;
            var total_Elements_Invalid_Format_Count = BX("total-elements-invalid-format-count").value;
            BX.ajax.loadJSON("<?= $APPLICATION->GetCurUri(
                "mode=check"
            )?>&part=" + part + "&checked_elements_count=" + checked_Elements_Count + "&total_elements_invalid_format_count=" + total_Elements_Invalid_Format_Count, function (data) {
                BX.adjust(BX("result-info"), {html: data.html});
                if (data.part == "done") {
                    BX("part").value = 1;
                    BX("checked-elements-count").value = 0;
                    BX("total-elements-invalid-format-count").value = 0;
                    BX.adjust(BX("new-check-button"), {props: {disabled: false}});

                    BX.ajax.loadJSON("<?= $APPLICATION->GetCurUri("mode=result")?>", function (data) {
                        BX.adjust(BX("result-data"), {html: data.html});
                    });
                } else {
                    BX("part").value = data.part;
                    BX("checked-elements-count").value = data.checked_Elements_Count;
                    BX("total-elements-invalid-format-count").value = data.total_Elements_Invalid_Format_Count;
                    var int_Random = Math.floor(Math.random() * 2000 + 1) + 1000;
                    setTimeout(checkElementsImages(), int_Random);
                }
            });
        }
    </script>

<?
endif; ?>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>
