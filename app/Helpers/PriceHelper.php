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

        // Format based on value - Rule: < 1 tỉ hiển thị triệu, >= 1 tỉ hiển thị tỉ
        if ($priceInMillion >= 1000) {
            // >= 1000 triệu (>= 1 tỉ) → hiển thị theo tỉ
            $ty = $priceInMillion / 1000;
            if ($ty == (int)$ty) {
                return number_format($ty, 0, ',', '.') . ' tỉ';
            } else {
                // Làm tròn đến 1 chữ số thập phân, sau đó bỏ .0 nếu không cần
                $ty = round($ty, 1);
                $formatted = number_format($ty, 1, ',', '.');
                $formatted = rtrim(rtrim($formatted, '0'), ',');
                return $formatted . ' tỉ';
            }
        } else {
            // < 1000 triệu (< 1 tỉ) → hiển thị theo triệu
            if ($priceInMillion == (int)$priceInMillion) {
                return number_format($priceInMillion, 0, ',', '.') . ' triệu';
            } else {
                // Làm tròn đến 1 chữ số thập phân, sau đó bỏ .0 nếu không cần
                $priceInMillion = round($priceInMillion, 1);
                $formatted = number_format($priceInMillion, 1, ',', '.');
                $formatted = rtrim(rtrim($formatted, '0'), ',');
                return $formatted . ' triệu';
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

        // Format with 1 decimal place, then remove .0 if not needed
        $formatted = number_format($pricePerM2InMillion, 1, ',', '.');
        $formatted = rtrim(rtrim($formatted, '0'), ',');
        return $formatted . ' tr/m²';
    }
}

if (!function_exists('formatNumber')) {
    /**
     * Format number without unnecessary .0
     *
     * @param float|int|null $number Number to format
     * @param int $decimals Number of decimal places (default 1)
     * @return string Formatted number string
     */
    function formatNumber($number, $decimals = 1) {
        if ($number === null) {
            return '0';
        }
        $formatted = number_format((float) $number, $decimals, '.', ',');
        // Remove trailing .0 or .00 etc
        $formatted = rtrim(rtrim($formatted, '0'), '.');
        return $formatted;
    }
}


