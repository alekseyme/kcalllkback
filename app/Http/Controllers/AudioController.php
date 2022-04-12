<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AudioController extends Controller
{
    public function da($rid)
    {
        try {
            $username = "operator-test3";
            $password = "ed2qJac2ca0x";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://31.210.220.111/callcenter/visor/json/GetMedia3?requestId='.$rid.'&start=2021-01-01%2000:00:00');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
            curl_setopt($ch, CURLOPT_USERPWD, $username.":".$password);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close ($ch);

            $headers = [
                'Content-type' => 'audio/mpeg',
                'Content-Disposition' => 'attachment; filename=audio.mp3',
            ];

            return response()->streamDownload(function () use ($response) {
                echo $response;
            }, 'audio.mp3', $headers);

        } catch (\Exception $e) {
            echo $e;
        }
    }
}
