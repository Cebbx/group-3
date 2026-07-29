<?php

namespace App\Services;

class PhilippineAddressService
{
    protected static ?array $regions = null;

    public static function getRegions(): array
    {
        if (self::$regions === null) {
            $path = storage_path('app/json/regions.json');
            if (file_exists($path)) {
                $data = json_decode(file_get_contents($path), true);
                $regions = [];
                foreach ($data as $item) {
                    $regions[$item['region_code']] = $item['region_name'];
                }
                self::$regions = $regions;
            } else {
                self::$regions = [];
            }
        }
        return self::$regions;
    }

    public static function getProvinces(?string $regionCode): array
    {
        if (empty($regionCode)) {
            return [];
        }
        $path = storage_path('app/json/provinces.json');
        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
            $provinces = [];
            foreach ($data as $item) {
                if ($item['region_code'] === $regionCode) {
                    $provinces[$item['province_code']] = $item['province_name'];
                }
            }
            asort($provinces);
            return $provinces;
        }
        return [];
    }

    public static function getCities(?string $provinceCode): array
    {
        if (empty($provinceCode)) {
            return [];
        }
        $path = storage_path('app/json/cities.json');
        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
            $cities = [];
            foreach ($data as $item) {
                if ($item['province_code'] === $provinceCode) {
                    $cities[$item['city_code']] = $item['city_name'];
                }
            }
            asort($cities);
            return $cities;
        }
        return [];
    }

    public static function getBarangays(?string $cityCode): array
    {
        if (empty($cityCode)) {
            return [];
        }
        $path = storage_path('app/json/barangays.json');
        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
            $barangays = [];
            foreach ($data as $item) {
                if ($item['city_code'] === $cityCode) {
                    $barangays[$item['brgy_code']] = $item['brgy_name'];
                }
            }
            asort($barangays);
            return $barangays;
        }
        return [];
    }

    public static function getCodesFromNames(?string $regionName, ?string $provinceName, ?string $cityName, ?string $brgyName): array
    {
        $regionCode = null;
        $provinceCode = null;
        $cityCode = null;
        $brgyCode = null;

        if ($regionName) {
            $regions = self::getRegions();
            $regionCode = array_search($regionName, $regions) ?: null;
        }

        if ($provinceName && $regionCode) {
            $provinces = self::getProvinces($regionCode);
            $provinceCode = array_search($provinceName, $provinces) ?: null;
        }

        if ($cityName && $provinceCode) {
            $cities = self::getCities($provinceCode);
            $cityCode = array_search($cityName, $cities) ?: null;
        }

        if ($brgyName && $cityCode) {
            $barangays = self::getBarangays($cityCode);
            $brgyCode = array_search($brgyName, $barangays) ?: null;
        }

        return [$regionCode, $provinceCode, $cityCode, $brgyCode];
    }
}
