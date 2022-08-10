<?php
/**
 * Creation Date: 21/06/2021
 * Author: Labeeb Ahmad
 * Description: Wrapper class around google sheets api.
 */
namespace App\Services;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_Spreadsheet;
use Google_Service_Sheets_ValueRange;
use Google_Service_Sheets_BatchUpdateSpreadsheetRequest;

class GoogleSheetService
{
    private $spreadSheetId;

    private $client;

    public $googleSheetService;

    /**
     * Selected TabName
     * @param $tabName
     */
    private $tabName = NULL;

    /**
     *
     *
     * @return void
     * @throws Exception
     */
    public function __construct()
    {
        $this->spreadSheetId = config('googlesheet.spreadsheet_id');
        $this->client = new Google_Client();
        $googleServiceAccountConfigPath = base_path() . config('googlesheet.service_account_file_path');
        if (!file_exists($googleServiceAccountConfigPath)) {
            throw new \Exception('Google Service Account Config Not Found');
        }
        $this->client->setAuthConfig($googleServiceAccountConfigPath);
        $this->client->addScope("https://www.googleapis.com/auth/spreadsheets");
        $this->googleSheetService = new Google_Service_Sheets($this->client);
    }

    /**
     * @return array
     */
    public function readGoogleSheet()
    {

        $dimensions = $this->getDimensions($this->spreadSheetId);
        $range = $this->getTabName() . '!A1:' . $dimensions['colCount'];

        $data = $this->googleSheetService
            ->spreadsheets_values
            ->batchGet($this->spreadSheetId, ['ranges' => $range]);

        return $data->getValueRanges()[0]->values;
    }

    /**
     * @return Google_Service_Sheets_Spreadsheet
     */
    public function getSheets()
    {

        $data = $this->googleSheetService
            ->spreadsheets
            ->get($this->spreadSheetId);

        return $data;
    }

    /**
     * @return array
     */
    public function getSheetsTitles(Google_Service_Sheets_Spreadsheet $sheets)
    {
        $titles = [];
        foreach ($sheets as $sheet) {
            $titles[] = $sheet->getProperties()->title;
        }
        $output = array();
        foreach($titles as $month) {
            $m = date_parse($month);
            $output[$m['month']] = $month;
        }
        ksort($output);
        return $output;
    }


    /**
     * @param $tabName string
     * @return Boolean
     */
    public function doesTabExist($tabName)
    {
        try {
            $sheets_info = $this->googleSheetService->spreadsheets->get($this->spreadSheetId);
            $sheetsArr = $sheets_info['sheets'];
            foreach ($sheetsArr as $sheet) {
                if ($sheet->properties && $sheet->properties->title == $tabName) {
                    return true;
                }
            }
            return false;
        } catch (\Throwable $e) {
            Log::error('Service: GoogleSheetService doesTabExist error: ' . $e->getMessage());
            throw new BaseException($e);
        }
    }

    /**
     * @param $tabName string
     * @return Google\Service\Sheets\BatchUpdateSpreadsheetResponse object
     */
    public function addNewSheet($tabName)
    {
        try {
            $body = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest(array(
                'requests' => array(
                    'addSheet' => array(
                        'properties' => array(
                            'title' => $tabName
                        )
                    )
                )
            ));
            return $this->googleSheetService->spreadsheets->batchUpdate($this->spreadSheetId ,$body);
        } catch (\Throwable $e) {
            Log::error('Service: GoogleSheetService addNewSheet error: ' . $e->getMessage());
            throw new BaseException($e);
        }
    }

    /**
     * @param $tabName
     */
    public function setTabName($tabName)
    {
        $this->tabName = $tabName;
    }

    /**
     * @return string
     */
    private function getTabName()
    {
        return $this->tabName;
    }

    /**
     * @return Google\Service\Sheets\UpdateValuesResponse object
     */
    public function saveDataToSheet(array $data)
    {
        $dimensions = $this->getDimensions($this->spreadSheetId);

        $body = new Google_Service_Sheets_ValueRange([
            'values' => $data
        ]);

        $params = [
            'valueInputOption' => 'USER_ENTERED',
        ];

        $range = $this->getTabName() . "!A" . ($dimensions['rowCount'] + 1);

        return $this->googleSheetService
            ->spreadsheets_values
            ->append($this->spreadSheetId, $range, $body, $params);
    }

    /**
     * @return array
     */
    private function getDimensions($spreadSheetId)
    {
        $rowDimensions = $this->googleSheetService->spreadsheets_values->batchGet(
            $spreadSheetId,
            ['ranges' => $this->getTabName() . '!A:A', 'majorDimension' => 'COLUMNS']
        );


        //if data is present at nth row, it will return array till nth row
        //if all column values are empty, it returns null
        $rowMeta = $rowDimensions->getValueRanges()[0]->values;

        $dimensions = [
            'error' => false,
            'rowCount' => 0,
            'colCount' => 'G'
        ];

        if (!$rowMeta) {
            return $dimensions;
        }

        $colDimensions = $this->googleSheetService->spreadsheets_values->batchGet(
            $spreadSheetId,
            ['ranges' => $this->getTabName() . '!1:1', 'majorDimension' => 'ROWS']
        );

        //if data is present at nth col, it will return array till nth col
        //if all column values are empty, it returns null
        $colMeta = $colDimensions->getValueRanges()[0]->values;
        if (!$colMeta) {
            return $dimensions;
        }

        return [
            'error' => false,
            'rowCount' => count($rowMeta[0]),
            'colCount' => $this->colLengthToColumnAddress(count($colMeta[0]))
        ];
    }

    /**
     * @return mixed
     */
    private function colLengthToColumnAddress($number)
    {
        if ($number <= 0) {
            return null;
        }

        $letter = '';
        while ($number > 0) {
            $temp = ($number - 1) % 26;
            $letter = chr($temp + 65) . $letter;
            $number = ($number - $temp - 1) / 26;
        }
        return $letter;
    }
}