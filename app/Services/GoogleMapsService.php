<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleMapsService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.google_maps.api_key');
    }

    public function getOptimizedRoute($origin, $destination, &$waypoints)
    {
        $url = 'https://routes.googleapis.com/directions/v2:computeRoutes';

        $origin = $this->getLongLattFromAddress($this->getMainUrl($origin));
        $destination = $this->getLongLattFromAddress($this->getMainUrl($destination));
        $waypoints = array_map(function ($waypoint) {
            try {
                return $this->getLongLattFromAddress($this->getMainUrl($waypoint));
            } catch (\Exception $e) {
                Log::info('Error');
                Log::info($e);
                Log::info('Error end');
                return null;
            }
        }, $waypoints);

        foreach ($waypoints as $key => $waypoint) {
            if ($waypoint == null) {
                unset($waypoints[$key]);
            }
        }

        $waypoints = array_values($waypoints);

        $payload = [
            'origin' => [
                'location' => [
                    'latLng' => $origin,
                ],
            ],
            'destination' => [
                'location' => [
                    'latLng' => $destination,
                ],
            ],
            'intermediates' => array_map(function ($waypoint) {
                return ['location' => ['latLng' => $waypoint]];
            }, $waypoints),
            'travelMode' => 'DRIVE',
            'routingPreference' => 'TRAFFIC_AWARE',
            'optimizeWaypointOrder' => true,
        ];

        Log::info('Route payload');
        Log::info($payload);
        Log::info('Route payload end');

        Log::info('API Key');
        Log::info($this->apiKey);
        Log::info('API Key end');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Goog-Api-Key' => $this->apiKey,
            'Referer' => 'genuine.msquare.app',
            'X-Goog-FieldMask' => 'routes.optimized_intermediate_waypoint_index',
        ])->post($url, $payload);

        if ($response->successful() && isset($response->json()['routes'][0]['optimizedIntermediateWaypointIndex'])) {
            return $response->json()['routes'][0]['optimizedIntermediateWaypointIndex'];
        } else {
            throw new \Exception($response->json()['error']['message'], $response->json()['error']['code']);
        }
    }



    private function getLongLattFromAddress($url, $try = true)
    {
        ///url example = https://www.google.com/maps/place/29%C2%B059'17.9%22N+31%C2%B025'28.1%22E/@29.9883156,31.4219017,17z/data=!3m1!4b1!4m4!3m3!8m2!3d29.9883156!4d31.4244766?entry=ttu&g_ep=EgoyMDI1MDIyNi4xIKXMDSoASAFQAw%3D%3D

        ///url example2 = https://www.google.com/maps/place/30.060106,31.494161/data=!4m6!3m5!1s0!7e2!8m2!3d30.060105999999998!4d31.494161?utm_source=mstt_1&entry=gps&coh=192189&g_ep=CAESBjI1LjcuMhgAIJ6dCipjLDk0MjIzMjk5LDk0MjE2NDEzLDk0MjEyNDk2LDk0MjA3Mzk0LDk0MjA3NTA2LDk0MjA4NTA2LDk0MjE3NTIzLDk0MjE4NjUzLDk0MjI5ODM5LDQ3MDg0MzkzLDk0MjEzMjAwQgJFRw%3D%3D  

        /// url example3 = https://www.google.com/maps/search/29.969535,%2B31.482114%3Fentry%3Dtts%26g_ep%3DEgoyMDI1MDIyNi4xIPu8ASoASAFQAw%253D%253D&q=EgTFNHSPGMLil74GIjAlki6tC9wrCCiUXx7i4KCowVhYoZsAfR9iDMGV667E2ywXsZbn_fRTOBXOjIxQwl4yAXJaAUM

        if (is_array($url)) {
            $url = $url[0];
        }


        Log::info('URL');
        Log::info($url);
        Log::info('URL end');

        // Extract coordinates from URL
        $coordinates = [];

        // Try to match coordinates in format lat,lng directly
        if (preg_match('/place\/([\d.-]+),([\d.-]+)/', $url, $matches) || preg_match('/search\/([\d.-]+),\s*%2B?([\d.-]+)/', $url, $matches)) {
            $coordinates = [
                'latitude' => (float)$matches[1],
                'longitude' => (float)$matches[2]
            ];
        }
        // Try to match coordinates in DMS format
        else if (preg_match('/place\/([\d.]+)%C2%B0([\d.]+)\'([\d.]+)%22([NS]).*?([\d.]+)%C2%B0([\d.]+)\'([\d.]+)%22([EW])/', $url, $matches)) {
            // Convert DMS to decimal degrees
            $latitude = $matches[1] + ($matches[2] / 60) + ($matches[3] / 3600);
            $latitude = ($matches[4] === 'S') ? -$latitude : $latitude;

            $longitude = $matches[5] + ($matches[6] / 60) + ($matches[7] / 3600);
            $longitude = ($matches[8] === 'W') ? -$longitude : $longitude;

            $coordinates = [
                'latitude' => $latitude,
                'longitude' => $longitude
            ];
        }
        // Try to extract from URL parameters as fallback
        else {
            preg_match('/[!&]3d(-?\d+\.?\d*).*?[!&]4d(-?\d+\.?\d*)/', $url, $matches);
            if (count($matches) === 3) {
                $coordinates = [
                    'latitude' => (float)$matches[1],
                    'longitude' => (float)$matches[2]
                ];
            }
        }

        if (empty($coordinates) && $try) {
            // If no coordinates found and this is first try, follow redirect and try again
            return $this->getLongLattFromAddress($this->getMainUrl($url), false);
        }

        if (empty($coordinates)) {
            throw new \Exception("Could not extract coordinates from URL: " . $url);
        }

        Log::info('Coordinates');
        Log::info($coordinates);
        Log::info('Coordinates end');

        return [
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude']
        ];
    }

    function getMainUrl($url)
    {
        $headers = get_headers($url, 1);
        if (isset($headers['Location'])) {
            return $headers['Location'];
        }
        return $url;
    }
}
