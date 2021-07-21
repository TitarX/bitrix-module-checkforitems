<?

class CheckResultHelper
{
    public static function Append($tb_Name, $hl_Name, $uf_Data)
    {
        $result = 0;

        if (CModule::IncludeModule('highloadblock')) {
            $hl_Filter = array(
                'select' => array('ID', 'NAME'),
                'filter' => array('=NAME' => $hl_Name)
            );

            $hl_Block = \Bitrix\Highloadblock\HighloadBlockTable::getList($hl_Filter)->fetch();
            if (!empty($hl_Block)) {
                $hl_Block_Entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity(
                    array(
                        'ID' => $hl_Block['ID'],
                        'NAME' => $hl_Block['NAME'],
                        'TABLE_NAME' => $tb_Name
                    )
                );

                $hl_Block_Class = $hl_Block_Entity->getDataClass();
                $hl_Block_Add_Data_Result = $hl_Block_Class::add($uf_Data);

                if ($hl_Block_Add_Data_Result->isSuccess()) {
                    $result++;
                } else {
                    $result = $hl_Block_Add_Data_Result->getErrorMessages();
                }
            }
        }

        return $result;
    }

    public static function Get($tb_Name, $hl_Name, $uf_Name, $uf_Filter = array())
    {
        $result = array();

        if (CModule::IncludeModule('highloadblock')) {
            $ar_Data_Query = array(
                'select' => array($uf_Name),
                'filter' => $uf_Filter
            );

            $hl_Filter = array(
                'select' => array('ID', 'NAME'),
                'filter' => array('=NAME' => $hl_Name)
            );

            $hl_Block = \Bitrix\Highloadblock\HighloadBlockTable::getList($hl_Filter)->fetch();
            if (!empty($hl_Block)) {
                $hl_Block_Entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity(
                    array(
                        'ID' => $hl_Block['ID'],
                        'NAME' => $hl_Block['NAME'],
                        'TABLE_NAME' => $tb_Name
                    )
                );

                $hl_Block_Class = $hl_Block_Entity->getDataClass();
                $hl_Data = $hl_Block_Class::getList($ar_Data_Query);
                $hl_Data = new CDBResult($hl_Data, $tb_Name);

                while ($ar_Result = $hl_Data->Fetch()) {
                    array_push($result, $ar_Result[$uf_Name]);
                }
            }
        }

        return $result;
    }

    public static function Delete($tb_Name, $hl_Name, $uf_Name = null, $uf_Data = null)
    {
        $result = 0;

        if (CModule::IncludeModule('highloadblock')) {
            $ar_Data_Query = array(
                'select' => array('ID'),
                'filter' => array()
            );

            if (isset($uf_Name) && isset($uf_Data)) {
                $ar_Data_Query['filter'] = array("=$uf_Name" => $uf_Data);
            }

            $hl_Filter = array(
                'select' => array('ID', 'NAME'),
                'filter' => array('=NAME' => $hl_Name)
            );

            $hl_Block = \Bitrix\Highloadblock\HighloadBlockTable::getList($hl_Filter)->fetch();
            if (!empty($hl_Block)) {
                $hl_Block_Entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity(
                    array(
                        'ID' => $hl_Block['ID'],
                        'NAME' => $hl_Block['NAME'],
                        'TABLE_NAME' => $tb_Name
                    )
                );

                $hl_Block_Class = $hl_Block_Entity->getDataClass();
                $hl_Data = $hl_Block_Class::getList($ar_Data_Query);
                $hl_Data = new CDBResult($hl_Data, $tb_Name);

                while ($ar_Result = $hl_Data->Fetch()) {
                    $hl_Result = $hl_Block_Class::delete($ar_Result['ID']);

                    if ($hl_Result->isSuccess()) {
                        $result++;
                    } else {
                        $result = $hl_Result->getErrorMessages();
                        break;
                    }
                }
            }
        }

        return $result;
    }

    public static function IsExist($tb_Name, $hl_Name, $uf_Name, $id)
    {
        $result = false;

        if (CModule::IncludeModule('highloadblock')) {
            $ar_Data_Query = array(
                'select' => array($uf_Name),
                'filter' => array()
            );

            $hl_Filter = array(
                'select' => array('ID', 'NAME'),
                'filter' => array('=NAME' => $hl_Name)
            );

            $hl_Block = \Bitrix\Highloadblock\HighloadBlockTable::getList($hl_Filter)->fetch();
            if (!empty($hl_Block)) {
                $hl_Block_Entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity(
                    array(
                        'ID' => $hl_Block['ID'],
                        'NAME' => $hl_Block['NAME'],
                        'TABLE_NAME' => $tb_Name
                    )
                );

                $hl_Block_Class = $hl_Block_Entity->getDataClass();
                $hl_Data = $hl_Block_Class::getList($ar_Data_Query);
                $hl_Data = new CDBResult($hl_Data, $tb_Name);

                while ($ar_Result = $hl_Data->Fetch()) {
                    $data_Unserialized = unserialize($ar_Result[$uf_Name]);
                    if ($data_Unserialized !== false && $data_Unserialized['ID'] == $id) {
                        $result = true;
                        break;
                    }
                }
            }
        }

        return $result;
    }
}
