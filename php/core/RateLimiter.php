<?php
class RateLimiter {
    public static function check($ip, $limit = 5, $period = 60) {
        $storage_path = __DIR__ . '/../storage/ratelimit/';
        if (!file_exists($storage_path)) {
            mkdir($storage_path, 0777, true);
        }

        $file = $storage_path . $ip . '.json';
        $data = [];

        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
        }

        // Filter out old requests
        $now = time();
        $data = array_filter($data, function($timestamp) use ($now, $period) {
            return ($now - $timestamp) < $period;
        });

        if (count($data) >= $limit) {
            return false; // Limit exceeded
        }

        // Add current request
        $data[] = $now;
        file_put_contents($file, json_encode($data));

        return true; // Allowed
    }
}
?>
