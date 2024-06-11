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
                "client_id" => "SnNPOEJVNHZtOVJvVmRLSTEvaUpkdz09",
                "timezone" => "America/Sao_Paulo"
            ],
            "datainicial" => $startDate, 
            "datafinal" => $endDate, 
            "social_media" => [
                "instagram_business" => [
                    "secret" => "17841400143028788",
                    "token" => "EAAXgITdlkwABAJqXhU5nYtruzZAfAv156jOPZArWZAXmWrusvD0VML87lryNTuZBqZBgjeEAxIfL4Kf1ZBDk3uDtfYGTYdiOL3YbZBR4wJoc7HM33Yychl2M4b8H2ek3GvMuOMaZB6OgmE4sH439XJdTPPI7KMWaCr4Bdj0QouAZARfujbwnYjIpkN9Cu9HLF4wePWqQZCnnakSCMDxnedEEm6kBh270DvCdT6lrZAMRfXtsfUFY7A0Vp7b"
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
