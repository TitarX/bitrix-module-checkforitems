<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

include_once __DIR__ . '/classes/CheckDoubleInstaller.php';
include_once __DIR__ . '/classes/CheckErrorInstaller.php';
include_once __DIR__ . '/classes/CheckFillInstaller.php';
include_once __DIR__ . '/classes/CheckImagesInstaller.php';
include_once __DIR__ . '/classes/CheckCommonInstaller.php';

class CheckForItems extends CModule
{
    var $MODULE_ID = "checkforitems";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    function checkforitems()
    {
        $arModuleVersion = array();

        include(__DIR__ . "/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME = Loc::getMessage("MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("MODULE_DESCRIPTION");
    }

    function DoInstall()
    {
        global $APPLICATION;

        CheckDoubleInstaller::InstallFiles();
        CheckErrorInstaller::InstallFiles();
        CheckFillInstaller::InstallFiles();
        CheckImagesInstaller::InstallFiles();

        CheckCommonInstaller::InstallCommonHighloadBlock();
        CheckImagesInstaller::InstallFormatHighloadBlock();
        CheckImagesInstaller::InstallSizeHighloadBlock();

        RegisterModule("checkforitems");
        $APPLICATION->IncludeAdminFile(Loc::getMessage("MODULE_INSTALL_TEXT") . " checkforitems", __DIR__ . "/step.php");
    }

    function DoUninstall()
    {
        global $APPLICATION;

        CheckDoubleInstaller::UninstallFiles();
        CheckErrorInstaller::UninstallFiles();
        CheckFillInstaller::UninstallFiles();
        CheckImagesInstaller::UninstallFiles();

        CheckCommonInstaller::UninstallCommonHighloadBlock();
        CheckImagesInstaller::UninstallFormatHighloadBlock();
        CheckImagesInstaller::UninstallSizeHighloadBlock();

        UnRegisterModule("checkforitems");
        $APPLICATION->IncludeAdminFile(Loc::getMessage("MODULE_UNINSTALL_TEXT") . " checkforitems", __DIR__ . "/unstep.php");
    }
}
