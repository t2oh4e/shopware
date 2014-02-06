<?php

use Shopware\Components\Plugin\Manager;
use Shopware\Models\Plugin\Plugin;
use Shopware\Models\Shop\Template;
use Shopware\Theme;

class Shopware_Controllers_Backend_Template extends Shopware_Controllers_Backend_Application
{
    /**
     * Model which handled through this controller
     * @var string
     */
    protected $model = 'Shopware\Models\Shop\Template';

    /**
     * SQL alias for the internal query builder
     * @var string
     */
    protected $alias = 'template';

    /**
     * Override of the application controller
     * to trigger the theme and template registration when the
     * list should be displayed.
     */
    public function listAction()
    {
        $this->registerTemplates();

        $this->registerThemes();

        parent::listAction();
    }

    /**
     * The getList function returns an array of the configured class model.
     * The listing query created in the getListQuery function.
     * The pagination of the listing is handled inside this function.
     *
     * @param int $offset
     * @param int $limit
     * @param array $sort Contains an array of Ext JS sort conditions
     * @param array $filter Contains an array of Ext JS filters
     * @param array $wholeParams Contains all passed request parameters
     * @return array
     */
    protected function getList($offset, $limit, $sort = array(), $filter = array(), array $wholeParams = array())
    {
        $data = parent::getList($offset, $limit, $sort, $filter, $wholeParams);

        return $data;
    }


    public function assignAction()
    {

    }

    /**
     * Assigns the passed template (identified over the primary key)
     * to the passed shop (identified over the shop primary key)
     *
     * @param $shopId
     * @param $templateId
     */
    protected function assign($shopId, $templateId)
    {

    }

    /**
     * Iterates all Shopware 5 themes which
     * stored in the /engine/Shopware/Themes directory.
     * Each theme are stored as new Shopware\Models\Shop\Template.
     */
    protected function registerThemes()
    {
        $directories = new DirectoryIterator(
            $this->container->getParameter('kernel.root_dir') . '/engine/Shopware/Themes'
        );

        $themes = $this->initialThemes($directories);

        $pluginThemes = $this->initialPluginThemes();

        $themes = array_merge($themes, $pluginThemes);

        $this->resolveThemeParents($themes);
    }

    /**
     * Helper function which iterates all plugins
     * and registers their themes.
     * Returns an array with all registered plugin themes.
     *
     * @return array
     */
    protected function initialPluginThemes()
    {
        $plugins = $this->getActivePlugins();

        /** @var Manager $pluginManager */
        $pluginManager = $this->container->get('shopware.plugin_manager');

        $themes = array();

        /**@var $plugin Plugin*/
        foreach($plugins as $plugin) {
            //get the plugin bootstrap to get access on the plugin path.
            $bootstrap = $pluginManager->getPluginBootstrap(
                $plugin
            );

            //bootstrap not found? skip plugin.
            if (!$bootstrap instanceof Shopware_Components_Plugin_Bootstrap) {
                continue;
            }

            //check if plugin contains themes
            if (!file_exists($bootstrap->Path() . DIRECTORY_SEPARATOR . 'Themes')) {
                continue;
            }

            $directories = new DirectoryIterator(
                $bootstrap->Path() . DIRECTORY_SEPARATOR . 'Themes'
            );

            //the initialThemes function create for each theme directory a shop template.
            $pluginThemes = $this->initialThemes($directories);

            if (empty($pluginThemes)) {
                continue;
            }

            //merge the plugin themes into the already detected plugin themes.
            $themes = array_merge($themes, $pluginThemes);

            //iterate themes to set the plugin id.
            foreach($themes as $theme) {
                /**@var $theme Theme*/
                /**@var $template Template*/
                $template = $this->getRepository()->findOneBy(array(
                    'template' => $theme->getTemplate()
                ));
                
                $template->setPlugin($plugin);
            }

            $this->getManager()->flush();
        }

        return $themes;
    }

    /**
     * Returns an object list with all installed and activated plugins.
     *
     * @return array
     */
    protected function getActivePlugins()
    {
        $builder = $this->getManager()->createQueryBuilder();

        $builder->select(array('plugins'))
            ->from('Shopware\Models\Plugin\Plugin', 'plugins')
            ->where('plugins.active = true')
            ->andWhere('plugins.installed IS NOT NULL');

        return $builder->getQuery()->getResult(
            \Doctrine\ORM\AbstractQuery::HYDRATE_OBJECT
        );
    }


    /**
     * Helper function which iterates the engine\Shopware\Themes directory
     * and registers all stored themes within the directory as \Shopware\Models\Shop\Template.
     * @param DirectoryIterator $directories
     * @return array
     */
    protected function initialThemes(DirectoryIterator $directories)
    {
        $themes = array();

        /**@var $directory DirectoryIterator */
        foreach ($directories as $directory) {
            //check valid directory
            if ($directory->isDot() || !$directory->isDir()) {
                continue;
            }

            $theme = $this->getThemeClass($directory);

            $data = $this->getThemeDefinition($theme);

            $template = $this->getRepository()->findOneBy(array(
                'template' => $theme->getTemplate()
            ));

            if (!$template instanceof Template) {
                $template = new Template();
                $this->getManager()->persist($template);
            }

            $template->fromArray($data);

            $this->getManager()->flush($template);

            $themes[] = $theme;
        }

        return $themes;
    }

    /**
     * Helper function which resolves the theme parent for each
     * passed theme
     *
     * @param array $themes
     * @throws Exception
     */
    protected function resolveThemeParents(array $themes)
    {
        /**@var $theme Theme */
        foreach ($themes as $theme) {
            if ($theme->getExtend() === null) {
                continue;
            }

            $template = $this->getRepository()->findOneBy(array(
                'template' => $theme->getTemplate()
            ));

            $parent = $this->getRepository()->findOneBy(array(
                'template' => $theme->getExtend()
            ));

            if (!$parent instanceof Template) {
                throw new Exception(sprintf(
                    "Parent %s of theme %s not found",
                    array(
                        $theme->getExtend(),
                        $theme->getTemplate()
                    )
                ));
            }

            $template->setParent($parent);

            $this->getManager()->flush();
        }
    }


    /**
     * Helper function which returns the theme information of the
     * passed theme.
     * Used to update the Shopware\Models\Shop\Template entity with
     * the theme data.
     *
     * @param Theme $theme
     * @return array
     */
    protected function getThemeDefinition(Theme $theme)
    {
        return array(
            'template' => $theme->getTemplate(),
            'name' => $theme->getName(),
            'author' => $theme->getAuthor(),
            'license' => $theme->getLicense(),
            'description' => $theme->getDescription(),
            'version' => 3,
            'esi' => true,
            'style' => true,
            'emotion' => true
        );
    }

    /**
     * Resolves the passed directory to a theme class.
     * Returns a new instance of the \Shopware\Theme
     *
     * @param DirectoryIterator $directory
     * @return Theme
     * @throws Exception
     */
    protected function getThemeClass(DirectoryIterator $directory)
    {
        $namespace = "Shopware\\Themes\\" . $directory->getFilename();
        $class = $namespace . "\\Theme";

        $file = $directory->getPathname() . DIRECTORY_SEPARATOR . 'Theme.php';

        if (!file_exists($file)) {
            throw new Exception(sprintf(
                "Theme directory %s contains no Theme.php",
                $directory->getFilename()
            ));
        }

        require_once $file;

        return new $class();
    }

    /**
     * Iterates all Shopware 4 templates which
     * stored in the /templates/ directory.
     * Each template are stored as new Shopware\Models\Shop\Template.
     */
    protected function registerTemplates()
    {
        $directories = new DirectoryIterator(
            $this->container->getParameter('kernel.root_dir') . '/templates'
        );

        /**@var $directory DirectoryIterator */
        foreach ($directories as $directory) {
            //check valid directory
            if ($directory->isDot()
                || !$directory->isDir()
                || strpos($directory->getFilename(), '_') === 0
            ) {
                continue;
            }

            //draw template information over the directory
            $data = $this->getTemplateDefinition($directory);

            $template = $this->getRepository()->findOneBy(array(
                'template' => $data['template']
            ));

            if (!$template instanceof Template) {
                $template = new Template();
                $this->getManager()->persist($template);
            }

            $template->fromArray($data);
            $this->getManager()->flush();
        }
    }

    /**
     * Helper function which returns the template information for
     * the passed shopware 4 template directory.
     *
     * @param DirectoryIterator $directory
     * @return array Contains the template data
     */
    protected function getTemplateDefinition(DirectoryIterator $directory)
    {
        $info = $directory->getPathname() . '/info.json';

        $data = array();
        if (file_exists($info)) {
            $data = (array)Zend_Json::decode(file_get_contents($info));
        }

        $data['template'] = $directory->getFilename();

        if (!isset($data['version'])) {
            $data['version'] = strpos($directory->getFilename(), 'emotion_') !== 0 ? 1 : 2;
        }

        if (!isset($data['name'])) {
            $data['name'] = ucwords(str_replace('_', ' ', $directory->getFilename()));
        }

        return $data;
    }
}