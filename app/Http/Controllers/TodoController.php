<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use File;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        //$content = File::get('bakuun/storage/sample.xml');
        $content = '<OTA_HotelInvCountNotifRQ Version="1.0" TimeStamp="2017-01-01T00:00:00-
        04:00" EchoToken="CERT-INVCOUNT-UPDATE"
        xmlns="http://www.opentravel.org/OTA/2003/05">
         <POS>
         <Source>
         <BookingChannel Type="7">
         <CompanyName Code="HTL15">My Hotel Name</CompanyName>
         </BookingChannel>
         </Source>
         </POS>
         <Inventories HotelCode="HTL15.3">
         <Inventory>
         <StatusApplicationControl Start="2021-12-06" End="2022-09-06"
        InvTypeCode="R15.3.1" RatePlanCode="R15.3.1.1" />
         <InvCounts>
         <InvCount Count="12" CountType="2" />
         </InvCounts>
         </Inventory>
         <Inventory>
         <StatusApplicationControl Start="2021-12-08" End="2022-09-06"
        InvTypeCode="R15.3.2" RatePlanCode="R15.3.2.1" />
         <InvCounts>
         <InvCount Count="13" CountType="3" />
         </InvCounts>
         </Inventory>
         </Inventories>
        </OTA_HotelInvCountNotifRQ>';
        $xmlString = file_get_contents(public_path('sample.xml'));
        $xmlObject = simplexml_load_string($xmlString);
        
        $CompanyName = $xmlObject->POS->Source->BookingChannel->CompanyName;
        $CompanyType = $xmlObject->POS->Source->BookingChannel->attributes()->Type;
        $CompanyCode = $xmlObject->POS->Source->BookingChannel->CompanyName->attributes()->Code;
        
        $Inventories = $xmlObject->Inventories->Inventory;
        $hotelDet = "{'CompanyName':$CompanyName,'CompanyType':$CompanyType,'CompanyCode':$CompanyCode,'Inventories':";

        $Hotel = Todo::create([
            'CompanyName' => $CompanyName,
            'CompanyType' => $CompanyType,
            'CompanyCode' => $CompanyCode
        ]);
        $HotelId = $Hotel->id;
        $InventoryData = [];
        $count = count($Inventories);
        $hotelDet .= "[";
        for($i = 0; $i<$count; $i++)
        {
            $StatusApplicationControl = $Inventories[$i]->StatusApplicationControl;
            $Start = $StatusApplicationControl->attributes()->Start;
            $End = $StatusApplicationControl->attributes()->End;
            $InvTypeCode = $StatusApplicationControl->attributes()->InvTypeCode;
            $RatePlanCode = $StatusApplicationControl->attributes()->RatePlanCode;

            $Rooms = Todo::create([
                'Start' => $Start,
                'End' => $End,
                'InvTypeCode' => $InvTypeCode,
                'RatePlanCode' => $RatePlanCode,
                'ParentId' => $HotelId
            ]);
            $hotelDet .= "{'Start':$Start,'End':$End,'InvTypeCode':$InvTypeCode,'RatePlanCode':$RatePlanCode,'InvCounts':";
            $RoomlId = $Rooms->id;
            $InvCounts = $Inventories[$i]->InvCounts->InvCount;
            $InvCountCnt = count($InvCounts);
            $hotelDet .= "[";
            $InvCountsJson = '';
            for($j = 0; $j<$InvCountCnt; $j++)
            {
                $InvCount = $InvCounts->attributes()->Count;
                $CountType = $InvCounts->attributes()->CountType;
                $InvCountsJson = "{'InvCount:$InvCount,'CountType':$CountType}";
                $hotelDet .= $InvCountsJson;
                $Rooms = Todo::create([
                    'InvCount' => $InvCount,
                    'CountType' => $CountType,
                    'ParentId' => $RoomlId
                ]);
            }
            $hotelDet .= "]";
            $hotelDet .= "}";
        }
        $hotelDet .= "]";
        $hotelDet .= "}";
        echo $hotelDet;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function show(Todo $todo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function edit(Todo $todo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Todo $todo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Todo $todo)
    {
        //
    }
}
