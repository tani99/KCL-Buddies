<?php

namespace App;

use Illuminate\Contracts\Filesystem\FileNotFoundException as FileNotFoundException;
use Illuminate\Support\Facades\Storage as Storage;

class CountryHandler
{
    private static $mapping = [];

    /**
     * Get the average latitude and longitude of a country
     * @param $code string The 2 letter country code of the country
     * @return array|null The latitude and longitude as an array or null if the country code is invalid.
     * @throws FileNotFoundException
     */
    public static function alpha2LatLonLookup($code): ?array
    {
        if (empty($mapping)) self::createMapping();
        $upperCaseCode = strtoupper($code);
        return array_key_exists($upperCaseCode, self::$mapping) ? self::$mapping[$upperCaseCode] : null;
    }

    /**
     * Create the mapping from country codes to latitude and longitudes in arrays of size 2
     * @throws FileNotFoundException
     */
    private static function createMapping()
    {
        $contents = Storage::disk('local')->get('Alpha2ToLatLon.csv'); // Open csv
        $data = preg_split("/\r\n|\n|\r/", $contents);
        for ($i = 0; $i < count($data); ++$i) $data[$i] = explode(',', $data[$i]);

        foreach ($data as $line) {
            if (count($line) >= 3) {
                self::$mapping[$line[0]] = [floatval($line[1]), floatval($line[2])]; // Add data to array
            }
        }
    }
}