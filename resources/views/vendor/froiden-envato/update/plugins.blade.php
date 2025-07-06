@php
    $allModules = Module::all();
    $activeModules = [];

    function moveUniversalToFront($array, $keyword = 'Universal') {
            // Find the index of the item with the specified keyword in the product_name
            $index = array_search(true, array_map(function ($item) use ($keyword) {
                return stripos($item['product_name'], $keyword) !== false;
            }, $array));

            // If the item is found, move it to the first position
            if ($index !== false) {
                $item = $array[$index];
                unset($array[$index]);
                array_unshift($array, $item);
            }

            return $array;
    }

     $universal = false;

    foreach ($allModules as $module) {

         $config = require base_path() . '/Modules/' . $module . '/Config/config.php';

          if(isset($config['envato_item_id']) && $config['envato_item_id']!== ''){
                if(stripos($config['name'], 'universal') !== false){
                    $universal = true;
                    break;
                }
                $activeModules[] = $config['envato_item_id'];
          }
    }

     $notInstalledModules = [];

     if(!$universal){
         $plugins = \Froiden\Envato\Functions\EnvatoUpdate::plugins();

        if (empty($plugins)) {
            $plugins = [];
        }else{
            $plugins = moveUniversalToFront($plugins);
        }

        foreach ($plugins as $item) {
            if (!in_array($item['envato_id'], $activeModules)) {
                $notInstalledModules[] = $item;
            }
        }
     }


@endphp

@if (count($notInstalledModules) && !$universal)
    <div class="col-sm-12 mt-5">
        <h4>{{ str(config('froiden_envato.envato_product_name'))->replace('new', '')->headline() }} Official
            Modules</h4>
        <div class="row">

            @foreach ($notInstalledModules as $item)
                <div
                    class="col-sm-12 border rounded p-3 mt-4 box @if (stripos($item['product_name'], 'universal') !== false) rainbow @endif">
                    <div class="row">
                        <div class="col-lg-2 col-xs-2">
                            <a href="{{ $item['product_link'] }}" target="_blank">
                                <img src="{{ $item['product_thumbnail'] }}" class="img-responsive" alt="">
                            </a>

                            @if(isset($item['rating']))
                                <br><span class="f-12 text-center mt-1"><i class="fa fa-star text-warning"></i> {{number_format($item['rating'],1)??0}} <small class="text-muted"><em>{{$item['number_of_sales']??0}} Sales</em></small></span>
                            @endif
                        </div>
                        <div class="col-lg-8 col-xs-5">
                            <a href="{{ $item['product_link'] }}" target="_blank"
                               class="f-w-500 f-14 text-darkest-grey">{{ $item['product_name'] }}
                            </a>

                            <p class="f-12 text-muted">
                                {{ $item['summary'] }}
                            </p>
                        </div>
                        <div class="col-lg-2 col-xs-5 text-right pt-4">
                            <x-forms.link-primary :link="$item['product_link']" data-toggle="tooltip"
                                                  data-original-title="Visit {{$item['product_name']}} Page"
                                                  target="_blank" icon="arrow-right">
                            </x-forms.link-primary>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
