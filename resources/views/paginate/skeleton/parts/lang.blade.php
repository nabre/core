<?php $localization = new Nabre\Repositories\LocalizationRepositorie(); ?>
<?php $localization->menuSettings($aviableLocale, $currentLocale); ?>
@if($aviableLocale->count()>1)
<div class="dropdown">
    <button title="{{ $currentLocale->language }}" class="btn btn-sm btn-light dropdown-toggle" type="button"
        id="dropdownLanguageButton" data-bs-toggle="dropdown" aria-expanded="false">
        {!! $currentLocale->icon !!}
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownLanguageButton">
        @foreach ($aviableLocale as $it)
            <li><a class="dropdown-item {{ $it->lang == $currentLocale->lang ? 'active' : '' }}"
                    href="{{ route('set.lang',$it->lang) }}" title="{{ $it->language }}">
                    {!! $it->icon !!} {{ $it->language }}</a>
            </li>
        @endforeach
    </ul>
</div>
@endif
