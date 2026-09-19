<?php

declare(strict_types=1);

use Boundwize\StructArmed\Architecture;
use Boundwize\StructArmed\Preset\Preset;

return Architecture::define()
    ->withPresets(Preset::PSR4(), Preset::CODEQUALITY())
    ->layer('ContentParser', 'src/App/ContentParser')
    ->layer('Collection', 'src/App/AbstractCollection.php')
    ->layer('AppEnums', 'src/App/Enums')
    ->layer('AppHandler', 'src/App/Handler')
    ->layer('AppConsole', 'src/App/Console')
    ->layer('AppTemplate', 'src/App/Template')
    ->layer('AppService', [
        'src/App/AccessLoggerFactory.php',
        'src/App/EventDispatcherFactory.php',
        'src/App/LoggingErrorListener.php',
        'src/App/LoggingErrorListenerDelegator.php',
    ])
    ->layer('AppConfig', 'src/App/ConfigProvider.php')
    ->layer('Blog', [
        'src/Blog/BlogAuthor.php',
        'src/Blog/BlogPost.php',
        'src/Blog/CreateBlogPostFromDataArray.php',
        'src/Blog/FetchBlogPostEvent.php',
    ])
    ->layer('BlogMapper', 'src/Blog/Mapper')
    ->layer('BlogListener', 'src/Blog/Listener')
    ->layer('BlogHandler', 'src/Blog/Handler')
    ->layer('BlogConsole', 'src/Blog/Console')
    ->layer('BlogConfig', [
        'src/Blog/ConfigProvider.php',
        'src/Blog/PlatesFunctionsDelegator.php',
    ])
    ->layer('IntegrationEnums', 'src/Integration/Enums')
    ->layer('Integration', [
        'src/Integration/CreateIntegrationFromArrayTrait.php',
        'src/Integration/Integration.php',
        'src/Integration/IntegrationConnectionTrait.php',
    ])
    ->layer('IntegrationMapper', 'src/Integration/Mapper')
    ->layer('IntegrationHandler', 'src/Integration/Handler')
    ->layer('IntegrationConsole', 'src/Integration/Console')
    ->layer('IntegrationConfig', 'src/Integration/ConfigProvider.php')
    ->layer('ReleaseFeed', [
        'src/ReleaseFeed/Author.php',
        'src/ReleaseFeed/Release.php',
        'src/ReleaseFeed/Releases.php',
    ])
    ->layer('ReleaseFeedHandler', [
        'src/ReleaseFeed/DisplayFeedHandler.php',
        'src/ReleaseFeed/DisplayFeedHandlerFactory.php',
        'src/ReleaseFeed/ReceiveFeedItemHandler.php',
        'src/ReleaseFeed/ReceiveFeedItemHandlerFactory.php',
        'src/ReleaseFeed/VerifyTokenMiddleware.php',
        'src/ReleaseFeed/VerifyTokenMiddlewareFactory.php',
    ])
    ->layer('ReleaseFeedConfig', 'src/ReleaseFeed/ConfigProvider.php')
    ->layer('Security', [
        'src/Security/Advisory.php',
        'src/Security/AdvisoryFactory.php',
    ])
    ->layer('SecurityHandler', 'src/Security/Handler')
    ->layer('SecurityConsole', 'src/Security/Console')
    ->layer('SecurityConfig', 'src/Security/ConfigProvider.php')
    ->ruleset([
        'ContentParser'      => [],
        'Collection'         => ['ContentParser'],
        'AppEnums'           => [],
        'AppHandler'         => [],
        'AppConsole'         => ['AppHandler'],
        'AppTemplate'        => [],
        'AppService'         => [],
        'AppConfig'          => ['+AppConsole', 'AppService', 'AppTemplate'],
        'Blog'               => ['ContentParser'],
        'BlogMapper'         => ['+Blog'],
        'BlogListener'       => ['+BlogMapper'],
        'BlogHandler'        => ['+BlogMapper'],
        'BlogConsole'        => ['+BlogMapper'],
        'BlogConfig'         => ['+BlogConsole', 'BlogHandler', 'BlogListener'],
        'IntegrationEnums'   => [],
        'Integration'        => ['IntegrationEnums'],
        'IntegrationMapper'  => ['+Integration'],
        'IntegrationHandler' => ['+IntegrationMapper'],
        'IntegrationConsole' => ['+IntegrationHandler'],
        'IntegrationConfig'  => ['+IntegrationConsole'],
        'ReleaseFeed'        => [],
        'ReleaseFeedHandler' => ['ReleaseFeed'],
        'ReleaseFeedConfig'  => ['ReleaseFeedHandler'],
        'Security'           => ['+Collection'],
        'SecurityHandler'    => ['Security'],
        'SecurityConsole'    => ['+Security'],
        'SecurityConfig'     => ['+SecurityConsole', 'SecurityHandler'],
    ]);
