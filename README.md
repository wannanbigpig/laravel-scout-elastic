# laravel-scout-elastic
Elastic Driver for Laravel Scout

### Installation
You can install the package via composer:

`composer require wannanbigpig/laravel-scout-elastic`

Laravel will automatically register the driver service provider.

Install elasticsearch-php client
For use this library we recomend using the version at this (^7.9|^8.0). reference: https://github.com/elastic/elasticsearch-php

`composer require elasticsearch/elasticsearch`

### Setting up Elasticsearch configuration
After you've published the Laravel Scout package configuration, you need to set your driver to elasticsearch and add its configuration:
```php
// config/scout.php
<?php

return [
    // ...
    
    'driver' => env('SCOUT_DRIVER', 'elasticsearch'),
    
    // ...
    
    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Elasticsearch settings.
    |
    */

    'elasticsearch' => [
        'hosts' => [env('ELASTICSEARCH_HOST', 'http://127.0.0.1:9200')],
        // 'auth' => [
        //     'username' => 'elastic',
        //     'password' => 'password copied during Elasticsearch start',
        // ],
        // index_ is followed by the index name. If you do not need to customize the index analyzer, skip the following Settings
        'index_article' => [
            'settings' => [
                'number_of_shards' => 5,
                'number_of_replicas' => 1,
            ],
            'mappings' => [
                "properties" => [
                    "title" => [
                        "type" => "text",
                        "analyzer" => "ik_max_word",
                        "search_analyzer" => "ik_smart",
                        "fields" => ["keyword" => ["type" => "keyword", "ignore_above" => 256]],
                    ],
                ],
            ],
        ],
    ],
];
```
### Usage
##### console
```shell
// create index
php artisan scout:index article

// delete index
php artisan scout:delete-index article

// batch update data to es
php artisan scout:import "App\Models\Article"

```
##### search example
```php
use App\Models\Article;

// $condition = "test";
// ... or
// $condition = [
//     "title" => "test",
//     "abstract" => "test"
// ];
// ... or
$keyword = "test";
$source = [1,2];
$startTime = '2023-05-01T00:00:00.000+0800';
$endTime = '2023-05-20T00:00:00.000+0800';
$condition = [
    "_customize_body" => 1,
    "bool" => [
        "should" => [
            [
                "match" => [
                    "title" => ["query" => $keyword, 'boost' => 5]
                ]
            ],
            [
                "match" => [
                    "abstract" => ["query" => $keyword, 'boost' => 3]
                ]
            ],
        ],
        "must" => [
            [
                "terms" => ["source" => $source]
            ],
            [
                "range" => [
                    "created_at" => [
                        'gte' => $startTime,
                        'lte' => $endTime
                    ]
                ]
            ]
        ]
    ],
     
];

$data = Article::search($condition)
        ->orderBy('_score', 'desc')
        ->paginate(10);
```
More please see [Laravel Scout official documentation](https://laravel.com/docs/10.x/scout).

#### Reference：
https://github.com/ErickTamayo/laravel-scout-elastic
https://github.com/laravel/scout/tree/10.x

### License
The MIT License (MIT).