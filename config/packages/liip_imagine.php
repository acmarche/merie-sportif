<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

/**
 * my_thumb :
# adjust the image quality to 75%
jpeg_quality : 100
# list of transformations to apply (the "filters")
filters :
# create a thumbnail: set size to 120x90 and use the "outbound" mode
# to crop the image when the size ratio of the input differs
thumbnail  : { size : [120, 45], mode : inset }
# create a 2px black border: center the thumbnail on a black background
# 4px larger to create a 2px border around the final image
#background : { size : [124, 94], position : center, color : '#000000' }
my_heighten_filter:
filters:
# use and setup the "relative_resize" filter
relative_resize:
# given 50x40px, output 75x120px using "heighten" option
heighten: 120

 */
return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('liip_imagine', [
        'resolvers' => [
            'default' => [
                'web_path' => null,
                
                
            ],
        ],
    ]);

    $containerConfigurator->extension(
        'liip_imagine',
        [
            'filter_sets' => [
                'cache' => null,
                'merite_thumb' => [
                    'quality' => 100,
                    'filters' => [
                        'thumbnail' => [
                            'size' => [250, 188],
                            'mode' => 'inset',
                            
                        ],
                    ],
                    
                ],
            ],
        ]
    );
};
