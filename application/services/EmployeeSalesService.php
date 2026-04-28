<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmployeeSalesService {
    private $ci;

    public function __construct() {
        $this->ci = &get_instance();
    }

    /**
     * Get sub-column letters by group label (handles merged columns)
     */
    public function getSubColumnLettersByGroupLabel($filePath, $groupLabel, $topRow = 1, $subRow = 2) {
        try {
            $objPHPExcel = PHPExcel_IOFactory::load($filePath);
            $sheet = $objPHPExcel->getActiveSheet();

            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

            // Find the center column of the merged group label
            $centerCol = null;
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $topValue = trim($sheet->getCellByColumnAndRow($col, $topRow)->getValue());
                if ($topValue === $groupLabel) {
                    $centerCol = $col;
                    break;
                }
            }

            if ($centerCol === null) {
                return array(); // Group label not found
            }

            $matchedColumns = array();

            // Expand left (up to 2 columns)
            for ($offset = 2; $offset >= 1; $offset--) {
                $col = $centerCol - $offset;
                if ($col < 0) {
                    continue;
                }

                $topValue = trim($sheet->getCellByColumnAndRow($col, $topRow)->getValue());
                $subValue = trim($sheet->getCellByColumnAndRow($col, $subRow)->getValue());

                // Column is part of merged group if top is empty and sub is not
                if ($topValue === '' && $subValue !== '') {
                    $matchedColumns[] = PHPExcel_Cell::stringFromColumnIndex($col);
                }
            }

            // Include center column if it has a sub-label
            $subCenter = trim($sheet->getCellByColumnAndRow($centerCol, $subRow)->getValue());
            if ($subCenter !== '') {
                $matchedColumns[] = PHPExcel_Cell::stringFromColumnIndex($centerCol);
            }

            // Expand right (up to 2 columns)
            for ($offset = 1; $offset <= 2; $offset++) {
                $col = $centerCol + $offset;
                if ($col >= $highestColumnIndex) {
                    break;
                }

                $topValue = trim($sheet->getCellByColumnAndRow($col, $topRow)->getValue());
                $subValue = trim($sheet->getCellByColumnAndRow($col, $subRow)->getValue());

                if ($topValue === '' && $subValue !== '') {
                    $matchedColumns[] = PHPExcel_Cell::stringFromColumnIndex($col);
                }
            }

            return $matchedColumns;
        } catch (Exception $e) {
            log_message('error', 'Error in getSubColumnLettersByGroupLabel: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * Get column groups by sub-label triggers (handles merged columns)
     */
    public function getGroupsBySubLabelTriggers($filePath, $topRow = 1, $subRow = 2) {
        try {
            $objPHPExcel = PHPExcel_IOFactory::load($filePath);
            $sheet = $objPHPExcel->getActiveSheet();

            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

            $groups = array();
            $currentGroupLabel = '';
            $currentGroup = array();

            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $subLabel = trim($sheet->getCellByColumnAndRow($col, $subRow)->getValue());

                if ($currentGroupLabel == '') {
                    $currentGroupLabel = trim($sheet->getCellByColumnAndRow($col, $topRow)->getValue());
                }

                // If we hit a new TGT trigger, start a new group
                if ($subLabel === 'TGT') {
                    if (!empty($currentGroup) && $currentGroupLabel !== '') {
                        $groups[$currentGroupLabel] = $currentGroup;
                    }
                    $currentGroupLabel = '';
                    $currentGroup = array();
                }

                // If it's a desired label and not already in the group, add it
                if ($subLabel !== '') {
                    $currentGroup[$subLabel] = PHPExcel_Cell::stringFromColumnIndex($col);
                }
            }

            // Add the final group if it has content
            if (!empty($currentGroup)) {
                $groups[$currentGroupLabel] = $currentGroup;
            }

            return $groups;
        } catch (Exception $e) {
            log_message('error', 'Error in getGroupsBySubLabelTriggers: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * Generate field map and reverse map from grouped data
     */
    public function generateFieldsMap($data) {
        $fieldsMap = [];
        foreach ($data as $top => $subs) {
            $fieldsMap[$this->formatColName($top)] = $top;
        }
        return [$fieldsMap, array_flip($fieldsMap)];
    }

    /**
     * Generate table structures from grouped data (handles merged columns)
     */
    public function generateTables($data) {
        $fieldsMap = $this->generateFieldsMap($data)[0];

        $table1 = [];
        $table2 = [];

        foreach ($data as $top => $subs) {
            $subs_length = count($subs);
            
            // Table 1: Sales with digital sales (5 columns)
            if ($subs_length == 5) {
                $table1[$fieldsMap[$this->formatColName($top)]] = [
                    'label' => $top,
                    'tgt' => $subs['TGT'],
                    'branch_sales' => $subs['Branch sales'] ?? null,
                    'digital_sales' => $subs['Digital Sales'] ?? null,
                    'total' => $subs['Total'] ?? null,
                    'percentage' => $subs['%'] ?? null
                ];
            } 
            // Table 2: Sales without digital (3 columns)
            else if ($subs_length == 3) {
                $table2[$fieldsMap[$this->formatColName($top)]] = [
                    'label' => $top,
                    'tgt' => $subs['TGT'] ?? null,
                    'sales' => $subs['Sales'] ?? null,
                    'percentage' => $subs['%'] ?? null
                ];
            }
        }

        return [$table1, $table2];
    }

    /**
     * Format column name (lowercase, remove spaces)
     */
    private function formatColName($name) {
        return strtolower(str_replace(' ', '', $name));
    }

    /**
     * Get numeric value from cell, handling various formats
     */
    public function getNumericValue($value) {
        if ($value === null || $value === '') {
            return 0;
        }
        
        if (is_numeric($value)) {
            return (float)$value;
        }

        // Remove currency symbols, thousand separators, spaces
        $cleanedValue = preg_replace('/[^\d.-]/', '', $value);

        return is_numeric($cleanedValue) ? (float)$cleanedValue : 0;
    }

    /**
     * Get cell value, handling formulas
     */
    public function getCellValue($sheet, $cell, $row) {
        if (!$sheet || !is_object($sheet)) {
            return 0;
        }

        try {
            $cellObj = $sheet->getCell($cell . $row);

            // Get calculated value if formula
            if ($cellObj->isFormula()) {
                try {
                    return $cellObj->getCalculatedValue();
                } catch (Exception $e) {
                    log_message('error', 'Error calculating formula for cell ' . $cell . $row . ': ' . $e->getMessage());
                    return 0;
                }
            }

            return $cellObj->getValue();
        } catch (Exception $e) {
            log_message('error', 'Error getting cell value ' . $cell . $row . ': ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Find employee by ID in sheet
     */
    public function findEmployeeByIdInSheet($sheet, $empId) {
        if (!$sheet || !$empId) {
            return null;
        }

        try {
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
        } catch (Exception $e) {
            log_message('error', 'Error in findEmployeeByIdInSheet: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract field data for Table 1 or Table 2
     */
    public function extractFieldData($sheet, $row, $fieldKey, $tableType, $tables) {
        if ($tableType === 'table1') {
            return $this->extractTable1FieldData($sheet, $row, $fieldKey, $tables[0]);
        } else {
            return $this->extractTable2FieldData($sheet, $row, $fieldKey, $tables[1]);
        }
    }

    /**
     * Extract Table 1 field data (with digital sales)
     */
    public function extractTable1FieldData($sheet, $row, $fieldKey, $tableItems) {
        if (!isset($tableItems[$fieldKey])) {
            return null;
        }

        try {
            $itemDetails = $tableItems[$fieldKey];

            // Check for null values in item details
            if (in_array(null, array_values($itemDetails))) {
                return null;
            }

            $tgt = $this->getNumericValue($this->getCellValue($sheet, $itemDetails['tgt'], $row));
            $branchSales = $this->getNumericValue($this->getCellValue($sheet, $itemDetails['branch_sales'], $row));
            $digitalSales = $this->getNumericValue($this->getCellValue($sheet, $itemDetails['digital_sales'], $row));

            $total = $branchSales + $digitalSales;
            $remaining = $tgt - $total;

            $percentage = 0;
            if ($tgt > 0) {
                $percentage = round(($total / $tgt) * 100, 0);
            } elseif ($total > 0) {
                $percentage = 100;
            }

            return [
                'tgt' => $tgt,
                'branch_sales' => $branchSales,
                'digital_sales' => $digitalSales,
                'total' => $total,
                'remaining' => $remaining,
                'percentage' => $percentage . '%'
            ];
        } catch (Exception $e) {
            log_message('error', 'Error in extractTable1FieldData: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract Table 2 field data (without digital sales)
     */
    public function extractTable2FieldData($sheet, $row, $fieldKey, $tableItems) {
        if (!isset($tableItems[$fieldKey])) {
            return null;
        }

        try {
            $itemDetails = $tableItems[$fieldKey];

            // Check for null values in item details
            if (in_array(null, array_values($itemDetails))) {
                return null;
            }

            $tgt = $this->getNumericValue($this->getCellValue($sheet, $itemDetails['tgt'], $row));
            $sales = $this->getNumericValue($this->getCellValue($sheet, $itemDetails['sales'], $row));

            $remaining = $tgt - $sales;

            $percentage = 0;
            if ($tgt > 0) {
                $percentage = round(($sales / $tgt) * 100, 0);
            } elseif ($sales > 0) {
                $percentage = 100;
            }

            return [
                'tgt' => $tgt,
                'sales' => $sales,
                'remaining' => $remaining,
                'percentage' => $percentage . '%'
            ];
        } catch (Exception $e) {
            log_message('error', 'Error in extractTable2FieldData: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Calculate totals from subordinates data
     */
    public function calculateTotals($subordinates, $tableType) {
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
}
