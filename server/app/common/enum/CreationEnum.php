<?php
namespace app\common\enum;
class CreationEnum
{

    /**
     * @notes
     * @param $form
     * @return array|string|string[]
     * @author cjhao
     * @date 2024/6/24 11:45
     */
    public static function getCreationTypeDesc($form = true)
    {
        $desc = [
            1=>'-',
            2=>'扩写',
            3=>'简写',
            4=>'续写',
            5=>'改写-正式得体',
            6=>'改写-严肃庄重',
            7=>'改写-轻松',
            8=>'改写-热情',
        ];
        if(true === $form){
            return $desc;
        }
        return $desc[$form] ?? '-';
    }
    /**
     * @notes
     * @param $form
     * @return array|string|string[]
     * @author cjhao
     * @date 2024/6/24 11:45
     */
    public static function getCreationDesc($form = true)
    {
        $desc = [
            2=>'请帮我将下面这段文字进行扩写，要求不改变原本的文意: ',
            3=>'请帮我将下面这段文字进行简写，要求不改变原本的文意: ',
            4=>'请帮我将下面这段文字进行续写，要求不改变原本的文意: ',
            5=>'请帮我将下面这段文字用【正式得体】的语气进行改写，要求不改变原本的文意: ',
            6=>'请帮我将下面这段文字用【严肃庄重】的语气进行改写，要求不改变原本的文意: ',
            7=>'请帮我将下面这段文字用【轻松】的语气进行改写，要求不改变原本的文意: ',
            8=>'请帮我将下面这段文字用【热情】的语气进行改写，要求不改变原本的文意: ',
        ];
        if(true === $form){
            return $desc;
        }
        return $desc[$form] ?? '-';
    }
}