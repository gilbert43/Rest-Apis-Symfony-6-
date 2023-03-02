<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;


use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * @Route("blog")
 */
class BlogController extends AbstractController
{
    const POSTS = [
        ['id'=>1,'title'=>'laravel 6'],
        ['id'=>2,'title'=>'React js'],
        ['id'=>3,'title'=>'Angular']
    ];


    /**
     * @Route("/add",methods={"POST"}, name="add-post")
     */
    public function add(Request $request,ManagerRegistry $doctrine){
        // $serializer = new Serializer();
        // $serializer = $this->get('serializer');
        // $post = $serializer->deserialize($request->getContent(),POST::class, 'json');

        $post = $request->getContent();

        $normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor()); //
    
        $serializer = new Serializer(array($normalizer, new ObjectNormalizer()), [new JsonEncoder()]);
    
    
        $post = $serializer->deserialize($post, POST::class, 'json');

        $em = $doctrine->getManager();
        $em->persist($post);
        $em->flush();

        return $this->json($post);
        
    }


    /**
     * @Route("/{page}", defaults={"page" : 10}, name="get-all" , methods={"GET"})
    */
    public function index($page, Request $request,ManagerRegistry $doctrine){
        $repository = $doctrine->getRepository(Post::class);
        $posts = $repository->findAll();

        return $this->json([
            'page' => $page,
            'name' => $request->get('name','yasweb'),
            'data' => array_map(function(Post $post){
               return  [
                    'title' => $post->getTitle(),
                    'content' => $post->getContent(),
                    'user' => $post->getAuthor(),
                    'link' => $this->generateUrl('get-post-by-id',['id' => $post->getId()])
                ];
            }, $posts)
        ]);
    }


    /**
     * @Route("/post/{id}", requirements={"id": "\d+"}, name="get-post-by-id" , methods={"GET"})
    */
    public function postById(Post $post,ManagerRegistry $doctrine){

        // $repository = $doctrine->getRepository(Post::class);
        // $post = $repository->find($id);
 
         return $this->json($post);
     }

    // /**
    //  * @Route("/{page}",defaults={"page" : 10}, name="get-all")
    //  */
    // public function index($page, Request $request){
    //     return $this->json([
    //         'page' => $page,
    //         'name' => $request->get('name','gilbert'),
    //         'data'=> array_map(function($post){
    //             return $this->generateUrl('get-post-by-id',['id' => $post['id']]);
    //         }, self::POSTS)
    //     ]);
    //     // return new JsonResponse([
    //     //     'page' => $page,
    //     //     'data'=> self::POSTS
    //     // ]);
    // }

    /**
     * @Route("/post/{id}",requirements={"id":"\d+"},name="get-post-by-id")
     */
    // public function postById($id){
    //     return new JsonResponse(self::POSTS[array_search($id,\array_column(self::POSTS,'id'))]);
    // }

    /**
     * @Route("/post/{title}",name="get-post-by-title")
     */
    // public function postByTitle($title){
    //     return new JsonResponse(self::POSTS[array_search($title,\array_column(self::POSTS,'title'))]);
    // }

    /**
     * @Route("/post/{id}", name="delete-post" , methods={"DELETE"})
    */
    public function destroy(Post $post,ManagerRegistry $doctrine){
        $em = $doctrine->getManager();

        $em->remove($post);
        $em->flush();

        return $this->json(null, 204);
    }
}
