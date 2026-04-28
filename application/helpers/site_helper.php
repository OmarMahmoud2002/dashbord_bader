<?php
function secure_data($str) 
{
    $output_value = htmlspecialchars($str);
    $output_value = str_replace('&amp;lt;', '&lt;', $output_value);
    $output_value = str_replace('&amp;gt;', '&gt;', $output_value);
    return $output_value;
}

function safe_data($str)
{
    $output_value = htmlspecialchars_decode((string)$str);
    return $output_value;
}

function notifications($ci) {
    $notifications = $ci->Notifications->get_notifications(array('user_id' => $ci->session->userdata('user_id')));
    $ci->data['notifications'] = array_reverse($notifications);
    $ci->data['notifications_count'] = $ci->Notifications->get_notifications_length(
        array(
            'status' => 'unread', 
            'user_id' => $ci->session->userdata('user_id'),
        )
    );
}

function getCellValue($sheet, $cell, $row) {
    $cellObj = $sheet->getCell($cell . $row);
    
    // الحصول على القيمة المحسوبة إذا كانت الخلية تحتوي على صيغة
    if ($cellObj->isFormula()) {
        try {
            return $cellObj->getCalculatedValue();
        } catch (\Exception $e) {
            log_message('error', 'Error calculating formula for cell ' . $cell . $row . ': ' . $e->getMessage());
            return 0;
        }
    }
    
    return $cellObj->getValue();
}

function getNumericValue($value) {
    if ($value === null || $value === '') return 0;
    if (is_numeric($value)) return (float)$value;
    
    // إزالة أي رموز عملة أو فواصل آلاف أو مسافات
    $cleanedValue = preg_replace('/[^\d.-]/', '', $value);
    
    return is_numeric($cleanedValue) ? (float)$cleanedValue : 0;
}

function formatColName($name) {
    return strtolower(str_replace(' ', "", $name));
}

function shortenString($string, $length) {
    return strlen($string) > $length ? substr($string, 0, $length) . '...' : $string;
}