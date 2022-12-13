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
        
        $hotelDet['CompanyName'] = $xmlObject->POS->Source->BookingChannel->CompanyName;
        $hotelDet['CompanyType'] = $xmlObject->POS->Source->BookingChannel->attributes()->Type;
        $hotelDet['CompanyCode'] = $xmlObject->POS->Source->BookingChannel->CompanyName->attributes()->Code;
        
        $Inventories = $xmlObject->Inventories->Inventory;

        $Hotel = Todo::create([
            'CompanyName' => $hotelDet['CompanyName'],
            'CompanyType' => $hotelDet['CompanyType'],
            'CompanyCode' => $hotelDet['CompanyCode']
        ]);
        $HotelId = $Hotel->id;
        $InventoryData = [];
        $count = count($Inventories);
        for($i = 0; $i<$count; $i++)
        {
            $StatusApplicationControl = $Inventories[$i]->StatusApplicationControl;
            $RoomDet[$i]['Start'] = $StatusApplicationControl->attributes()->Start;
            $RoomDet[$i]['End'] = $StatusApplicationControl->attributes()->End;
            $RoomDet[$i]['InvTypeCode'] = $StatusApplicationControl->attributes()->InvTypeCode;
            $RoomDet[$i]['RatePlanCode'] = $StatusApplicationControl->attributes()->RatePlanCode;

            $Rooms = Todo::create([
                'Start' => $RoomDet[$i]['Start'],
                'End' => $RoomDet[$i]['End'],
                'InvTypeCode' => $RoomDet[$i]['InvTypeCode'],
                'RatePlanCode' => $RoomDet[$i]['RatePlanCode'],
                'ParentId' => $HotelId
            ]);
            $RoomlId = $Rooms->id;
            $InvCounts = $Inventories[$i]->InvCounts->InvCount;
            $InvCountCnt = count($InvCounts);
            $InvCountsJson = '';
            for($j = 0; $j<$InvCountCnt; $j++)
            {
                $InvDet[$i][$j]['InvCount'] = $InvCounts->attributes()->Count;
                $InvDet[$i][$j]['CountType'] = $InvCounts->attributes()->CountType;
                $Rooms = Todo::create([
                    'InvCount' => $InvDet[$i][$j]['InvCount'],
                    'CountType' => $InvDet[$i][$j]['CountType'],
                    'ParentId' => $RoomlId
                ]);
                $RoomDet[$i]['InvDet'][$j] = $InvDet[$i][$j];
            }
        }
        $hotelDet['Inventory'] = $RoomDet;
        $RoomData = '';
        $RoomData .= '[';
        $roomCount = count($hotelDet['Inventory']);
        foreach($hotelDet['Inventory'] as $key => $RoomDetRow)
        {
            
            $InvData = '[';
            $InvDataCount = count($RoomDetRow['InvDet']);
            foreach($RoomDetRow['InvDet'] as $InvDetkey => $InvDetRow)
            {
                $InvData .= "{
                    'InvCount' : '".$InvDetRow['InvCount']."',
                    'CountType' : '".$InvDetRow['CountType']."'
                }";
                if($InvDetkey < $InvDataCount-1)
                {
                    $InvData .= ',';
                }
            }
            $InvData .= ']';
            $RoomData .= "{
                'Start' : '".$RoomDetRow['Start']."',
                'End' : '".$RoomDetRow['End']."',
                'InvTypeCode' : '".$RoomDetRow['InvTypeCode']."',
                'RatePlanCode' : '".$RoomDetRow['RatePlanCode']."',
                'InvCounts' : $InvData
            }";
            if($key < $roomCount-1)
            {
                $RoomData .= ',';
            }
        }
        $RoomData .= ']';

        $HotelData = "{
                    'CompanyName': '".$hotelDet['CompanyName']."',
                    'CompanyType': '".$hotelDet['CompanyType']."',
                    'CompanyCode': '".$hotelDet['CompanyCode']."',
                    'Inventories' : $RoomData
                }";
        
        dd($HotelData);
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
