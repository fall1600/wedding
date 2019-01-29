<?php
namespace Backend\BaseBundle\ParamConvert;

use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Propel\Bundle\PropelBundle\Request\ParamConverter\PropelParamConverter as BaseParamConverter;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

/**
 * @DI\Service()
 * @DI\Tag("request.param_converter", attributes={"converter": "propel", "priority": 10})
 * @DI\Tag("kernel.cache_warmer")
 */
class PropelParamConverter extends BaseParamConverter implements CacheWarmerInterface
{
    protected $cacheDir;

    /** @var  Filesystem */
    protected $filesystem;
    protected $enableCachePropelConverter;

    /**
     * @DI\InjectParams({
     *    "enableCachePropelConverter" = @DI\Inject("%cache_propel_converter%")
     * })
     */
    public function injectCachePropelConverterConfig($enableCachePropelConverter)
    {
        $this->enableCachePropelConverter = $enableCachePropelConverter;
    }

    /**
     * @DI\InjectParams({
     *    "cacheDir" = @DI\Inject("%kernel.cache_dir%")
     * })
     */
    public function injectCacheDir($cacheDir)
    {
        $this->cacheDir = "$cacheDir/propel_param_converter";
    }

    /**
     * @DI\InjectParams()
     */
    public function injectFileSystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @DI\InjectParams()
     */
    public function setRouter(RouterInterface $router = null)
    {
        $this->router = $router;
    }

    /**
     * @param Request                $request
     * @param ParamConverter $configuration
     *
     * @return bool
     *
     * @throws \LogicException
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $classQuery = $configuration->getClass() . 'Query';
        $classPeer = $configuration->getClass() . 'Peer';
        $this->filters = array();
        $this->exclude = array();

        if (!class_exists($classQuery)) {
            throw new \Exception(sprintf('The %s Query class does not exist', $classQuery));
        }

        $tableMap = $classPeer::getTableMap();
        $pkColumns = $tableMap->getPrimaryKeyColumns();

        if (count($pkColumns) == 1) {
            $this->pk = strtolower($pkColumns[0]->getName());
        }

        $options = $configuration->getOptions();

        // Check route options for converter options, if there are non provided.
        if (empty($options) && $request->attributes->has('_route') && $this->router && $configuration instanceof ParamConverter) {
            $converterOption = $this->getConverterOption($request);
            if (!empty($converterOption[$configuration->getName()])) {
                $options = $converterOption[$configuration->getName()];
            }
        }

        if (isset($options['mapping'])) {
            // We use the mapping for calling findPk or filterBy
            foreach ($options['mapping'] as $routeParam => $column) {
                if ($request->attributes->has($routeParam)) {
                    if ($this->pk === $column) {
                        $this->pk = $routeParam;
                    } else {
                        $this->filters[$column] = $request->attributes->get($routeParam);
                    }
                }
            }
        } else {
            $this->exclude = isset($options['exclude'])? $options['exclude'] : array();
            $this->filters = $request->attributes->all();
        }

        $this->withs = isset($options['with'])? is_array($options['with'])? $options['with'] : array($options['with']) : array();

        // find by Pk
        if (false === $object = $this->findPk($classQuery, $request)) {
            // find by criteria
            if (false === $object = $this->findOneBy($classQuery, $request)) {
                if ($configuration->isOptional()) {
                    //we find nothing but the object is optional
                    $object = null;
                } else {
                    throw new \LogicException('Unable to guess how to get a Propel object from the request information.');
                }
            }
        }

        if (null === $object && false === $configuration->isOptional()) {
            throw new NotFoundHttpException(sprintf('%s object not found.', $configuration->getClass()));
        }

        $request->attributes->set($configuration->getName(), $object);

        return true;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    protected function getConverterOption(Request $request)
    {
        $routerName = $request->attributes->get('_route');
        if(false !== ($converterOption = $this->getConverterOptionCache($routerName))){
            return $converterOption;
        }
        return $this->storeConverterOptionCache($routerName, $this->router->getRouteCollection()->get($routerName)->getOption('propel_converter'));
    }

    protected function getConverterOptionCache($routerName)
    {
        if($this->enableCachePropelConverter && $this->filesystem->exists("{$this->cacheDir}/$routerName.cache")){
            return unserialize(file_get_contents("{$this->cacheDir}/$routerName.cache"));
        }
        return false;
    }

    protected function storeConverterOptionCache($routerName, $converterOption)
    {
        if($this->enableCachePropelConverter){
            file_put_contents("{$this->cacheDir}/$routerName.cache", serialize($converterOption));
        }
        return $converterOption;
    }

    /**
     * Checks whether this warmer is optional or not.
     *
     * Optional warmers can be ignored on certain conditions.
     *
     * A warmer should return true if the cache can be
     * generated incrementally and on-demand.
     *
     * @return bool true if the warmer is optional, false otherwise
     */
    public function isOptional()
    {
        return true;
    }

    public function warmUp($cacheDir)
    {
        $this->emptyCacheDir();
    }

    protected function emptyCacheDir()
    {
        if($this->filesystem->exists($this->cacheDir)){
            $this->filesystem->remove($this->cacheDir);
        }
        $this->filesystem->mkdir($this->cacheDir);
    }

}