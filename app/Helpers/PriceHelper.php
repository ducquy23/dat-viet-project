<?php

if (!function_exists('formatPrice')) {
    /**
     * Format price in a user-friendly way
     * 
     * @param float|int|null $price Price value (can be in đồng or triệu)
     * @return string Formatted price string
     */
    function formatPrice($price) {
        if (!$price || $price <= 0) {
            return 'Liên hệ';
        }

        // Convert to triệu if price is in đồng (VND)
        $priceInMillion = $price >= 1000000 ? $price / 1000000 : $price;

        // Format based on value
        if ($priceInMillion >= 1000) {
            // >= 1000 triệu → hiển thị theo tỉ
            $ty = $priceInMillion / 1000;
            if ($ty == (int)$ty) {
                return number_format($ty, 0, ',', '.') . ' tỉ';
            } else {
                return number_format($ty, 1, ',', '.') . ' tỉ';
            }
        } elseif ($priceInMillion >= 100) {
            // >= 100 triệu → hiển thị theo trăm triệu
            $tram = $priceInMillion / 100;
            if ($tram == (int)$tram) {
                return number_format($tram, 0, ',', '.') . ' trăm triệu';
            } else {
                return number_format($tram, 1, ',', '.') . ' trăm triệu';
            }
        } else {
            // < 100 triệu → hiển thị theo triệu
            if ($priceInMillion == (int)$priceInMillion) {
                return number_format($priceInMillion, 0, ',', '.') . ' triệu';
            } else {
                return number_format($priceInMillion, 1, ',', '.') . ' triệu';
            }
        }
    }
}

if (!function_exists('formatPricePerM2')) {
    /**
     * Format price per m2 in a user-friendly way
     * 
     * @param float|int|null $pricePerM2 Price per m2 (can be in đồng/m² or triệu/m²)
     * @param float|int|null $price Total price (for calculation if pricePerM2 not set)
     * @param float|int|null $area Area in m² (for calculation if pricePerM2 not set)
     * @return string|null Formatted price per m2 string or null
     */
    function formatPricePerM2($pricePerM2, $price = null, $area = null) {
        // Calculate if not provided
        if (!$pricePerM2 && $price && $area && $area > 0) {
            $pricePerM2 = $price / $area;
        }

        if (!$pricePerM2 || $pricePerM2 <= 0) {
            return null;
        }

        // Convert to triệu/m² if price_per_m2 is in đồng/m²
        $pricePerM2InMillion = $pricePerM2 >= 1000000 
            ? $pricePerM2 / 1000000 
            : $pricePerM2;

        // Format with 1 decimal place
        return number_format($pricePerM2InMillion, 1, ',', '.') . ' tr/m²';
    }
}


