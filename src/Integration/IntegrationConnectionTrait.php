<?php

declare(strict_types=1);

namespace GetLaminas\Integration;

use Exception;

use function assert;
use function base64_decode;
use function curl_close;
use function curl_exec;
use function curl_init;
use function curl_setopt;
use function explode;
use function fclose;
use function fopen;
use function is_array;
use function is_string;
use function json_decode;
use function sprintf;

use const CURLOPT_FILE;
use const CURLOPT_FOLLOWLOCATION;
use const CURLOPT_HEADER;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_POST;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_URL;
use const JSON_THROW_ON_ERROR;

trait IntegrationConnectionTrait
{
    private function initCurl(): void
    {
        if ($this->ghToken === null || $this->ghToken === '') {
            $variables = json_decode(base64_decode($_ENV['PLATFORM_VARIABLES']), true, 512, JSON_THROW_ON_ERROR);
            assert(is_array($variables));
            assert(isset($variables['REPO_TOKEN']));

            $this->ghToken = $variables['REPO_TOKEN'];
        }
        assert(is_string($this->ghToken));

        $headers = [
            'Accept: application/vnd.github+json',
            'X-GitHub-Api-Version: 2022-11-28',
            'User-Agent: getlaminas.org',
        ];

        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);

        $githubHeaders = [
            'Accept: application/vnd.github+json',
            'Authorization: Bearer ' . $this->ghToken,
            'X-GitHub-Api-Version: 2022-11-28',
            'User-Agent: getlaminas.org',
        ];

        $this->githubCurl = curl_init();
        curl_setopt($this->githubCurl, CURLOPT_HTTPHEADER, $githubHeaders);
        curl_setopt($this->githubCurl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->githubCurl, CURLOPT_RETURNTRANSFER, 1);
    }

    private function getPackageImage(string $package): string
    {
        $packageId    = explode('/', $package);
        $graphQlQuery = sprintf(
            '{"query": "query {repository(owner: \"%s\", name: \"%s\"){owner{avatarUrl}}}"}',
            $packageId[0],
            $packageId[1]
        );

        curl_setopt($this->githubCurl, CURLOPT_URL, 'https://api.github.com/graphql');
        curl_setopt($this->githubCurl, CURLOPT_POST, true);
        curl_setopt($this->githubCurl, CURLOPT_POSTFIELDS, $graphQlQuery);

        $rawResult = curl_exec($this->githubCurl);
        assert(is_string($rawResult));

        /** @var array{data: array{repository: array{owner: array{avatarUrl: string}}}}|null $githubResult */
        $githubResult = json_decode($rawResult, true, 512, JSON_THROW_ON_ERROR);
        $image        = '';

        if ($githubResult === null || isset($githubResult['errors'])) {
            return $image;
        }

        return $this->cachePackageOwnerAvatar(
            $githubResult['data']['repository']['owner']['avatarUrl'],
            sprintf('%s-%s.png', $packageId[0], $packageId[1])
        );
    }

    private function cachePackageOwnerAvatar(string $avatarUrl, string $file): string
    {
        try {
            $ch = curl_init($avatarUrl);
            assert($ch !== false);
            $fp = fopen('public/images/packages/' . $file, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
        } catch (Exception $exception) {
            return '';
        }

        return $file;
    }
}
