<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InstagramController extends Controller
{
    public function getInstagramData(Request $request)
    {

        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        $postData = [
            "action" => "heatmap",
            "client" => [
                "client_id" => "R3ZUZ3VGUFRidkl2enpNZHNDT3B2UT09",
                "timezone" => "America/Sao_Paulo"
            ],
            "datainicial" => $startDate,
            "datafinal" => $endDate,
            "social_media" => [
                "instagram_business" => [
                    "secret" => "17841407685744108",
                    "token" => "EAAXgITdlkwABO62T2OhwjpjUTtsX0D8ZBlViqyxaubmq5yZB13fPjtzjVZAyo59lLVVytY7CYZCLmrvV67H9hsJrBs52gJZAV59CkS1Sxe6uIzSz07Fk7uZBVzVFiRjcN6DIzwOYmruDXs5zGONPNGj5k0af4oZBfbX1UAPdlAVgbKnOFbiFKHjUFkU97vJ2pqcOmqCC30I7c1L5lHZCMbwNZBA3diZB5ky7HZAkwxvB5iG2jI2H9xPFhjz"
                ]
            ]
        ];


        $response = Http::withHeaders([
            'Authorization' => 'Bearer HavU40LLYMSBEWvpOb3RjPs8lOD0YQjtLoACFBV0zcIjlIMC597giDh5MFszjew5OHuQT9IcWI8WMtzpHfHEwO6GSbU',
            'Content-Type' => 'application/json'
        ])->post('https://newreport.4et.us/api/report/heatmap/instagram', $postData);

        
        if ($response->successful()) {
            $instagramData = $response->json();
            return $instagramData;
        } else {

            return response()->json(['error' => 'Erro ao obter os dados do Instagram'], $response->status());
        }
    }
}
