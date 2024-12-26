<?php
$blacklist = ['10.211.55.8', '10.211.55.0/24']; // Kara liste

function is_ip_blacklisted($ip, $blacklist) {
    foreach ($blacklist as $blocked_ip) {
        if (strpos($blocked_ip, '/') !== false) {
            // CIDR Aralığını Kontrol Et
            list($subnet, $bits) = explode('/', $blocked_ip);
            $ip_binary = ip2long($ip);
            $subnet_binary = ip2long($subnet);
            $mask = ~((1 << (32 - $bits)) - 1);
            if (($ip_binary & $mask) === ($subnet_binary & $mask)) {
                return true;
            }
        } else {
            // Doğrudan IP Eşleşmesi
            if ($ip === $blocked_ip) {
                return true;
            }
        }
    }
    return false;
}

// Kullanıcı IP adresini kontrol et
$user_ip = '10.211.55.8'; // Test için bir IP adresi

if (is_ip_blacklisted($user_ip, $blacklist)) {
    echo "IP ($user_ip) is blacklisted.";
} else {
    echo "IP ($user_ip) is allowed.";
}
?>
