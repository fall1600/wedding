<?php
namespace Widget\CategoryBundle\Tests\InitDatabase;

use Backend\BaseBundle\Event\TestInitEvent;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Observe;
use Backend\BaseBundle\Model;
use Symfony\Component\HttpFoundation\File\File;
use Widget\CategoryBundle\Model\Category;
use Widget\CategoryBundle\Model\CategoryThread;
use Widget\PhotoBundle\File\PhotoUploadFile;
use Widget\PhotoBundle\Image\PhotoConfigFinder;
use Widget\PhotoBundle\Image\Resizer;

/**
 * @Service
 */
class initCategory
{
    /** @var  \Faker\Generator */
    protected $faker;

    /** @var  Resizer */
    protected $resizer;

    /** @var  PhotoConfigFinder */
    protected $configFinder;

    /**
     * @InjectParams({
     *     "faker" = @Inject("faker.generator", required = false),
     * })
     */
    public function injectFaker(\Faker\Generator $faker = null)
    {
        $this->faker = $faker;
    }

    /**
     * @InjectParams({
     *     "resizer" = @Inject("widget_photo.image.resizer", required = false),
     * })
     */
    public function injectResizer(Resizer $resizer)
    {
        $this->resizer = $resizer;
    }

    /**
     * @InjectParams({
     *     "configFinder" = @Inject("widget.photo_bundle.config_finder", required = false),
     * })
     */
    public function injectConfigFinder(PhotoConfigFinder $configFinder)
    {
        $this->configFinder = $configFinder;
    }

    /**
     * @Observe(TestInitEvent::EVENT_TEST_INIT, priority = 100)
     */
    public function onInit(TestInitEvent $event)
    {
        $event->getOutput()->writeln("<info>Create Creategory</info>");
        $thread = new CategoryThread();
        $thread->setThread('product');
        $rootCategory = new Category();
        $rootCategory
            ->setName('root')
            ->setCategoryThread($thread)
            ->makeRoot()
        ;
        $rootCategory->save();

        for($i=0;$i<10;$i++){
            $this->appendChildCategory($rootCategory, true);
        }

        for($i=0;$i<10;$i++){
            $this->appendChildCategory($rootCategory, false);
        }
        $event->getOutput()->writeln("<comment>20</comment> <info>Categories created</info>");
    }

    protected function appendChildCategory(Category $rootCategory, $status)
    {
        $category = new Category();
        $category
            ->setName($this->faker->text(20))
            ->insertAsFirstChildOf($rootCategory)
            ->setStatus($status);
        if($this->resizer) {
            $this->attachPhoto($category);
        }
        $category->save();
        return $category;
    }

    protected function attachPhoto(Category $category)
    {
        $photoFile = new File($this->fakePhoto(1024, 768));

        $uploadPhoto = PhotoUploadFile::createFromUploadFile(
            $photoFile,
            $this->resizer,
            $this->configFinder->findConfig('product_category_cover')
        );
        $photo = $uploadPhoto->makePhoto();
        $photo->save();
        $category->setMetaData($photo);
    }

    protected function fakePhoto($width, $height)
    {
        //return $this->faker->image(sys_get_temp_dir(), $width, $height);
        return __DIR__.'/../Fixture/cats.jpg';
    }
}
