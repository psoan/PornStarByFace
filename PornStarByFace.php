<?php
class PornStarByFace
{
    private function request($type, $param = [])
    {
        $cp = curl_init('https://pornstarbyface.com/Home/' . $type);
        curl_setopt_array($cp, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $param,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36'
        ]);
        $result = curl_exec($cp);
        curl_close($cp);
        return $result;
    }
    public function getResults($source)
    {
        $dom = new DOMDocument;
        $dom->loadHTML('<?xml encoding="UTF-8">' . $source);
        $xpath = new DOMXPath($dom);
        $results = $xpath->query('//div[@class="col-lg-3 col-offset-3 candidate realCandidate text-left"]');
        foreach ($results as $key => $result) {
            preg_match('/(\d+)/', trim($result->nodeValue), $percent);
            $return[$key]['image'] = 'https://pornstarbyface.com' . trim($result->getElementsByTagName('img')[0]->attributes[2]->value);
            $return[$key]['percent'] = $percent[1];
            $return[$key]['name'] = trim($result->getElementsByTagName('p')[0]->nodeValue);
            $return[$key]['profile'] = 'https://pornstarbyface.com' . trim($result->getElementsByTagName('a')[1]->attributes[1]->value);
        }
        return $return;
    }
    public function searchByImage($image_path)
    {
        $source =  $this->request('LooksLikeByPhoto', ['imageUploadForm' => curl_file_create($image_path)]);
        return $this->getResults($source);
    }
    public function searchByUrl($url)
    {
        $source = $this->request('LooksLike', ['url' => $url, 'isWeb' => false]);
        return $this->getResults($source);
    }
}
