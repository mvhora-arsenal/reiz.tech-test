<?php

namespace App\Services;

use App\Jobs\ScrapeHtml;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class JobService
{

    /**
     * Register all the available jobs in the redis
     */
    public function register($data): array
    {
        $result = [];
        foreach ($data['urls'] as $url) {
            $selectorData = [];
            $selectorData['url'] = $url;
            $selectorData['status'] = 'Pending';
            $selectorData['selectors'] = $data['selectors'];
            $randomString = Str::random(10);
            $this->setRedisData($randomString, $selectorData);
            ScrapeHtml::dispatch($randomString);
            $result[] = $randomString;
        }
        return $result;
    }

    /**
     * Fetch the data from the URL's provided and store it in the redis
     */
    public function scrapeData($id): void
    {
        try {
            $data = $this->getJobById($id);

            $selectors = $data['selectors'];
            unset($data['selectors']);

            $data['status'] = 'In Progress';
            $this->setRedisData($id, $data);

            $response = Http::get($data['url']);

            if ($response->successful()) {
                $htmlContent = $response->body();
                foreach ($selectors as $selector) {
                    $crawler = new Crawler($htmlContent);
                    $selectorText = $crawler->filter($selector)->each(function ($node) {
                        // Extract the text content of each selector
                        return $node->text();
                    });
                    $data['selectors'][$selector] = $selectorText;
                }

                $data['status'] = 'Completed';
                $this->setRedisData($id, $data);
            } else {
                $data['status'] = 'Failed';
                $data['error_message'] = json_encode($response->body());
                $this->setRedisData($id, $data);
            }
        } catch (\Throwable $th) {
            Log::error(['message' => $th->getMessage()]);
        }
    }

    /**
     *  Set Data in the redis
     */
    public function setRedisData($id, $data): void
    {
        Redis::set($id, json_encode($data));
    }

    /**
     * Get Job by ID From Redis
     */
    public function getJobById($id) : array | string
    {
        $getJob = Redis::get($id);
        if ($getJob > 0) {
            return json_decode($getJob, true);
        } else {
            return "$id not found.";
        }
    }

    /**
     * Get All Job From Redis
     */
    public function getAllJob(): array
    {
        $redisData = [];
        $keys = Redis::keys('*');

        foreach ($keys as $key) {
            if (!Str::contains($key, 'queues:default')) { // If the queue is not running then these key will be stored in redis. To remove those while displaying we are adding condition
                $value = Redis::get($key);
                $redisData[$key] = json_decode($value);
            }
        }

        return $redisData;
    }

    /**
     * Delete Job From Redis By ID
     */
    public function deleteJobById($keyName) : string
    {
        $deleteKey = Redis::del($keyName);
        // Check if the key was successfully deleted
        if ($deleteKey > 0) {
            return "$keyName deleted successfully from Redis.";
        } else {
            return "$keyName was not found in Redis.";
        }
    }
}
