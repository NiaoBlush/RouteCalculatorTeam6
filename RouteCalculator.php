<?php
/**
 * Author: Yue Yang
 * Date: 2019/2/25
 * Time: 19:56
 */

class RouteCalculator
{
    private static $api_key = "Put Google Map API Key here";

    private $start;
    private $addresses;
    private $distances;


    /**
     * RouteCalculator constructor.
     * @param $s string start point (address of the warehouse)
     * @param array $add addresses
     */
    public function __construct($s, array $add)
    {
        $this->start = str_replace(" ", "+", $s);
        $this->addresses = $add;
        for ($i = 0; $i < count($this->addresses); $i++) {
            $formatted = str_replace(" ", "+", $this->addresses[$i]);
            $this->addresses[$i] = $formatted;
        }
        $this->distances = array();

        //populate the distances array via google api
        $this->calculateDistances();
    }

    public function getDistances()
    {
        return $this->distances;
    }

    /**
     * calculate the distances between every nodes via Google Map Api
     */
    private function calculateDistances()
    {
        $origins = $this->start;
        $nodes = $this->start . "|";
        for ($i = 0; $i < count($this->addresses); $i++) {
            $nodes .= $this->addresses[$i];
            if ($i != count($this->addresses) - 1) {
                $nodes .= "|";
            }
        }
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?"
            . "origins=" . $nodes
            . "&destinations=" . $nodes
            . "&key=" . self::$api_key
            . "&mode=driving";
        //$html = file_get_contents($url);
        $html = file_get_contents("SampleResult.json");
        $html = json_decode($html);

        $status = $html->{"status"};

        //populate distances array
        if ($status == "OK") {
            //echo print_r($html);
            $rows = $html->{"rows"};
            //echo print_r($rows);
            for ($i = 0; $i < count($rows); $i++) {
                $cols = $rows[$i]->{"elements"};
                //echo print_r($cols);
                for ($j = 0; $j < count($cols); $j++) {
                    $distance = $cols[$j]->{"distance"}->{"value"};
                    //echo $distance . "\n";
                    $this->distances[$i][$j] = $distance;
                }
            }
        }
    }

    /**
     * exhaustion
     * @return null
     */
    public function calculateRoute()
    {
        //check size of distances array
        //at least one origin and one destination, so count()>1
        if (count($this->distances) > 1) {
            $count = count($this->distances);
            //assume stop 0 is the origin

            $stops = array();
            for ($i = 1; $i < $count; $i++) {
                array_push($stops, $i);
            }
            $perms = $this->recursive_permutations($stops);
            $totalDistances = array();
            $minDistanceValue = 0;
            $minDistanceIndex = 0;
            for ($j = 0; $j < count($perms[0]) - 1; $j++) {
                $origin = $perms[0][$j];
                $destination = $perms[0][$j + 1];
                $minDistanceValue += $this->distances[$origin][$destination];
            }


            for ($i = 0; $i < count($perms); $i++) {
                $length = count($perms[$i]);
                $distance = 0;
                for ($j = 0; $j < $length - 1; $j++) {
                    $origin = $perms[$i][$j];
                    $destination = $perms[$i][$j + 1];
                    $distance += $this->distances[$origin][$destination];
                }
                $totalDistances[$i] = $distance;
                if ($distance < $minDistanceValue) {
                    $minDistanceValue = $distance;
                    $minDistanceIndex = $i;
                }

            }
            //echo print_r($totalDistances);
//            echo $minDistanceIndex . "\n";
//            echo $minDistanceValue . "\n";
            echo print_r($perms[$minDistanceIndex]);
            return $perms[$minDistanceIndex];


        } else {
            return array();
        }

    }

	/**
	 * list all possible orders of an array
	 * inspired by code from stackoverflow.com
	 */
    private function recursive_permutations($items, $perms = array("0"))
    {
        static $list;
        if (empty($items)) {
            array_unshift($perms, "0");
            //$list[] = join('', $perms);
            $list[] = $perms;

        } else {
            for ($i = count($items) - 1; $i >= 0; --$i) {
                $newitems = $items;
                $newperms = $perms;
                list($foo) = array_splice($newitems, $i, 1);
                array_unshift($newperms, $foo);
                $this->recursive_permutations($newitems, $newperms);
            };
            return $list;
        };
    }


}