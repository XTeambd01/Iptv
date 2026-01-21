<?php
// ১. ব্রাউজার এবং ক্রস-ডোমেইন পারমিশন
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain; charset=utf-8");

// ২. প্লেলিস্টের লিঙ্কের তালিকা (List)
// বিশেষ দ্রষ্টব্য: প্রতিটি লাইনের শেষে কমা (,) দিতে ভুলবেন না
$urls = [
    "https://is.gd/Bnmgis.m3u",
    "https://is.gd/YJ307U.m3u", 
    "https://is.gd/QnlwJr.m3u",
    "https://is.gd/woSv8q.m3u",
    
];

$combinedM3U = "#EXTM3U\n"; // মাস্টার হেডার

foreach ($urls as $url) {
    // cURL সেটআপ (সবচেয়ে শক্তিশালী পদ্ধতি)
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // SSL সমস্যা এড়াতে
    // ব্রাউজারের মতো ভান করবে যাতে সার্ভার ব্লক না করে
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 15); // ১৫ সেকেন্ডের মধ্যে রেস্পন্স না পেলে বাদ দিবে

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // যদি ডাটা সঠিকভাবে আসে
    if ($httpCode == 200 && $response) {
        // বারবার #EXTM3U লেখা আসলে রিমুভ করে ক্লিন ডাটা যোগ করবে
        $cleanData = str_replace("#EXTM3U", "", $response);
        $combinedM3U .= "\n" . trim($cleanData);
    }
    
    curl_close($ch);
}

// ৩. ফাইনাল আউটপুট দেখানো
if (strlen($combinedM3U) > 20) {
    echo $combinedM3U;
} else {
    // যদি কোনো লিংক কাজ না করে, এই এরর মেসেজ দেখাবে
    echo "#EXTM3U\n#EXTINF:-1,SERVER ERROR (Try Again Later)\nhttp://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4";
}
?>
