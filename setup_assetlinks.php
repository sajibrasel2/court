<?php
// Run this script once on cPanel to set up Digital Asset Links at root domain
// After running, delete this file for security

$dir = $_SERVER['DOCUMENT_ROOT'] . '/.well-known';
$file = $dir . '/assetlinks.json';

if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

$assetlinks = '[
  {
    "relation": ["delegate_permission/common.handle_all_urls"],
    "target": {
      "namespace": "android_app",
      "package_name": "site.techandclick.twa",
      "sha256_cert_fingerprints": [
        "5D:FC:07:A4:97:DB:68:DC:24:7A:F2:87:D2:E4:38:86:92:69:33:E1:72:34:8D:7E:D3:F3:46:C3:89:7E:38:98"
      ]
    }
  }
]';

$result = file_put_contents($file, $assetlinks);

if ($result !== false) {
    echo "SUCCESS: assetlinks.json created at root domain!<br>";
    echo "Path: $file<br>";
    echo "Verify: <a href='https://techandclick.site/.well-known/assetlinks.json' target='_blank'>https://techandclick.site/.well-known/assetlinks.json</a><br><br>";
    echo "IMPORTANT: Delete this setup_assetlinks.php file now for security!";
} else {
    echo "ERROR: Could not create file. Check permissions.";
}
?>
