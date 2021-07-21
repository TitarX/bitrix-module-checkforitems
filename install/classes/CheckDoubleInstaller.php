<?

class CheckDoubleInstaller
{
    public static function InstallFiles()
    {
        CopyDirFiles(__DIR__ . "/../pages/admin/checkforitems_double_code.php", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/checkforitems_double_code.php", true, true);
        CopyDirFiles(__DIR__ . "/../pages/admin/checkforitems_double_name.php", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/checkforitems_double_name.php", true, true);
        CopyDirFiles(__DIR__ . "/../pages/admin/checkforitems_double_fullname.php", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/checkforitems_double_fullname.php", true, true);

        return true;
    }

    public static function UninstallFiles()
    {
        DeleteDirFilesEx("/bitrix/admin/checkforitems_double_code.php");
        DeleteDirFilesEx("/bitrix/admin/checkforitems_double_name.php");
        DeleteDirFilesEx("/bitrix/admin/checkforitems_double_fullname.php");

        return true;
    }
}
