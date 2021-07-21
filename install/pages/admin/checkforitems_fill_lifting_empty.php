<?

$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];
$local_modules_path = "$DOCUMENT_ROOT/local/modules/checkforitems/admin/checkforitems";
$bitrix_modules_path = "$DOCUMENT_ROOT/bitrix/modules/checkforitems/admin/checkforitems";
$page_include_path = (file_exists($local_modules_path) ? $local_modules_path : (file_exists($bitrix_modules_path) ? $bitrix_modules_path : ''));

if (!empty($page_include_path)) {
    require "$page_include_path/fill/lifting_empty.php";
}
