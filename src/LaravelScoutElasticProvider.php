<?php

namespace Wannanbigpig\LaravelScoutElastic;

use Exception;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Laravel\Scout\EngineManager;
use Illuminate\Support\ServiceProvider;
use Wannanbigpig\LaravelScoutElastic\Engines\ElasticsearchEngine;

class LaravelScoutElasticProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->ensureElasticClientIsInstalled();

        resolve(EngineManager::class)->extend('elasticsearch', function () {
            $client = ClientBuilder::create()->setHosts(Config::get('scout.elasticsearch.hosts'));
            $auth = Config::get('scout.elasticsearch.auth');
            if (Arr::has($auth, 'username')) {
                $client = $client->setBasicAuthentication(
                    Arr::get($auth, 'username', ''),
                    Arr::get($auth, 'password', '')
                );
            }

            return new ElasticsearchEngine($client->build());
        });
    }

    /**
     * Ensure the Elastic API client is installed.
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function ensureElasticClientIsInstalled()
    {
        if (class_exists(ClientBuilder::class)) {
            return;
        }

        throw new Exception('Please install the Elasticsearch PHP client: elasticsearch/elasticsearch.');
    }
}
