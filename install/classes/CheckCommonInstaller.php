<?

class CheckCommonInstaller
{
    public static function InstallCommonHighloadBlock()
    {
        if (CModule::IncludeModule('highloadblock')) {
            $hl_Add_Params = array(
                'NAME' => 'CheckforitemsCommon',
                'TABLE_NAME' => 'checkforitems_common'
            );
            $hl_Add_Result = \Bitrix\Highloadblock\HighloadBlockTable::add($hl_Add_Params);
            if ($hl_Add_Result->isSuccess()) {
                $hl_Add_Result_Id = $hl_Add_Result->getId();

                $uf_Check_Common_Type = array(
                    'ENTITY_ID' => 'HLBLOCK_' . $hl_Add_Result_Id,
                    'FIELD_NAME' => 'UF_CHECK_COM_TYPE',
                    'XML_ID' => 'XML_ID_UF_CHECK_COM_TYPE',
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
                        'ru' => 'Имя проверки',
                        'en' => 'Check name'
                    ),
                    'LIST_COLUMN_LABEL' => array(
                        'ru' => 'Имя проверки',
                        'en' => 'Check name'
                    ),
                    'LIST_FILTER_LABEL' => array(
                        'ru' => 'Имя проверки',
                        'en' => 'Check name'
                    ),
                    'ERROR_MESSAGE' => array(
                        'ru' => 'Ошибка при заполнении пользовательского поля',
                        'en' => 'Error filling out the custom field'
                    ),
                    'HELP_MESSAGE' => array(
                        'ru' => 'Имя проверки элементов',
                        'en' => 'A serialized array of item data'
                    )
                );

                $uf_Check_Common_Data = array(
                    'ENTITY_ID' => 'HLBLOCK_' . $hl_Add_Result_Id,
                    'FIELD_NAME' => 'UF_CHECK_COM_DATA',
                    'XML_ID' => 'XML_ID_UF_CHECK_COM_DATA',
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
                        'ru' => 'Данные последней проверки',
                        'en' => 'Last check data'
                    ),
                    'LIST_COLUMN_LABEL' => array(
                        'ru' => 'Данные последней проверки',
                        'en' => 'Last check data'
                    ),
                    'LIST_FILTER_LABEL' => array(
                        'ru' => 'Данные последней проверки',
                        'en' => 'Last check data'
                    ),
                    'ERROR_MESSAGE' => array(
                        'ru' => 'Ошибка при заполнении пользовательского поля',
                        'en' => 'Error filling out the custom field'
                    ),
                    'HELP_MESSAGE' => array(
                        'ru' => 'Данные последней проверки элементов',
                        'en' => 'A serialized array of item data'
                    )
                );

                $cUserTypeEntity = new CUserTypeEntity();

                $arUserFieldId = $cUserTypeEntity->Add($uf_Check_Common_Type);
                if (empty($arUserFieldId)) {
                    foreach ($hl_Add_Result->getErrorMessages() as $error_Message) {
                        CAdminMessage::ShowMessage($error_Message);
                    }
                }

                $arUserFieldId = $cUserTypeEntity->Add($uf_Check_Common_Data);
                if (empty($arUserFieldId)) {
                    foreach ($hl_Add_Result->getErrorMessages() as $error_Message) {
                        CAdminMessage::ShowMessage($error_Message);
                    }
                }
            }
        }
    }

    public static function UninstallCommonHighloadBlock()
    {
        if (CModule::IncludeModule('highloadblock')) {
            $hl_Filter = array(
                'select' => array('ID'),
                'filter' => array('=NAME' => 'CheckforitemsCommon')
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
