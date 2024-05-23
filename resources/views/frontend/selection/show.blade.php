<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="content">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="api-key" content="{{ $apiKey }}">
    <title>Agento: Selection {{ $selection->title }}</title>
    <!-- lightgallery -->
    <link rel="stylesheet" href="{{ asset('assets/libs/lightgallery/lightgallery.min.css') }}">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery-js/1.4.0/css/lightgallery.min.css"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

<div class="wrapper">

    <div class="container">
        <div class="language__box">
            <div class="">
                <div class="eng"></div>
                <div class="option__value">
                </div>
            </div>

        </div>
    </div>

    <!-- main -->
    <main class="main">

        <!-- media intro slider -->
        <section class="media__intro-slider">
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    @foreach($selection->adverts as $advert)
                        <div class="swiper-slide">
                            <div class="swiper mySwiper2">
                                <div class="swiper-wrapper">
                                    @foreach($advert->catalogable->images as $image)
                                        <div class="swiper-slide">
                                            <div class="lightgallery">
                                                @php $images = $advert->catalogable->images; @endphp
                                                <a class="relative" href="{{ $image->path }}">
                                                    <img src="{{ $image->path }}"
                                                         alt="{{ $image->id }}">
                                                    <div class="absolute bottom-[20px] right-[20px] hover:scale-125">
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-pagination"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        <!-- intro -->
        <section class="intro">
            <div class="container">
                <div class="swiper mySwiper gallery-2">
                    <div class="swiper-wrapper">
                        @php
                            /** @var \App\Models\Selection\Advert $advert */
                        @endphp
                        @foreach($selection->adverts as $advert)

                            <div class="swiper-slide">
                                <div class="intro__boxes">
                                    <div class="intro__box-left">
                                        <div class="lightgallery">
                                            @php $images = $advert->catalogable->images; @endphp
                                            <a class="relative" href="{{ $images->first()->path }}">
                                                <img src="{{ $images->first()->path }}"
                                                     alt="{{ $images->first()->id }}">
                                            </a>
                                        </div>
                                        <div class="intro__box-btns gallery-3">
                                            <div class="lightgallery">
                                                <a href="{{ $images->first()->path }}">
                                                    <img src="{{ asset('assets/images/icons/camera.svg') }}"
                                                         alt="camera" width="17" height="17">
                                                    <span
                                                        class="ru-lang">Показать {{ $imagesCount }} фотографий</span>
                                                    <span class="en-lang">Show {{ $advert->catalogable->images->count() }} photos</span>
                                                </a>
                                            </div>
                                            <a href="https://www.google.com/maps/place/{{ $advert->catalogable->geo_lat }},{{ $advert->catalogable->geo_lon }}"
                                               target="_blank">
                                                <img src="{{ asset('assets/images/icons/location.svg') }}"
                                                     alt="location" width="14" height="17">
                                                <span class="en-lang">View on map</span>
                                                <span class="ru-lang">Посмотреть на карте</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="intro__box-right lightgallery">
                                        @foreach($images->skip(1) as $image)
                                            <a class="relative" href="{{ $image->path }}">
                                                <img src="{{ $image->path }}" alt="{{ $image->id }}">
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        @endforeach
                    </div>
                </div>
                <div class="intro__sale">
                    <div class="swiper mySwiper">
                        <div class="swiper-wrapper">
                            @foreach($selection->adverts as $advert)

                                <div class="swiper-slide">
                                    <div class="intro__sale-boxes">
                                        <p class="intro__sale-info">{{ $advert->catalogable->property_type }} <span
                                                class="en-lang">for {{ $advert->catalogable->deal_type }}</span>
                                            <span class="ru-lang">НА
												ПРОДАЖУ</span>in {{ $advert->catalogable->property_city }}
                                            {{ $advert->catalogable->property_tower }}
                                            {{ $advert->catalogable->property_community }}
                                            {{ $advert->catalogable->property_subcommunity }}
                                        </p>
                                        <h4 class="intro__sale-title">{{ $advert->catalogable->title }}</h4>
                                        <div class="intro__sale-box">
                                            <ul>
                                                <li>
                                                    <div class="left__box">
                                                        <img src="{{ asset('assets/images/icons/building.svg') }}"
                                                             alt="building">
                                                        <p class="en-lang">Property type:</p>
                                                        <p class="ru-lang">Тип объекта:</p>
                                                    </div>
                                                    <p class="proto__name">{{ $advert->catalogable->property_type }}</p>
                                                </li>
                                                <li>
                                                    <div class="left__box">
                                                        <img src="{{ asset('assets/images/icons/bedrooms.svg') }}"
                                                             alt="building">
                                                        <p><span class="en-lang">Bedrooms</span></p>
                                                        <p class="ru-lang">Кол-во комнат:</p>
                                                    </div>
                                                    <p class="proto__name">{{ $advert->catalogable->bedrooms }}</p>
                                                </li>
                                                <li>
                                                    <div class="left__box">
                                                        <img src="{{ asset('assets/images/icons/project.svg') }}"
                                                             alt="building">
                                                        <p class="en-lang">Project:</p>
                                                        <p class="ru-lang">Проект:</p>
                                                    </div>
                                                    <p class="proto__name underline">
                                                        @if ($advert->catalogable->property_tower)
                                                            {{ $advert->catalogable->property_tower }}
                                                        @else
                                                            Unknown
                                                        @endif
                                                    </p>
                                                </li>
                                            </ul>

                                            <ul>
                                                <li class="desktop__none">
                                                    <div class="left__box">
                                                        <img src="{{ asset('assets/images/icons/bedrooms.svg') }}"
                                                             alt="building">
                                                        <p><span class="en-lang">Bathrooms</span></p>
                                                        <p class="ru-lang s-none">Кол-во ванных:</p>
                                                    </div>
                                                    <p class="proto__name">{{ $advert->catalogable->bedrooms }}
                                                        bedroom</p>
                                                </li>

                                                <div class="line"></div>
                                                <li>
                                                    <div class="left__box">
                                                        <img src="{{ asset('assets/images/icons/size.svg') }}"
                                                             alt="building">
                                                        <p>
                                                            <span class="en-lang">Property size</span>
                                                        </p>
                                                        <p class="ru-lang s-none">Площадь:</p>
                                                    </div>
                                                    <p class="proto__name media__none">{{ $advert->catalogable->size_sqft }}
                                                        <span
                                                            class="en-lang">sqft</span> <span
                                                            class="ru-lang">кв. фут</span>
                                                        / {{ $advert->catalogable->size_m2 }} <span
                                                            class="en-lang">sqm</span> <span
                                                            class="ru-lang">кв. м</span></p>
                                                    <p class="proto__name desktop__none">{{ $advert->catalogable->size_sqft }}
                                                        <span
                                                            class="en-lang">sqft</span> <span
                                                            class="ru-lang">кв. фут</span></p>
                                                </li>
                                                <div class="line last"></div>
                                                <li>
                                                    <div class="left__box">
                                                        <img src="{{ asset('assets/images/icons/bathrooms.svg') }}"
                                                             alt="building">
                                                        <p><span class="en-lang">Bathrooms</span></p>
                                                        <p class="ru-lang s-none">Кол-во ванных:</p>
                                                    </div>
                                                    <p class="proto__name flex-media"> {{ $advert->catalogable->bathrooms }}
                                                        <span class="media__inline">
															<span class="en-lang">Bathrooms</span>
															<span class="ru-lang">Кол-во ванных:</span>
														</span>
                                                    </p>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="slide__btns">
                        <div class="slide__btns-header">
                            <div class="swiper mySwiper">
                                <div class="swiper-wrapper">
                                    @foreach($selection->adverts as $advert)
                                        <div class="swiper-slide">
                                            <h5>{{ number_format($advert->catalogable->price, 0, ',') }}&nbsp;AED</h5>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="slider-pagination"></div>
                            <p class="en-lang">property</p>
                            <p class="ru-lang">объект</p>
                        </div>
                        <div class="slide__btns-footer">
                            <div class="swiper btn__swiper mySwiper">
                                <div class="swiper-wrapper">
                                    @foreach($selection->adverts as $advert)
                                        <div class="swiper-slide">
                                            <button class="like__btn" data-advert-id="{{ $advert->id }}">
                                                <span class="en-lang">Like</span>
                                                <span class="ru-lang">Нрав</span>
                                            </button>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                            <button class="btn button-prev">
                                <span class="en-lang">Previuous</span>
                                <span class="ru-lang">Пред</span>
                            </button>
                            <button class="btn button-next">
                                <span class="en-lang">Next</span>
                                <span class="ru-lang">След</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- location -->
        <section class="location container">
            <div class="location__container">
                <div class="swiper mySwiper">
                    <div class="swiper-wrapper">
                        @foreach($selection->adverts as $advert)
                            <div class="swiper-slide">
                                <h3 class="title en-lang">Location</h3>
                                <h3 class="title ru-lang">Местоположение</h3>
                                <div class="location__boxes">
                                    <div class="location__map">
                                        <iframe
                                            width="140"
                                            height="140"
                                            frameborder="0"
                                            style="border:0"
                                            loading="lazy"
                                            referrerpolicy="no-referrer-when-downgrade"
                                            src="https://www.google.com/maps/embed/v1/place?key={{ config('google.google_maps.key') }}&q={{ urlencode($advert->catalogable->full_location_path) }}"
                                            >
                                        </iframe>
{{--                                        <a class="map__content"--}}
{{--                                           href="https://www.google.com/maps/place/{{ $advert->catalogable->geo_lat }},{{ $advert->catalogable->geo_lon }}"--}}
{{--                                           target="_blank">--}}
{{--                                            <span class="en-lang">Map</span>--}}
{{--                                            <span class="ru-lang">карта</span>--}}
{{--                                        </a>--}}
                                    </div>
                                    <div class="location__box">
                                        <p>{{ $advert->catalogable->property_city }}
                                            &nbsp;{{ $advert->catalogable->property_tower }}</p>
                                        <p>{{ $advert->catalogable->full_location_path }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </section>

        <!-- Amenities -->
        <section class="amenities container">
            <div class="amenities__container">
                <div class="swiper mySwiper">
                    <div class="swiper-wrapper">
                        @foreach($selection->adverts as $advert)
                            <div class="swiper-slide">
                                <h3 class="title en-lang">Amenities</h3>
                                <h3 class="title ru-lang">Удобства</h3>
                                <div class="amenities__boxes">
                                    @foreach($advert->catalogable->getAmenityNamesCollection()->chunk(6) as $chunk)
                                        <div class="amenities__box">
                                            <ul>
                                                @foreach($chunk as $amenity)
                                                    <li>
                                                        <p>{{ $amenity }}</p>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </main>


    <!-- footer -->
    <footer class="footer">

    </footer>

</div>

<!-- lightgallery -->
<script src="{{ asset('assets/libs/lightgallery/lightgallery.min.js') }}"></script>

</body>

</html>
