<?php

//function แปลงเป็นเลขไทย
function thainumDigit($num)
{
    return str_replace(
        array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'),
        array("o", "๑", "๒", "๓", "๔", "๕", "๖", "๗", "๘", "๙"),
        $num
    );
};
