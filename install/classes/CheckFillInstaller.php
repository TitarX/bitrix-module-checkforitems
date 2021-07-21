<?

class CheckFillInstaller
{
    public static function InstallFiles()
    {
        CopyDirFiles(
            __DIR__ . "/../pages/admin/checkforitems_fill_proizvod_empty.php",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/checkforitems_fill_proizvod_empty.php",
            true,
            true
        );
        CopyDirFiles(__DIR__ . "/../pages/admin/checkforitems_fill_strana_empty.php", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/checkforitems_fill_strana_empty.php", true, true);
        CopyDirFiles(__DIR__ . "/../pages/admin/checkforitems_fill_series_match.php", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/checkforitems_fill_series_match.php", true, true);
        CopyDirFiles(
            __DIR__ . "/../pages/admin/checkforitems_fill_lifting_empty.php",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/checkforitems_fill_lifting_empty.php",
            true,
            true
        );
        CopyDirFiles(
            __DIR__ . "/../pages/admin/checkforitems_fill_lifting_floor_empty.php",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/checkforitems_fill_lifting_floor_empty.php",
            true,
            true
        );

        return true;
    }

    public static function UninstallFiles()
    {
        DeleteDirFilesEx("/bitrix/admin/checkforitems_fill_proizvod_empty.php");
        DeleteDirFilesEx("/bitrix/admin/checkforitems_fill_strana_empty.php");
        DeleteDirFilesEx("/bitrix/admin/checkforitems_fill_series_match.php");
        DeleteDirFilesEx("/bitrix/admin/checkforitems_fill_lifting_empty.php");
        DeleteDirFilesEx("/bitrix/admin/checkforitems_fill_lifting_floor_empty.php");

        return true;
    }
}
