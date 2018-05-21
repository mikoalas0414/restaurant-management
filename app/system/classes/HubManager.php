<?php namespace System\Classes;

use Cache;
use Carbon\Carbon;
use Config;
use Exception;
use Log;
use Request;

/**
 * Hub Manager Class
 * @package System
 */
class HubManager
{
    use \Igniter\Flame\Traits\Singleton;

    const ENDPOINT = 'https://api.tastyigniter.com/v2';

    protected $cachePrefix;

    protected $cacheTtl;

    public function initialize()
    {
        $this->cachePrefix = 'hub_';
        $this->cacheTtl = 15;
    }

    public function listItems($filter = [])
    {
        $cacheKey = $this->getCacheKey('items', $filter);

        if (!$items = Cache::get($cacheKey)) {
            $items = $this->requestRemoteData('items', array_merge(['include' => 'require'], $filter));

            if (!empty($items) AND is_array($items))
                Cache::put($cacheKey, $items, $this->cacheTtl);
        }

        return $items;
    }

    public function getDetail($type, $itemName = [])
    {
        return $this->requestRemoteData("{$type}/detail", ['item' => json_encode($itemName)]);
    }

    public function getDetails($type, $itemNames = [])
    {
        return $this->requestRemoteData("{$type}/details", ['items' => json_encode($itemNames)]);
    }

    public function applyItems($itemNames = [])
    {
        $response = $this->requestRemoteData('core/apply', [
            'items'   => json_encode($itemNames),
            'version' => params('ti_version'),
        ]);

        return $response;
    }

    public function applyItemsToUpdate($itemNames, $force = FALSE)
    {
        $cacheKey = $this->getCacheKey('updates', $itemNames);

        if ($force OR !$response = Cache::get($cacheKey)) {
            $response = $this->requestRemoteData('core/apply', [
                'items'   => json_encode($itemNames),
                'include' => 'tags',
                'version' => params('ti_version'),
                'force'   => $force,
            ]);

            if (is_array($response)) {
                $response['check_time'] = Carbon::now()->toDateTimeString();
                Cache::put($cacheKey, $response, $this->cacheTtl);
            }
        }

        return $response;
    }

    public function buildMetaArray($response)
    {
        if (isset($response['type']))
            $response = ['items' => [$response]];

        if (isset($response['items'])) {
            $extensions = [];
            foreach ($response['items'] as $item) {
                if ($item['type'] == 'extension' AND
                    (!ExtensionManager::instance()->findExtension($item['type']) OR ExtensionManager::instance()->isDisabled($item['code']))
                ) {
                    if (isset($item['tags']))
                        arsort($item['tags']);

                    $extensions[$item['code']] = $item;
                }
            }

            unset($response['items']);
            $response['extensions'] = $extensions;
        }

        return $response;
    }

    public function setSecurity($key, $info)
    {
        params()->set('carte_key', $key ? encrypt($key) : '');

        if ($info AND is_array($info))
            params()->set('carte_info', $info);

        params()->save();
    }

    public function getSecurity()
    {
        return (!$carteKey = params('carte_key')) ? md5('NULL') : decrypt($carteKey);
    }

    public function downloadFile($filePath, $fileHash, $params = [])
    {
        return $this->requestRemoteFile('core/download', [
            'item' => json_encode($params),
        ], $filePath, $fileHash);
    }

    protected function getCacheKey($fileName, $suffix)
    {
        return $this->cachePrefix.$fileName.'_'.md5(serialize($suffix));
    }

    protected function requestRemoteData($url, $params = [])
    {
        $result = null;

        try {
            $curl = $this->prepareRequest($url, $params);
            $result = curl_exec($curl);

            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($httpCode == 500)
                throw new Exception('Server error try again');

            curl_close($curl);
        } catch (Exception $ex) {
            throw new Exception('Server responded with error: '.$ex->getMessage());
        }

        $response = null;
        try {
            $response = @json_decode($result, TRUE);
        } catch (Exception $ex) {
        }

        if (isset($response['message']) AND !in_array($httpCode, [200, 201])) {
            if (isset($response['errors']))
                Log::debug('Server validation errors: '.print_r($response['errors'], TRUE));

            throw new Exception($response['message']);
        }

        return $response;
    }

    protected function requestRemoteFile($url, $params = [], $filePath, $fileHash)
    {
        if (!is_dir($fileDir = dirname($filePath)))
            throw new Exception("Downloading failed, download path ({$filePath}) not found.");

        try {
            $curl = $this->prepareRequest($url, $params);
            $fileStream = fopen($filePath, 'wb');
            curl_setopt($curl, CURLOPT_FILE, $fileStream);
            curl_exec($curl);

            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($httpCode == 500)
                throw new Exception('Server error try again');

            curl_close($curl);
            fclose($fileStream);
        } catch (Exception $ex) {
            throw new Exception('Server responded with error: '.$ex->getMessage());
        }

        $fileSha = sha1_file($filePath);

        if ($fileHash != $fileSha) {
            Log::info(file_get_contents($filePath));
            @unlink($filePath);
            throw new Exception("Download failed, File hash mismatch: {$fileHash} (expected) vs {$fileSha} (actual)");
        }

        return TRUE;
    }

    protected function prepareRequest($uri, $params)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, Config::get('system.hubEndpoint', static::ENDPOINT).'/'.$uri);
        curl_setopt($curl, CURLOPT_USERAGENT, Request::userAgent());
        curl_setopt($curl, CURLOPT_TIMEOUT, 3600);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_REFERER, url()->current());
        curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);

        $params['url'] = base64_encode(root_url());

        if ($siteKey = $this->getSecurity()) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, ["TI-Rest-Key: bearer {$siteKey}"]);
        }

        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params, '', '&'));

        return $curl;
    }
}