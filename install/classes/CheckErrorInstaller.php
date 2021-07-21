<?

class CheckErrorInstaller
{
    public static function InstallFiles()
    {
        CopyDirFiles(__DIR__ . "/../pages/admin/checkforitems_error_code.php", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/checkforitems_error_code.php", true, true);

        return true;
    }

    public static function UninstallFiles()
    {
        DeleteDirFilesEx("/bitrix/admin/checkforitems_error_code.php");

        return true;
    }
}
