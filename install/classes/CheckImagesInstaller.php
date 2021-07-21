<?

class CheckImagesInstaller
{
    public static function InstallFiles()
    {
        CopyDirFiles(__DIR__ . "/../pages/admin/checkforitems_images_format.php", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/checkforitems_images_format.php", true, true);
        CopyDirFiles(__DIR__ . "/../pages/admin/checkforitems_images_size.php", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/checkforitems_images_size.php", true, true);
        CopyDirFiles(__DIR__ . "/../pages/admin/checkforitems_images_name.php", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/checkforitems_images_name.php", true, true);

        return true;
    }

    public static function UninstallFiles()
    {
        DeleteDirFilesEx("/bitrix/admin/checkforitems_images_format.php");
        DeleteDirFilesEx("/bitrix/admin/checkforitems_images_size.php");
        DeleteDirFilesEx("/bitrix/admin/checkforitems_images_name.php");

        return true;
    }

    public static function InstallFormatHighloadBlock()
    {
        if (CModule::IncludeModule('highloadblock')) {
            $hl_Add_Params = array(
                'NAME' => 'CheckforitemsImagesFormat',
                'TABLE_NAME' => 'checkforitems_images_format'
            );
            $hl_Add_Result = \Bitrix\Highloadblock\HighloadBlockTable::add($hl_Add_Params);
            if ($hl_Add_Result->isSuccess()) {
                $hl_Add_Result_Id = $hl_Add_Result->getId();
                $uf_Check_Image_Format = array(
                    'ENTITY_ID' => 'HLBLOCK_' . $hl_Add_Result_Id,
                    'FIELD_NAME' => 'UF_CHECK_IMAGE_FORM',
                    'XML_ID' => 'XML_ID_UF_CHECK_IMAGE_FORM',
                    'USER_TYPE_ID' => 'string',
                    'SORT' => 100,
                    'MULTIPLE' => 'N',
                    'MANDATORY' => 'N',
                    'SHOW_FILTER' => 'N',
                    'SHOW_IN_LIST' => '',
                    'EDIT_IN_LIST' => '',
                    'IS_SEARCHABLE' => 'N',
                    'SETTINGS' => array(
                        'DEFAULT_VALUE' => '',
                        'SIZE' => '30',
                        'ROWS' => '1',
                        'MIN_LENGTH' => '0',
                        'MAX_LENGTH' => '0',
                        'REGEXP' => ''
                    ),
                    'EDIT_FORM_LABEL' => array(
                        'ru' => 'Данные элемента',
                        'en' => 'Element data'
                    ),
                    'LIST_COLUMN_LABEL' => array(
                        'ru' => 'Данные элемента',
                        'en' => 'Element data'
                    ),
                    'LIST_FILTER_LABEL' => array(
                        'ru' => 'Данные элемента',
                        'en' => 'Element data'
                    ),
                    'ERROR_MESSAGE' => array(
                        'ru' => 'Ошибка при заполнении пользовательского поля',
                        'en' => 'Error filling out the custom field'
                    ),
                    'HELP_MESSAGE' => array(
                        'ru' => 'Сериализованный массив данных элемента',
                        'en' => 'A serialized array of item data'
                    )
                );

                $cUserTypeEntity = new CUserTypeEntity();

                $arUserFieldId = $cUserTypeEntity->Add($uf_Check_Image_Format);
                if (empty($arUserFieldId)) {
                    foreach ($hl_Add_Result->getErrorMessages() as $error_Message) {
                        CAdminMessage::ShowMessage($error_Message);
                    }
                }
            }
        }
    }

    public static function InstallSizeHighloadBlock()
    {
        if (CModule::IncludeModule('highloadblock')) {
            $hl_Add_Params = array(
                'NAME' => 'CheckforitemsImagesSize',
                'TABLE_NAME' => 'checkforitems_images_size'
            );
            $hl_Add_Result = \Bitrix\Highloadblock\HighloadBlockTable::add($hl_Add_Params);
            if ($hl_Add_Result->isSuccess()) {
                $hl_Add_Result_Id = $hl_Add_Result->getId();
                $uf_Check_Image_Size = array(
                    'ENTITY_ID' => 'HLBLOCK_' . $hl_Add_Result_Id,
                    'FIELD_NAME' => 'UF_CHECK_IMAGE_SIZE',
                    'XML_ID' => 'XML_ID_UF_CHECK_IMAGE_SIZE',
                    'USER_TYPE_ID' => 'string',
                    'SORT' => 100,
                    'MULTIPLE' => 'N',
                    'MANDATORY' => 'N',
                    'SHOW_FILTER' => 'N',
                    'SHOW_IN_LIST' => '',
                    'EDIT_IN_LIST' => '',
                    'IS_SEARCHABLE' => 'N',
                    'SETTINGS' => array(
                        'DEFAULT_VALUE' => '',
                        'SIZE' => '30',
                        'ROWS' => '1',
                        'MIN_LENGTH' => '0',
                        'MAX_LENGTH' => '0',
                        'REGEXP' => ''
                    ),
                    'EDIT_FORM_LABEL' => array(
                        'ru' => 'Данные элемента',
                        'en' => 'Element data'
                    ),
                    'LIST_COLUMN_LABEL' => array(
                        'ru' => 'Данные элемента',
                        'en' => 'Element data'
                    ),
                    'LIST_FILTER_LABEL' => array(
                        'ru' => 'Данные элемента',
                        'en' => 'Element data'
                    ),
                    'ERROR_MESSAGE' => array(
                        'ru' => 'Ошибка при заполнении пользовательского поля',
                        'en' => 'Error filling out the custom field'
                    ),
                    'HELP_MESSAGE' => array(
                        'ru' => 'Сериализованный массив данных элемента',
                        'en' => 'A serialized array of item data'
                    )
                );

                $cUserTypeEntity = new CUserTypeEntity();

                $arUserFieldId = $cUserTypeEntity->Add($uf_Check_Image_Size);
                if (empty($arUserFieldId)) {
                    foreach ($hl_Add_Result->getErrorMessages() as $error_Message) {
                        CAdminMessage::ShowMessage($error_Message);
                    }
                }
            }
        }
    }

    public static function UninstallFormatHighloadBlock()
    {
        if (CModule::IncludeModule('highloadblock')) {
            $hl_Filter = array(
                'select' => array('ID'),
                'filter' => array('=NAME' => 'CheckforitemsImagesFormat')
            );
            $hl_Block = \Bitrix\Highloadblock\HighloadBlockTable::getList($hl_Filter)->fetch();
            if (!empty($hl_Block)) {
                $result = \Bitrix\Highloadblock\HighloadBlockTable::delete($hl_Block['ID']);
                if (!$result->isSuccess()) {
                    foreach ($result->getErrorMessages() as $error_Message) {
                        CAdminMessage::ShowMessage($error_Message);
                    }
                }
            }
        }
    }

    public static function UninstallSizeHighloadBlock()
    {
        if (CModule::IncludeModule('highloadblock')) {
            $hl_Filter = array(
                'select' => array('ID'),
                'filter' => array('=NAME' => 'CheckforitemsImagesSize')
            );
            $hl_Block = \Bitrix\Highloadblock\HighloadBlockTable::getList($hl_Filter)->fetch();
            if (!empty($hl_Block)) {
                $result = \Bitrix\Highloadblock\HighloadBlockTable::delete($hl_Block['ID']);
                if (!$result->isSuccess()) {
                    foreach ($result->getErrorMessages() as $error_Message) {
                        CAdminMessage::ShowMessage($error_Message);
                    }
                }
            }
        }
    }
}
