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

        $folder="images/uploaded/";
        $disk = Storage::build([
            'driver' => 'local',
            'root' => public_path($folder),
        ]);

        $path=$disk->path('');

        $dom = new \DomDocument();
        $dom->loadHtml(trim($content), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $imageFile = $dom->getElementsByTagName('img');

        foreach ($imageFile as $item => $image) {
            $src = $image->getAttribute('src');
            if (file_exists($src)) {
                $code = file_get_contents($src);
                $type = \File::mimeType($src);
                $name=time().".".pathinfo($src, PATHINFO_EXTENSION);
                $src = asset($folder.$name);//str_replace(request()->getSchemeAndHttpHost(), '', route('image', $picture));

                $picture = Image::create(compact('code', 'type','name','path','src'));
                $disk->put($name, $code);
                $image->removeAttribute('src');
                $image->setAttribute('src', $src);
            }
        }

        $value = trim($dom->saveHTML());
        return [$key => $value];
    }
}
