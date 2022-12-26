<?php
$routes = [];
route("/", function () {
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    $content = array();
    $content["body"] = array();
    for ($i = 1; $i <= 12; $i++) {
        if ($i === 1) {
            $url = "https://chiasemoi.com/sach-tom-tat";
        } else {
            $url = "https://chiasemoi.com/sach-tom-tat/page/" . $i;
        }

        $api = file_get_contents($url);
        preg_match_all('#<article class="item-list".*?>(.+?)</article>#si', $api, $match);

        if (!empty($match[1])) {
            foreach ($match[1] as $item) {
                preg_match('#<h2 class="post-box-title">(.+?)</h2>#si', $item, $a);
                preg_match('#<a.*?>(.+?)</a>#si', $a[1], $title);
                preg_match('#<a href="(.*?)">(.+?)</a>#si', $a[1], $href);
                preg_match('#<img.*? src="(.*?)" class="attachment-tie-medium size-tie-medium wp-post-image" alt="" />#si', $item, $img);
                $itemArr = array(
                    "img" => $img[1],
                    "href" => $href[1],
                    "title" => $title[1]
                );
                array_push($content["body"], $itemArr);
            }
        }
    }
    echo json_encode($content["body"]);
});

function route($path, $callback)
{
    global $routes;
    $routes[$path] = $callback;
}

route("/login", function () {
    echo "Login page";
});

function run()
{
    global $routes;
    $uri = $_SERVER["REQUEST_URI"];
    foreach ($routes as $path => $callback) {
        if ($path !== $uri) continue;
        $callback();
    }
}
run();
