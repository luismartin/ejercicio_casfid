<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    public static function calcular()
    {
        $url = 'https://www.covermanager.com/reservation/search_restaurant_groups_nobottom';
        $params = [
            'language' => 'spanish',
            'restaurant' => 'restaurante-voltereta-nuevo',
            'hour' => date('H:i'),
            'date' => date('d-m-Y'), //'16-02-2022',
            'people' => 2,
        ];
        $res = static::post($url, $params);
        return static::parse($res);
    }

    /**
     * Send a POST request without using PHP's curl functions.
     *
     * @param string $url The URL you are sending the POST request to.
     * @param array $postVars Associative array containing POST values.
     * @return string The output response.
     * @throws Exception If the request fails.
     */
    private static function post($url, $postVars = array())
    {
        //Transform our POST array into a URL-encoded query string.
        $postStr = http_build_query($postVars);
        //Create an $options array that can be passed into stream_context_create.
        $options = array(
            'http' =>
            array(
                'method'  => 'POST', //We are using the POST HTTP method.
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postStr //Our URL-encoded query string.
            )
        );
        //Pass our $options array into stream_context_create.
        //This will return a stream context resource.
        $streamContext  = stream_context_create($options);
        //Use PHP's file_get_contents function to carry out the request.
        //We pass the $streamContext variable in as a third parameter.
        $result = file_get_contents($url, false, $streamContext);
        //If $result is FALSE, then the request has failed.
        if ($result === false) {
            //If the request failed, throw an Exception containing
            //the error.
            $error = error_get_last();
            throw new \Exception('POST request failed: ' . $error['message']);
        }
        //If everything went OK, return the response.
        return $result;
    }

    private static function parse($resp)
    {
        $huecos = array();
        if (preg_match_all('/people=2&day=\d{4}-\d{2}-\d{2}&hour=\d{2}:\d{2}/', $resp, $coincidencias)) {
            foreach ($coincidencias[0] as $fila) {
                if (preg_match('/people=2&day=(\d{4}-\d{2}-\d{2})&hour=(\d{2}:\d{2})/', $fila, $matches)) {
                    $huecos[] = implode('/', array_reverse(explode('-', $matches[1]))) . ' ' . $matches[2];
                }
            }
        }
        return $huecos;
    }
}
