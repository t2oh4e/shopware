<?php

namespace Shopware\DependencyInjection\Bridge;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\Driver\DriverChain;
use Shopware\Components\Model\CategoryDenormalization;
use Shopware\Components\Model\CategorySubscriber;
use Shopware\Components\Model\Configuration;
use Shopware\Components\Model\EventSubscriber;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Order\OrderHistorySubscriber;

class Models
{

    protected $config;
    protected $modelPath;
    protected $loader;
    protected $eventManager;
    protected $db;
    protected $resourceLoader;

    public function __construct(
        Configuration $config,
        $modelPath,
        \Enlight_Loader $loader,
        \Enlight_Event_EventManager $eventManager,
        \Enlight_Components_Db_Adapter_Pdo_Mysql $db,
        $resourceLoader
    ) {
        $this->config = $config;
        $this->modelPath = $modelPath;
        $this->loader = $loader;
        $this->eventManager = $eventManager;
        $this->db = $db;
        $this->resourceLoader = $resourceLoader;
    }

    public function factory()
    {
        // register standard doctrine annotations
        AnnotationRegistry::registerFile(
            'Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php'
        );

        // register symfony validation annotions
        AnnotationRegistry::registerAutoloadNamespace(
            'Symfony\Component\Validator\Constraint',
            realpath(__DIR__ . '/../../vendor/symfony/validator')
        );

        $cachedAnnotationReader = $this->config->getAnnotationsReader();

        $annotationDriver = new AnnotationDriver(
            $cachedAnnotationReader,
            array(
                $this->modelPath,
                $this->config->getAttributeDir(),
            )
        );

        $this->loader->registerNamespace(
            'Shopware\Models\Attribute',
            $this->config->getAttributeDir()
        );

        // create a driver chain for metadata reading
        $driverChain = new DriverChain();

        // register annotation driver for our application
        $driverChain->addDriver($annotationDriver, 'Shopware\\Models\\');
        $driverChain->addDriver($annotationDriver, 'Shopware\\CustomModels\\');

        $this->resourceLoader->Bootstrap()->registerResource('ModelAnnotations', $annotationDriver);

        $this->config->setMetadataDriverImpl($driverChain);

        // Create event Manager
        $eventManager = new EventManager();

        // Create new shopware event subscriber to handle the entity lifecycle events.
        $lifeCycleSubscriber = new EventSubscriber(
            $this->$eventManager
        );
        $eventManager->addEventSubscriber($lifeCycleSubscriber);

        $categorySubscriber = new CategorySubscriber();

        //TODO Use the new resource loader
        $this->resourceLoader->Bootstrap()->registerResource('CategorySubscriber', $categorySubscriber);
        $eventManager->addEventSubscriber($categorySubscriber);

        $eventManager->addEventSubscriber(new OrderHistorySubscriber());

        $categoryDenormalization = new CategoryDenormalization(
            $this->db->getConnection()
        );

        //TODO Use the new resource loader
        $this->resourceLoader->Bootstrap()->registerResource('CategoryDenormalization', $categoryDenormalization);

        // now create the entity manager and use the connection
        // settings we defined in our application.ini
        $conn = DriverManager::getConnection(
            array('pdo' => $this->db->getConnection()),
            $this->config,
            $eventManager
        );

        $conn->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        $conn->getDatabasePlatform()->registerDoctrineTypeMapping('bit', 'boolean');

        $entityManager = ModelManager::create(
            $conn, $this->config, $eventManager
        );

        \Doctrine\ORM\Proxy\Autoloader::register(
            $this->config->getProxyDir(),
            $this->config->getProxyNamespace(),
            function ($proxyDir, $proxyNamespace, $className) use ($entityManager) {
                if (0 === stripos($className, $proxyNamespace)) {
                    $fileName = str_replace('\\', '', substr($className, strlen($proxyNamespace) + 1));
                    if (!is_file($fileName)) {
                        $classMetadata = $entityManager->getClassMetadata($className);
                        $entityManager->getProxyFactory()->generateProxyClasses(array($classMetadata), $proxyDir);
                    }
                }
            }
        );

        return $entityManager;
    }
}
