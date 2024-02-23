<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\CollectionsRepository;
use App\Repository\PageRepository;
use App\Repository\ProductRepository;
use App\Repository\SettingRepository;
use App\Repository\SlidersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $repoProduct;

    public function __construct(ProductRepository $repoProduct)
    {
        $this->repoProduct = $repoProduct;
    }
    
    #[Route('/', name: 'app_home')]
    public function index(
        PageRepository $pageRep,
        CollectionsRepository $collectionsRep ,
        SlidersRepository $slidersRepo,
        SettingRepository $settingRep , 
        CategoryRepository $categoriesRep,

        Request $request): Response
        {   $session = $request->getSession();
            $data = $settingRep->findAll();
            $sliders=$slidersRepo->findAll();
            $collections =$collectionsRep->findBy(['isMega'=> false]);
            $megacollections = $collectionsRep->findBy(['isMega'=> true]);
            $categories=$categoriesRep->findBy(['isMega'=> true]);
        
        // dd($data);
            $session->set('setting',$data[0]);
            $headerPages=$pageRep->findBy(['isHead'=> true]);
            $footerPages=$pageRep->findBy(['isFoot'=> true]);

            

            $session->set('headerPages',$headerPages);
            $session->set('footerPages',$footerPages);
            $session->set('categories',$categories);
            $session->set('megacollections',$megacollections);

            
        // dd([$productsBestSeller , $productsNewArrival, $productsFeatured ,$productsSpecialOffer]);

            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController',
                'sliders'=>$sliders,
                'collections'=>$collections,
                'productsBestSeller' => $this->repoProduct->findBy(['isBestSeller'=>true]),
                'productsNewArrival' => $this->repoProduct->findBy(['isNewArrival'=>true]),
                'productsFeatured' => $this->repoProduct->findBy(['isFeatured'=>true]),
                'productsSpecialOffer' => $this->repoProduct->findBy(['isSpecialOffer'=>true]),
               
            
            ]);
        }


    #[Route('/product/{slug}', name: 'app_product_by_slug')]
    public function showProduct(string $slug)
        {  
            $product=$this->repoProduct->findOneBy(['slug'=>$slug]);
             if(!$product){
                 //error redirection
                //    return $this->render('page/notfound.html.twig', [
                //     'controller_name' => 'PageController',
                //     ]);
                return $this->redirectToRoute('app_errorpage');
            }

            return $this->render('product/show_product.html.twig', [
                'controller_name' => 'HomeController',
                'product'=> $product,
            
               
            
            ]);
        }

        #[Route('/error', name: 'app_errorpage')]
        public function errorPage()
            {  
                    return $this->render('page/notfound.html.twig', [
                        'controller_name' => 'PageController',
                    ]);
                }

                
            


        
}
