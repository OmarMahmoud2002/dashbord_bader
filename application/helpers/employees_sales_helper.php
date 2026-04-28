<?php

function calculateTotals($subordinates, $tableType) {
    if (empty($subordinates)) {
        return null;
    }

    $totals = [
        'tgt' => 0,
        'remaining' => 0
    ];

    if ($tableType === 'table1') {
        $totals['branch_sales'] = 0;
        $totals['digital_sales'] = 0;
        $totals['total'] = 0;

        foreach ($subordinates as $sub) {
            $totals['tgt'] += isset($sub['tgt']) ? $sub['tgt'] : 0;
            $totals['branch_sales'] += isset($sub['branch_sales']) ? $sub['branch_sales'] : 0;
            $totals['digital_sales'] += isset($sub['digital_sales']) ? $sub['digital_sales'] : 0;
            $totals['total'] += isset($sub['total']) ? $sub['total'] : 0;
            $totals['remaining'] += isset($sub['remaining']) ? $sub['remaining'] : 0;
        }

        $percentage = $totals['tgt'] > 0 
            ? round(($totals['total'] / $totals['tgt']) * 100, 0)
            : ($totals['total'] > 0 ? 100 : 0);
        
        $totals['percentage'] = $percentage . '%';

    } else {
        $totals['sales'] = 0;

        foreach ($subordinates as $sub) {
            $totals['tgt'] += isset($sub['tgt']) ? $sub['tgt'] : 0;
            $totals['sales'] += isset($sub['sales']) ? $sub['sales'] : 0;
            $totals['remaining'] += isset($sub['remaining']) ? $sub['remaining'] : 0;
        }

        $percentage = $totals['tgt'] > 0 
            ? round(($totals['sales'] / $totals['tgt']) * 100, 0)
            : ($totals['sales'] > 0 ? 100 : 0);
        
        $totals['percentage'] = $percentage . '%';
    }

    return $totals;
}

function findEmployeeByIdInSheet($sheet, $empId) {
    $highestRow = $sheet->getHighestDataRow();
    $firstDataRow = 2;

    for ($row = $firstDataRow; $row <= $highestRow; $row++) {
        $currentEmpId = trim((string)$sheet->getCell('A' . $row)->getValue());

        if ($currentEmpId === $empId) {
            return [
                'name' => trim((string)$sheet->getCell('D' . $row)->getValue()),
                'position' => trim((string)$sheet->getCell('E' . $row)->getValue()),
                'emp_id' => $currentEmpId,
                'row' => $row
            ];
        }
    }

    return null;
}

?>