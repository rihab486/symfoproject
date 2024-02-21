<?php

namespace App\EventSubscriber;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class DatabaseActivitySubscriber implements EventSubscriberInterface
{
    private $appkernel;
    private $rootDir;
    public function __construct(KernelInterface $appkernel)
    {
        $this->appkernel = $appkernel;
        $this->rootDir =$appkernel->getProjectDir();
    }

    public  function getSubscribedEvents(): array
    {
        return [
            Events::postRemove,
        ];
    }
    public function postRemove(PostRemoveEventArgs $args): void
    {
       $this->logActivity('remove', $args->getObject());
    }
    public function logActivity(string $action, mixed $entity): void
    {

       
       if (($entity instanceof Product) && $action === "remove" ){

        $listimageurls = $entity->getImageUrls();
        foreach($listimageurls as $imgurl) {
            $filelink = $this->rootDir."/public/assets/images/products/".$imgurl;
           // dd($filelink);
            $this->deleteImage($filelink);
        }
        
       }
       if (($entity instanceof Category) && $action === "remove" ){
        $filename=$entity->getImageUrl();
        $filelink = $this->rootDir."/public/assets/images/categories/".$filename ;
        $this->deleteImage($filelink);
     }
    // dd($entity);
    }
    public function deleteImage(string $filelink): void
    {
        try{
            $result= unlink($filelink);
          //  dd($result);

            }catch(\Throwable $th){

            }
    }
}
