<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SiteController extends Controller
{

    public function __construct()
    {

    }

    /**
     * @name getAsteroidData
     */
    public function getAsteroidData(Request $request)
    {
        $inputs = $request->all();

        $validator = \Validator::make($request->all(), [
            'start_date' => 'required',
            'end_date'   => 'required',
        ]);

        if ($validator->fails()) {
            $result = [
                'status'  => 'validation',
                'message' => 'Please select start date and end date.',
            ];

            return response()->json($result);
        }

        $startDate = date("Y-m-d", strtotime($inputs['start_date']));
        $endDate   = date("Y-m-d", strtotime($inputs['end_date']));

        $params = [
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'detailed'   => true,
            'api_key'    => 'DEMO_KEY',
        ];

        $response = Http::get("https://api.nasa.gov/neo/rest/v1/feed", $params);
        $response = json_decode($response->body());

        if (!empty($response) && !empty($response->near_earth_objects)) {

            $asteroidData = [];
            $allAsteroid  = [];
            $neoDiameter  = [];
            $neoVelocity  = [];
            $neoDistance  = [];

            foreach ($response->near_earth_objects as $date => $neos) {
                $asteroidData[$date] = count($neos);

                foreach ($neos as $neo) {
                    // $allAsteroid[$neo->id] = $neo;

                    if (!empty($neo->estimated_diameter) && isset($neo->estimated_diameter->kilometers)) {
                        $size = (($neo->estimated_diameter->kilometers->estimated_diameter_min + $neo->estimated_diameter->kilometers->estimated_diameter_max) / 2);

                        $neoDiameter[$neo->id] = $size;
                    }

                    foreach ($neo->close_approach_data as $specification) {

                        if (!empty($specification->relative_velocity) && isset($specification->relative_velocity->kilometers_per_hour)) {
                            $neoVelocity[$neo->id] = $specification->relative_velocity->kilometers_per_hour;
                        }

                        if (!empty($specification->miss_distance) && isset($specification->miss_distance->kilometers)) {
                            $neoDistance[$neo->id] = $specification->miss_distance->kilometers;
                        }
                    }
                }
            }
            ksort($asteroidData);

            arsort($neoVelocity);
            $fastestAseroidId = array_key_first($neoVelocity);
            $fastestAseroid   = [
                'id'    => $fastestAseroidId,
                'speed' => round($neoVelocity[$fastestAseroidId], 3),
            ];

            asort($neoDistance);
            $closestAseroidId = array_key_first($neoDistance);
            $closestAseroid   = [
                'id'    => $closestAseroidId,
                'speed' => round($neoDistance[$closestAseroidId], 3),
            ];

            if ($response->element_count > 0) {
                $average = array_sum($neoDiameter) / $response->element_count;
            }

            $result = [
                'status'  => 200,
                'data'    => [
                    'fastest_asteroid'  => $fastestAseroid,
                    'closest_asteroid'  => $closestAseroid,
                    'average_asteroids' => round($average, 3),
                    'chart_data'        => [
                        'labels' => array_keys($asteroidData),
                        'data'   => array_values($asteroidData),
                    ],
                ],
                'message' => "Get asteroidData successfully!",
            ];
        } else {
            $result = [
                'status'  => 500,
                'data'    => [],
                'message' => "No data available",
            ];
        }

        return response()->json($result);
    }
}
