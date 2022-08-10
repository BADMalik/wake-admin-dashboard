<?php

namespace App\Http\Controllers;

use App\Services\GoogleSheetService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sheet = new GoogleSheetService();

        $sheets = $sheet->getSheets();

        $sheets = $sheet->getSheetsTitles($sheets);

        $current = end($sheets);

        $sheet->setTabName($current);

        $rows = $sheet->readGoogleSheet();

        $headings = array_shift($rows);

        return view('reports.view', compact('rows', 'headings', 'sheets', 'current'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($month)
    {
        $sheet = new GoogleSheetService();

        $sheet->setTabName($month);

        $rows = $sheet->readGoogleSheet();

        $headings = array_shift($rows);

        return [ 'data' => compact('rows', 'headings') ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
