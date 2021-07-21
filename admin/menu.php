<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$aMenu = array(
    'parent_menu' => 'global_menu_services',
    'sort' => 1000,
    'text' => Loc::getMessage('CHECKFORITEMS_MENU_TEXT'),
    'title' => Loc::getMessage('CHECKFORITEMS_MENU_TITLE'),
    'icon' => '',
    'page_icon' => '',
    'url' => '',
    'items_id' => 'menu_checkforitems',
    'items' => array(
        array(
            'text' => Loc::getMessage('CHECKFORITEMS_MENU_DOUBLE_TEXT'),
            'title' => Loc::getMessage('CHECKFORITEMS_MENU_DOUBLE_TITLE'),
            'url' => '',
            'items' => array(
                array(
                    'text' => Loc::getMessage('CHECKFORITEMS_MENU_DOUBLE_NAMES_TEXT'),
                    'title' => Loc::getMessage('CHECKFORITEMS_MENU_DOUBLE_NAMES_TITLE'),
                    'url' => 'checkforitems_double_name.php?lang=' . LANGUAGE_ID
                ),
                array(
                    'text' => Loc::getMessage('CHECKFORITEMS_MENU_DOUBLE_FULL_NAMES_TEXT'),
                    'title' => Loc::getMessage('CHECKFORITEMS_MENU_DOUBLE_FULL_NAMES_TITLE'),
                    'url' => 'checkforitems_double_fullname.php?lang=' . LANGUAGE_ID
                ),
                array(
                    'text' => Loc::getMessage('CHECKFORITEMS_MENU_DOUBLE_CODE_TEXT'),
                    'title' => Loc::getMessage('CHECKFORITEMS_MENU_DOUBLE_CODE_TITLE'),
                    'url' => 'checkforitems_double_code.php?lang=' . LANGUAGE_ID
                )
            )
        ),
        array(
            'text' => Loc::getMessage('CHECKFORITEMS_MENU_ERROR_TEXT'),
            'title' => Loc::getMessage('CHECKFORITEMS_MENU_ERROR_TITLE'),
            'url' => '',
            'items' => array(
                array(
                    'text' => Loc::getMessage('CHECKFORITEMS_MENU_ERROR_CODE_TEXT'),
                    'title' => Loc::getMessage('CHECKFORITEMS_MENU_ERROR_CODE_TITLE'),
                    'url' => 'checkforitems_error_code.php?lang=' . LANGUAGE_ID
                )
            )
        ),
        array(
            'text' => Loc::getMessage('CHECKFORITEMS_MENU_FILL_TEXT'),
            'title' => Loc::getMessage('CHECKFORITEMS_MENU_FILL_TITLE'),
            'url' => '',
            'items' => array(
                array(
                    'text' => Loc::getMessage('CHECKFORITEMS_MENU_FILL_SERIES_MATCH_TEXT'),
                    'title' => Loc::getMessage('CHECKFORITEMS_MENU_FILL_SERIES_MATCH_TITLE'),
                    'url' => 'checkforitems_fill_series_match.php?lang=' . LANGUAGE_ID
                ),
                array(
                    'text' => Loc::getMessage('CHECKFORITEMS_MENU_FILL_PROIZVOD_EMPTY_TEXT'),
                    'title' => Loc::getMessage('CHECKFORITEMS_MENU_FILL_PROIZVOD_EMPTY_TITLE'),
                    'url' => 'checkforitems_fill_proizvod_empty.php?lang=' . LANGUAGE_ID
                ),
                array(
                    'text' => Loc::getMessage('CHECKFORITEMS_MENU_FILL_STRANA_EMPTY_TEXT'),
                    'title' => Loc::getMessage('CHECKFORITEMS_MENU_FILL_STRANA_EMPTY_TITLE'),
                    'url' => 'checkforitems_fill_strana_empty.php?lang=' . LANGUAGE_ID
                ),
                array(
                    'text' => Loc::getMessage('CHECKFORITEMS_MENU_FILL_LIFTING_EMPTY_TEXT'),
                    'title' => Loc::getMessage('CHECKFORITEMS_MENU_FILL_LIFTING_EMPTY_TITLE'),
                    'url' => 'checkforitems_fill_lifting_empty.php?lang=' . LANGUAGE_ID
                ),
                array(
                    'text' => Loc::getMessage('CHECKFORITEMS_MENU_FILL_LIFTING_FLOOR_EMPTY_TEXT'),
                    'title' => Loc::getMessage('CHECKFORITEMS_MENU_FILL_LIFTING_FLOOR_EMPTY_TITLE'),
                    'url' => 'checkforitems_fill_lifting_floor_empty.php?lang=' . LANGUAGE_ID
                )
            )
        ),
        array(
            'text' => Loc::getMessage('CHECKFORITEMS_MENU_IMG_TEXT'),
            'title' => Loc::getMessage('CHECKFORITEMS_MENU_IMG_TITLE'),
            'url' => '',
            'items' => array(
                array(
                    'text' => Loc::getMessage('CHECKFORITEMS_MENU_IMG_FORMAT_TEXT'),
                    'title' => Loc::getMessage('CHECKFORITEMS_MENU_IMG_FORMAT_TITLE'),
                    'url' => 'checkforitems_images_format.php?lang=' . LANGUAGE_ID
                ),
                array(
                    'text' => Loc::getMessage('CHECKFORITEMS_MENU_IMG_SIZE_TEXT'),
                    'title' => Loc::getMessage('CHECKFORITEMS_MENU_IMG_SIZE_TITLE'),
                    'url' => 'checkforitems_images_size.php?lang=' . LANGUAGE_ID
                ),
//				array(
//					'text' => Loc::getMessage('CHECKFORITEMS_MENU_IMG_NAME_TEXT'),
//					'title' => Loc::getMessage('CHECKFORITEMS_MENU_IMG_NAME_TITLE'),
//					'url' => 'checkforitems_images_name.php?lang=' . LANGUAGE_ID
//				)
            )
        )
    )
);

return (!empty($aMenu) ? $aMenu : false);
