<?php
namespace Backend\BaseBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Route;

/**
 * @DI\Service("backend_base.backend.config.builder", scope="prototype")
 */
class BackendConfigBuilder
{
    /** @var  \Twig_Environment */
    protected $twig;
    /** @var  Router */
    protected $router;
    protected $targetDir;
    protected $baseDir;
    protected $bundleName;
    protected $fieldsConfig;
    protected $enabledActions = array();

    /**
     * @DI\InjectParams()
     */
    public function injectTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @DI\InjectParams()
     */
    public function injectRouter(Router $router)
    {
        $this->router = $router;
    }

    public function build($bundleName, $targetDir, $baseDir)
    {
        $this->targetDir = $targetDir;
        $this->baseDir = $baseDir;
        $this->bundleName = $bundleName;

        foreach ($this->findConfigFiles("$baseDir/config") as $file) {
            $this->buildItem($file);
        }
    }

    protected function generateConfigEdit($editConfig, $moduleName)
    {
        $targetPath = "{$this->targetDir}/config/edit";
        $targetFile = "$targetPath/$moduleName.coffee";

        if(file_exists($targetFile)){
            return;
        }

        $config = array(
        );
        foreach ($editConfig['fields'] as $editItem => $itemConfig) {
            $itemConfig['name'] = $editItem;
            $defaultConfig = array(
                'text' => $this->fieldsConfig[$editItem]['label'],
                'type' => $this->fieldsConfig[$editItem]['type'],
                'required'  => $this->fieldsConfig[$editItem]['required'],
                'config' => $this->converFieldsConfig($moduleName, $this->fieldsConfig[$editItem]),
                'data' => $this->converFieldsData($moduleName, $this->fieldsConfig[$editItem]),
            );
            $config[] = $this->getFieldTypeDefine('edit', $editItem, array_merge($defaultConfig, $itemConfig));
        }
        $this->mkdir($targetPath);
        $js = json_encode($config, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        file_put_contents($targetFile, "module.exports = $js");
    }

    protected function generateConfigView($viewConfig, $moduleName)
    {
        $targetPath = "{$this->targetDir}/config/view";
        $targetFile = "$targetPath/$moduleName.coffee";

        if(file_exists($targetFile)){
            return;
        }

        $config = array(
        );
        foreach ($viewConfig['fields'] as $viewItem => $itemConfig) {
            $itemConfig['name'] = $viewItem;
            $defaultConfig = array(
                'text' => $this->fieldsConfig[$viewItem]['label'],
                'type' => $this->fieldsConfig[$viewItem]['type'],
                'config' => $this->converFieldsConfig($moduleName, $this->fieldsConfig[$viewItem]),
            );
            $config[] = $this->getFieldTypeDefine('view', $viewItem, array_merge($defaultConfig, $itemConfig));
        }
        $this->mkdir($targetPath);
        $js = json_encode($config, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        file_put_contents($targetFile, "module.exports = $js");
    }

    protected function converFieldsData($moduleName, $config)
    {
        $convert = array();
        if(isset($config['value'])) {
            $convert = array();
            foreach ($config['value'] as $value) {
                $convert[] = array(
                    'value' => $value,
                    'label' => "$value",
                );
            }
        }
        return $convert;
    }

    protected function converFieldsConfig($moduleName, $config)
    {
        $convert = array();
        if(isset($config['value'])) {
            $convert['label'] = 'label';
            $convert['value'] = 'value';
        }
        if(isset($config['source'])){
            $convert['api'] = "$moduleName.{$config['source']}";
            $convert['value'] = "id";
            $convert['name'] = $config['display_field'];
        }
        return $convert;
    }

    protected function generateConfigList($listConfig, $moduleName)
    {
        $targetPath = "{$this->targetDir}/config/list";
        $targetFile = "$targetPath/$moduleName.coffee";

        if(file_exists($targetFile)){
            return;
        }

        $config = array(
            'list' => array(),
            'extra' => array(),
            'action' => array()
        );
        if (isset($listConfig['index'])) {
            foreach ($listConfig['index'] as $listItem) {
                $config['list'][$listItem] = array(
                    'label' => $this->fieldsConfig[$listItem]['label'],
                    'type' => $this->fieldsConfig[$listItem]['type'],
                );
                $config['list'][$listItem] = $this->getFieldTypeDefine('list', $listItem, $config['list'][$listItem]);
            }
            $config = $this->generateConfigListSort($listConfig, $config);
        }

        if (isset($listConfig['search'])) {
            foreach ($listConfig['search'] as $listItem => $searchConfig) {
                $config['list'][$listItem]['search'] = true;
                $config['list'][$listItem]['searchConfig'] = array(
                    'key' => $listItem,
                    'type' => $this->fieldsConfig[$listItem]['type'],
                    'like' => in_array($this->fieldsConfig[$listItem]['type'], array('id', 'text')),
                );
                $config['list'][$listItem]['searchConfig'] = $this->getFieldTypeDefine('search', $listItem, $config['list'][$listItem]['searchConfig']);
            }
        }
        $listConfig['config'] = isset($listConfig['config'])?$listConfig['config']:array();

        $config['extra'] = array();
        $defaultConfigs = array(
            'new' => array(
                'name' => 'new',
                'label' => "action.new",
                'route' => "{$moduleName}-new",
                'target' => 'extra',
            ),
            'edit' => array(
                'name' => 'edit',
                'label' => "action.edit",
                'route' => "{$moduleName}-edit",
                'target' => 'action',
            ),
            'view' => array(
                'name' => 'view',
                'label' => "action.view",
                'route' => "{$moduleName}-view",
                'target' => 'action',
            ),
            'quick' => array(
                'name' => 'quick',
                'label' => "action.quick",
                'component' => "components/backendbase/partial/list/table/actions/quick.vue",
                'target' => 'action',
            ),
            'delete' => array(
                'name' => 'delete',
                'label' => "action.delete",
                'component' => "components/backendbase/partial/list/table/actions/delete.vue",
                'target' => 'action',
            ),
        );

        foreach ($this->enabledActions as $action => $actionConfig){
            $listActionConfig = isset($listConfig['config'][$action])?$listConfig['config'][$action]:array();
            $listConfig['config'][$action] = array_merge($listActionConfig, $actionConfig);
        }

        foreach($defaultConfigs as $item => $defaultConfig){
            if(isset($listConfig['config'][$item])){
                $target = $defaultConfig['target'];
                unset($defaultConfig['target']);
                $config[$target][] = array_merge($defaultConfig, $listConfig['config'][$item]);
            }
        }

        if(isset($listConfig['config']['batch'])){
            foreach ($listConfig['config']['batch'] as $item => $itemConfigs){
                foreach ($itemConfigs as $index => $itemConfig){
                    $defaultConfig = array(
                        'label' => "batch.${moduleName}.$index"
                    );
                    $config['list'][$item]['batchSetting'] = array_merge($defaultConfig, $itemConfig);
                }
            }
        }

        if(isset($listConfig['custom'])){
            foreach ($listConfig['custom'] as $item => $itemConfig){
                if(isset($itemConfig['component'])){
                    if(!preg_match('/\.vue$/i', $itemConfig['component'], $match)){
                        $itemConfig['component'] = "components/backendbase/partial/list/extra/{$itemConfig['component']}.vue";
                    }
                }
                $config['action'][] = array_merge(array(
                    'name' => $item,
                    'label' => "custom.$item",
                    'module' => $moduleName,
                ), $itemConfig);
            }
        }

        $js = json_encode($config, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        $js = preg_replace('/\"component\":\s*\"(.*?)\"/i', '"component": require("$1")', $js);
        $this->mkdir($targetPath);
        file_put_contents($targetFile, "module.exports = $js");
    }

    protected function isAssoc(array $arr)
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    protected function generateConfigListSort($listConfig, $config)
    {
        if(isset($listConfig['sort'])){
            foreach($listConfig['sort'] as $item){
                if(isset($config['list'][$item])){
                    $config['list'][$item]['sort'] = true;
                }
            }
            if(isset($listConfig['config'])&&isset($listConfig['config']['default_sort'])){
                $defaultSortItem = $listConfig['config']['default_sort'][0];
                $defaultSortOrder = isset($listConfig['config']['default_sort'][1])?($listConfig['config']['default_sort'][1]):'desc';
                $config['list'][$defaultSortItem]['defaultSorting'] = $defaultSortOrder;
            }
        }
        return $config;
    }

    protected function getFieldTypeDefine($group, $itemName, $itemConfig)
    {
        if(!isset($itemConfig['type'])){
            return $itemConfig;
        }

        $groupConfig = array(
            'list' => array(
                'id' => array('type' => 'id'),
                'text' => array('type' => 'text'),
                'longtext' => array('type' => 'text'),
                'html' => array('type' => 'text'),
                'boolean' => array(
                    'type' => 'text',
                    'config' => array(
                        'label_prefix' => 'default_value.boolean',
                    ),
                ),
                'datetime' => array('type' => 'datetime-local'),
                'enum' => array(
                    'type' => 'text',
                    'config' => array(
                        'label_prefix' => "values.$itemName",
                    ),
                ),
                'image' => array('type' => 'image'),
            ),
            'search' => array(
                'id' => array('type' => 'text'),
                'text' => array('type' => 'text'),
                'longtext' => array('type' => 'text'),
                'html' => array('type' => 'text'),
                'boolean' => array(
                    'type' => 'boolean',
                    'label_prefix' => 'default_value.boolean',
                ),
                'datetime' => array('type' => 'date'),
                'enum' => array('type' => 'text'),
            ),
            'edit' => array(
                'id' => array('type' => 'id'),
                'text' => array('type' => 'input'),
                'longtext' => array('type' => 'textarea'),
                'html' => array('type' => 'html'),
                'boolean' => array(
                    'type' => 'switch',
                    'config' => array(
                        "on" => "default_value.boolean.true",
                        "off" => "default_value.boolean.false",
                    ),
                ),
                'datetime' => array('type' => 'date'),
                'enum' => array(
                    'type' => 'choice',
                    'label_prefix' => "values.$itemName",
                ),
                'image' => array('type' => 'image'),
                'relation' => array('type' => 'choice'),
            ),
            'view' => array(
                'id' => array('type' => 'id'),
                'text' => array('type' => 'string'),
                'longtext' => array('type' => 'paragraph'),
                'html' => array('type' => 'paragraph'),
                'boolean' => array(
                    'type' => 'choice',
                ),
                'datetime' => array('type' => 'datetime'),
                'enum' => array(
                    'type' => 'string',
                    'label_prefix' => "values.$itemName",
                ),
                'image' => array('type' => 'image'),
                'relation' => array('type' => 'string'),
            ),
        );
        $itemConfig = array_merge($groupConfig[$group][$itemConfig['type']], $itemConfig);
        $itemConfig['type'] = $groupConfig[$group][$itemConfig['type']]['type'];

        if(isset($itemConfig['help']) && $itemConfig['help']){
            unset($itemConfig['help']);
            $itemConfig['config']['help'] = "helps.$itemName";
        }

        return $itemConfig;
    }

    protected function getFieldLabel($listItem, $moduleName)
    {
        if(isset($this->fieldsConfig[$listItem]['label'])){
            return $this->fieldsConfig[$listItem]['label'];
        }
        return "fields.{$moduleName}.$listItem";
    }

    protected function findConfigFiles($path)
    {
        return Finder::create()->name("*.json")->in($path)->files();
    }

    protected function mkdir($targetPath)
    {
        if(!is_dir($targetPath)){
            mkdir($targetPath, 0755, true);
        }
    }

    protected function buildItem(\SplFileInfo $file)
    {
        $config = json_decode(file_get_contents($file->getPathname()), true);
        if(!$config){
            return;
        }
        preg_match('/(.*)\.json$/i', $file->getFilename(), $match);
        $moduleName = $match[1];
        $this->fieldsConfig = $this->prepareDefaultFieldsConfig($config['fields'], $moduleName);

        $this->prepareEnabledActions($config);

        if(isset($config['list'])){
            $this->generateRouterList($config['list'], $moduleName);
            $this->generateActionsList($config['list'], $moduleName);
            $this->generateConfigList($config['list'], $moduleName);
        }

        if(isset($config['edit'])){
            $this->generateRouterEdit($config['edit'], $moduleName);
            $this->generateActionsEdit($config['edit'], $moduleName);
            $this->generateConfigEdit($config['edit'], $moduleName);
        }

        if(isset($config['new'])){
            if(!isset($config['new']['fields'])){
                $config['new']['fields'] =  $config['edit']['fields'];
            }

            $this->generateRouterNew($config['new'], $moduleName, isset($config['edit']));
            if(!isset($config['edit'])) {
                $this->generateActionsEdit($config['new'], $moduleName);
                $this->generateConfigEdit($config['new'], $moduleName);
            }
        }

        if(isset($config['view'])){
            $this->generateRouterView($config['view'], $moduleName);
            $this->generateActionsView($config['view'], $moduleName);
            $this->generateConfigView($config['view'], $moduleName);
        }
        $this->dumpRoute($moduleName, $config);
    }

    protected function prepareEnabledActions($config)
    {
        $this->enabledActions = array();
        foreach(array('new', 'view', 'edit') as $action) {
            if (isset($config[$action])) {
                $this->enabledActions[$action] = array();
                if (isset($config[$action]['roles'])) {
                    $this->enabledActions[$action]['roles'] = $config[$action]['roles'];
                }
            }
        }
    }

    protected function prepareDefaultFieldsConfig($fieldsConfig, $moduleName)
    {
        $defaultConfig = array('label' => '', 'type' => 'text', 'required' => false);
        foreach($fieldsConfig as $item => $config){
            $defaultConfig['label'] = "fields.{$moduleName}.$item";
            $fieldsConfig[$item] = array_merge($defaultConfig, $config);
        }
        return $fieldsConfig;
    }

    protected function dumpRoute($moduleName, $config)
    {
        $this->writeAPIRoute($moduleName, $config);
        $this->writeVueRoute($moduleName, $config);
    }

    protected function generateAPIAllowActions($config)
    {
        $listAllowActions = array();
        $editAllowActions = array();
        $newAllowActions = array();
        $viewAllowActions = array();

        if(isset($config['list'])){
            $listAllowActions = $this->generateListAPIAllowActions($config['list']);
        }

        if(isset($config['edit'])){
            $editAllowActions = $this->generateEditAPIAllowActions($config['edit']);
        }

        if(isset($config['new'])){
            $newAllowActions = $this->generateCreateAPIAllowActions($config['new']);
        }

        if(isset($config['view'])){
            $viewAllowActions = $this->generateViewAPIAllowActions($config['view']);
        }

        return array_unique(array_merge($listAllowActions, $editAllowActions, $newAllowActions, $viewAllowActions));
    }

    protected function generateViewAPIAllowActions($editConfig)
    {
        $allowActions = array('read');
        return $allowActions;
    }

    protected function generateEditAPIAllowActions($editConfig)
    {
        $allowActions = array('read', 'update');
        return $allowActions;
    }

    protected function generateCreateAPIAllowActions($editConfig)
    {
        $allowActions = array('create');
        return $allowActions;
    }

    protected function generateListAPIAllowActions($listConfig)
    {
        $allowActions = array('search');
        if(isset($listConfig['config'])){

            if(isset($listConfig['config']['view'])){
                $allowActions[] = 'read';
            }

            if(isset($listConfig['config']['new'])){
                $allowActions[] = 'create';
            }

            if(isset($listConfig['config']['delete'])){
                $allowActions[] = 'delete';
                $allowActions[] = 'batch';
            }

            if(isset($listConfig['config']['edit'])){
                $allowActions[] = 'read';
                $allowActions[] = 'update';
            }

            if(isset($listConfig['config']['batch'])){
                $allowActions[] = 'batch';
            }

        }
        return $allowActions;
    }

    protected function getAPIRoute($moduleName, $allowActions, $extraAcions = array())
    {
        $actionMatcher = array(
            'create' => array('POST', false, false),
            'update' => array('PUT', true, false),
            'search' => array('GET', false, false),
            'read' => array('GET', true, false),
            'delete' => array('DELETE', true, false),
            'batch' => array('PUT', false, false),
        );
        $actionMatcher = array_merge($actionMatcher, $extraAcions);
        $allowActions = array_merge($allowActions, array_keys($extraAcions));

        $apis = array();
        foreach($allowActions as $action) {
            if(!isset($actionMatcher[$action])){
                continue;
            }
            list($method, $hasIdParam, $isFileStream) = $actionMatcher[$action];
            $routeName = $this->generateRouteName($moduleName, $action);
            if ($route = $this->router->getRouteCollection()->get($routeName)) {
                $apiRoutePath = $this->getApiRoutePath($route->getPath());
                $apis[$action] = array(
                    'params' => array(),
                    'method' => $method,
                    'path' => $apiRoutePath,
                    'isFileStream' => $isFileStream,
                );
                if($hasIdParam){
                    $apis[$action]['params']['id'] = 'id';
                }
                $apis[$action]['params']['data'] = 'data';
            }
        }
        return $this->twig->render('BackendBaseBundle:BackendCode:api.coffee.twig', array('moduleName' => $moduleName, 'apis' => $apis));
    }

    protected function getApiRoutePath($path)
    {
        return preg_replace(array('/^\/admin\/api/', '/\{(\w+)\}/i'), array('', '#{$1}'), $path);
    }

    protected function getVueRoutePath($path)
    {
        return preg_replace(array('/\#\{(\w+)\}/i'), array(':$1'), $path);
    }

    protected function generateRouteName($moduleName, $action)
    {
        preg_match('/^widget(.*)$/', $this->bundleName, $match);
        return "widget_{$match[1]}_backendapi_{$moduleName}_{$action}";
    }

    protected function generateRouterList($listConfig, $moduleName)
    {
        $targetPath = "{$this->targetDir}/router/list";
        $targetFile = "$targetPath/$moduleName.vue";
        if(file_exists($targetFile)){
            return;
        }
        $codeTemplate = $this->twig->render("BackendBaseBundle:BackendCode/router:list.vue.twig", array(
            'bundleName' => $this->bundleName,
            'moduleName' => $moduleName,
        ));
        $this->mkdir($targetPath);
        file_put_contents($targetFile, $codeTemplate);
    }

    protected function generateRouterEdit($editConfig, $moduleName)
    {
        $targetPath = "{$this->targetDir}/router/edit";
        $targetFile = "$targetPath/$moduleName.vue";
        if(file_exists($targetFile)){
            return;
        }
        $codeTemplate = $this->twig->render("BackendBaseBundle:BackendCode/router:edit.vue.twig", array(
            'bundleName' => $this->bundleName,
            'moduleName' => $moduleName,
        ));
        $this->mkdir($targetPath);
        file_put_contents($targetFile, $codeTemplate);
    }

    protected function generateRouterView($viewConfig, $moduleName)
    {
        $targetPath = "{$this->targetDir}/router/view";
        $targetFile = "$targetPath/$moduleName.vue";
        if(file_exists($targetFile)){
            return;
        }
        $codeTemplate = $this->twig->render("BackendBaseBundle:BackendCode/router:view.vue.twig", array(
            'bundleName' => $this->bundleName,
            'moduleName' => $moduleName,
        ));
        $this->mkdir($targetPath);
        file_put_contents($targetFile, $codeTemplate);
    }

    protected function generateRouterNew($editConfig, $moduleName, $hasEdit = true)
    {
        $targetPath = "{$this->targetDir}/router/new";
        $targetFile = "$targetPath/$moduleName.vue";
        if(file_exists($targetFile)){
            return;
        }
        $codeTemplate = $this->twig->render("BackendBaseBundle:BackendCode/router:edit.vue.twig", array(
            'bundleName' => $this->bundleName,
            'moduleName' => $moduleName,
            'hasEdit' => $hasEdit,
        ));
        $this->mkdir($targetPath);
        file_put_contents($targetFile, $codeTemplate);
    }

    protected function generateActionsList($listConfig, $moduleName)
    {
        $targetPath = "{$this->targetDir}/actions/list";
        $targetFile = "$targetPath/$moduleName.coffee";
        if(file_exists($targetFile)){
            return;
        }

        $allowActions = $this->generateListAPIAllowActions($listConfig);

        $codeTemplate = $this->twig->render("BackendBaseBundle:BackendCode/actions:list.coffee.twig", array(
            'bundleName' => $this->bundleName,
            'moduleName' => $moduleName,
            'allowActions' => $allowActions,
        ));
        $this->mkdir($targetPath);
        file_put_contents($targetFile, $codeTemplate);
    }

    protected function generateActionsEdit($editConfig, $moduleName)
    {
        $targetPath = "{$this->targetDir}/actions/edit";
        $targetFile = "$targetPath/$moduleName.coffee";
        if(file_exists($targetFile)){
            return;
        }
        $codeTemplate = $this->twig->render("BackendBaseBundle:BackendCode/actions:edit.coffee.twig", array(
            'bundleName' => $this->bundleName,
            'moduleName' => $moduleName,
        ));
        $this->mkdir($targetPath);
        file_put_contents($targetFile, $codeTemplate);
    }

    protected function generateActionsView($viewConfig, $moduleName)
    {
        $targetPath = "{$this->targetDir}/actions/view";
        $targetFile = "$targetPath/$moduleName.coffee";
        if(file_exists($targetFile)){
            return;
        }
        $codeTemplate = $this->twig->render("BackendBaseBundle:BackendCode/actions:view.coffee.twig", array(
            'bundleName' => $this->bundleName,
            'moduleName' => $moduleName,
        ));
        $this->mkdir($targetPath);
        file_put_contents($targetFile, $codeTemplate);
    }

    /**
     * @param $moduleName
     * @param $config
     */
    protected function writeAPIRoute($moduleName, $config)
    {
        $targetPath = "{$this->targetDir}/actions/api";
        $targetFile = "$targetPath/$moduleName.coffee";

        if (file_exists($targetFile)) {
            return;
        }
        $extraActions = $this->buildExtraActions($config['fields']);
        if(isset($config['list']) && isset($config['list']['custom'])) {
            $extraActions = array_merge($extraActions, $this->buildExtraActions($config['list']['custom'], true));
        }
        $this->mkdir($targetPath);
        file_put_contents($targetFile, $this->getAPIRoute($moduleName, $this->generateAPIAllowActions($config), $extraActions));
    }

    protected function buildExtraActions($fields, $exportId = false)
    {
        $extraActions = array();
        foreach ($fields as $item => $config){
            if(isset($config['source'])){
                $extraActions[$config['source']] = array('GET', $exportId, isset($config['filestream']) && $config['filestream']);
            }
        }
        return $extraActions;
    }

    protected function generateVueRouteAction($moduleName, $config)
    {
        $allowActions = array();

        if(isset($config['list'])) {
            $allowActions[] = array(
                'path' => "/{$moduleName}s",
                'name' => "$moduleName-list",
                'component' => "components/{$this->bundleName}/router/list/$moduleName.vue",
            );
        }

        if(isset($config['edit'])){
            $allowActions[] = array(
                'path' => "/{$moduleName}/:id",
                'name' => "$moduleName-edit",
                'component' => "components/{$this->bundleName}/router/edit/$moduleName.vue",
            );
        }

        if (isset($config['view'])) {
            $allowActions[] = array(
                'path' => "/{$moduleName}/:id/view",
                'name' => "$moduleName-view",
                'component' => "components/{$this->bundleName}/router/view/$moduleName.vue",
            );
        }

        if (isset($config['new'])) {
            $allowActions[] = array(
                'path' => "/{$moduleName}s/new",
                'name' => "$moduleName-new",
                'component' => "components/{$this->bundleName}/router/new/$moduleName.vue",
            );
        }

        return $allowActions;
    }

    protected function writeVueRoute($moduleName, $config)
    {
        $targetPath = realpath($this->targetDir.'/../../');
        $targetFile = "$targetPath/routers.json";

        if (!file_exists($targetFile)) {
            return;
        }

        $routeData = $this->mergeVueRouter($moduleName, json_decode(file_get_contents($targetFile), true), $this->generateVueRouteAction($moduleName, $config));

        $json = json_encode($routeData, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        file_put_contents($targetFile, $json);
    }

    protected function mergeVueRouter($moduleName, $originConfig, $mergeConfig)
    {
        if(!isset($originConfig[$this->bundleName])){
            $originConfig[$this->bundleName] = array();
        }
        $routeNames = array_column($originConfig[$this->bundleName], 'name');
        foreach ($mergeConfig as $index => $route){
            if(in_array($route['name'], $routeNames)){
                continue;
            }
            $originConfig[$this->bundleName][] = $route;
        }
        return $originConfig;
    }
}
