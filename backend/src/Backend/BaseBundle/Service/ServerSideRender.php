<?php
namespace Backend\BaseBundle\Service;


use Backend\BaseBundle\Exception\ErrorResponseException;
use Backend\BaseBundle\Model\Site;
use Backend\BaseBundle\Model\SiteQuery;
use Backend\BaseBundle\SiteConfig\SiteConfigBuilder;
use GuzzleHttp\Client;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;
use RickySu\Tagcache\Adapter\TagcacheAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @DI\Service("server_side_render")
 */
class ServerSideRender
{
    const ENGINE_PHANTOMJS = 'phantomjs';
    const ENGINE_PUPPETEER = 'puppeteer';

    protected $cacheDir;
    protected $kernelRootDir;
    protected $allowOrigins;
    /** @var  TagcacheAdapter */
    protected $tagCache;
    protected $puppeteerApi;

    /**
     * @InjectParams({
     *    "tagCache" = @Inject("tagcache", required=false)
     * })
     */
    public function injectTagCache($tagCache)
    {
        $this->tagCache = $tagCache;
    }

    /**
     * @InjectParams({
     *    "allowOrigins" = @Inject("%allow_origins%"),
     * })
     */
    public function injectAllowOrigin($allowOrigins)
    {
        $this->allowOrigins = $allowOrigins;
    }

    /**
     * @InjectParams({
     *    "puppeteerApi" = @Inject("%puppeteer_api%"),
     * })
     */
    public function injectPuppeteerAPI($puppeteerApi)
    {
        $this->puppeteerApi = $puppeteerApi;
    }

    /**
     * @InjectParams({
     *     "cacheDir" = @Inject("%kernel.cache_dir%"),
     *     "kernelRootDir" = @Inject("%kernel.root_dir%")
     * })
     */
    public function injectDir($cacheDir, $kernelRootDir)
    {
        $this->cacheDir = $cacheDir;
        $this->kernelRootDir = $kernelRootDir;
    }

    protected function makeTempName()
    {
        return tempnam($this->cacheDir, md5(self::class));
    }

    /**
     * @param $url
     * @return Response
     */
    public function render($url, $engine = self::ENGINE_PHANTOMJS)
    {
        if($url === null){
            throw new BadRequestHttpException();
        }

        $parsed = parse_url($url);

        if(!$parsed || !isset($parsed['host']) || !isset($parsed['scheme']) || !preg_match('/^https?$/i', $parsed['scheme'])) {
            throw new NotAcceptableHttpException();
        }

        $hostPort = $parsed['host'];
        if(isset($parsed['port'])){
            $hostPort.=":{$parsed['port']}";
        }

        if(!$this->allowOrigins || !in_array($hostPort, $this->allowOrigins)){
            throw new AccessDeniedHttpException();
        }

        return $this->renderWithNoCheck($url, $engine);
    }

    protected function readFromCache($key)
    {
        if($this->tagCache === null){
            return false;
        }
        return $this->tagCache->get($key);
    }

    protected function storeCache($key, $data)
    {
        if($this->tagCache === null){
            return null;
        }

        return $this->tagCache->set($key, $data, array(), 3600*3); //3 hours
    }

    protected function getFetchPhantomJSScriptPath()
    {
        return realpath($this->kernelRootDir.'/../seo/fetch.js');
    }

    /**
     * @param $url
     * @return mixed
     */
    protected function renderPhantomJs($url)
    {
        if (!($scriptPath = $this->getFetchPhantomJSScriptPath())) {
            throw new NotFoundHttpException();
        }

        $tmpOutput = $this->makeTempName();

        $builder = new ProcessBuilder();
        $process = $builder->setPrefix('env')
            ->setArguments(array(
                'phantomjs',
                '--proxy-type=none',
                '--ignore-ssl-errors=true',
                '--load-images=false',
                '--local-to-remote-url-access=true',
                $scriptPath,
                $tmpOutput,
                $url,
            ))
            ->getProcess();
        $process->run();
        $result = json_decode(file_get_contents($tmpOutput), true);
        @unlink($tmpOutput);
        return $result;
    }

    protected function renderPuppeteer($url)
    {
        $client = new Client();
        $response = $client->request('GET', $this->puppeteerApi, array(
            'query' => array(
                'url' => $url,
            ),
        ));

        if($response->getStatusCode() != Response::HTTP_OK){
            return array(
                'status' => false,
                'content' => $response->getBody(),
                'reason' => 'error',
                'statusCode' => $response->getStatusCode()
            );
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * @param $url
     * @param $engine
     * @return array|bool|mixed
     */
    public function renderWithNoCheck($url, $engine = self::ENGINE_PHANTOMJS)
    {
        if (($data = $this->readFromCache($url)) !== false) {
            return $data;
        }

        if ($engine == self::ENGINE_PHANTOMJS) {
            $result = $this->renderPhantomJs($url);
        } else {
            $result = $this->renderPuppeteer($url);
        }
        $this->storeCache($url, $result);
        return $result;
    }

}