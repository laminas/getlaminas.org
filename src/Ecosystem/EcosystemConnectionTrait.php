<?php

declare(strict_types=1);

namespace GetLaminas\Ecosystem;

use function assert;
use function base64_decode;
use function curl_exec;
use function curl_init;
use function curl_setopt;
use function explode;
use function is_array;
use function is_string;
use function json_decode;
use function sprintf;

use const CURLOPT_FOLLOWLOCATION;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_POST;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_URL;

trait EcosystemConnectionTrait
{
    private function initCurl(): void
    {
        if ($this->ghToken === null || $this->ghToken === '') {
            $variables = json_decode(base64_decode($_ENV['PLATFORM_VARIABLES']), true);
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

    private function getSocialPreview(string $package): string
    {
        $packageId    = explode('/', $package);
        $graphQlQuery = sprintf(
            '{"query": "query {repository(owner: \"%s\", name: \"%s\"){openGraphImageUrl}}"}',
            $packageId[0],
            $packageId[1]
        );

        curl_setopt($this->githubCurl, CURLOPT_URL, 'https://api.github.com/graphql');
        curl_setopt($this->githubCurl, CURLOPT_POST, true);
        curl_setopt($this->githubCurl, CURLOPT_POSTFIELDS, $graphQlQuery);

        $rawResult = curl_exec($this->githubCurl);
        assert(is_string($rawResult));

        /** @var array{data: array{repository: array{openGraphImageUrl: string}}} | array{data: array} $githubResult */
        $githubResult = json_decode($rawResult, true);

        return $githubResult['data']['repository']['openGraphImageUrl'] ?? '';
    }
}
