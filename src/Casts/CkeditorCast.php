<?php

namespace Nabre\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Facades\Storage;
use Nabre\Models\Image;

class CkeditorCast implements CastsAttributes
{

    public function get($model, $key, $value, $attributes)
    {
        return $value;
    }

    public function set($model, $key, $value, $attributes)
    {
        $value =  preg_replace("/<script.*?\/script>/s", "", $value) ?: $value;

        $content = $value;


        $disk = Storage::build([
            'driver' => 'local',
            'root' => public_path('images'),
        ]);

        $dom = new \DomDocument();
        $dom->loadHtml(trim($content), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $imageFile = $dom->getElementsByTagName('img');

        foreach ($imageFile as $item => $image) {
            $src = $image->getAttribute('src');
            if (file_exists($src)) {
                $code = file_get_contents($src);
                $type = \File::mimeType($src);
                $filename=time().".".pathinfo($src, PATHINFO_EXTENSION);
                $picture = Image::create(compact('code', 'type'));

                $disk->put($filename, $code);

                $image->removeAttribute('src');
                $src = asset('images/'.$filename);//str_replace(request()->getSchemeAndHttpHost(), '', route('image', $picture));
                $image->setAttribute('src', $src);
            }
        }

        $value = trim($dom->saveHTML());
        return [$key => $value];
    }
}
