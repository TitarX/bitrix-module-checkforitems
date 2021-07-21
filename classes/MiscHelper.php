<?

class MiscHelper
{
    public static function convertToReadableSize($size)
    {
        $base = log($size) / log(1024);
        $suffix = array('', ' Kb', ' Mb', ' Gb', ' Tb');
        $f_base = floor($base);
        return round(pow(1024, $base - floor($base)), 1) . $suffix[$f_base];
    }

    public static function convertKbToBytes($kb)
    {
        return $kb * 1024;
    }
}
