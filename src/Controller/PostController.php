<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use App\Entity\Comment;
class PostController extends AbstractController
{
    #[Route('/post', name: 'app_post')]

    public function index(EntityManagerInterface $entityManager,
    PostRepository $postRepository): Response

    {
       
      // récupération de tous les posts
      $posts = $postRepository-> findAll();
//équivalent à SELECT * FROM post

//Opération findBy()
/* $where = [
    'url' => '1.jpg', 
    ];
    $order = [
    'created_at' => 'DESC', 
    ];
    $limit = 10; 
    $offset = 0; 
    $posts = $postRepository->findBy($where, $order, $limit, $offset);
*/
    
//-----------------. Opération findByX()
       //$posts = $postRepository->findByurl("1.jpg"); 

//--------------------flush
        $post = $postRepository->find(1); 
        $post->setTitle('Mon titre');
        $entityManager->flush();
//----------------------- L’opération Persist()
      //creation dun nouvel objet post
      $post =new Post();
      //definition des proprietes de l'objet
    $post->setTitle('Les fourmis');
    $post->setContent('Les fourmis sont des insectes sociaux appartenant à la famille des Formicidae. Elles vivent en colonies organisées et hiérarchisées');
    $post->setAuthor('Ounar Souha');
    $post->setCreatedAt(new \DateTimeImmutable());
    $post->setUpdatedAt(new \DateTimeImmutable());
    $post->setUrl('/1.jpg');

    $this->addFlash('success', 'Le post a été enregistré avec succès.');
//ajouter
    //$entityManager->persist($post);
    //$entityManager->flush();
    //sup
    $post1=$postRepository->find(23);
   // $entityManager->remove($post1,true);
    $entityManager->flush();
       return $this->render('post/index.html.twig',
       ['posts' => $posts]);

    }



//------------------comment----------------

#[Route('/addComment', name: 'add')]
public function add(EntityManagerInterface $entityManager,
PostRepository $postRepository): Response
{ 
$post=$postRepository->find(1); 
if($post)
{
$comment = new Comment();
$comment->setContent('Commentaire 1');
$comment->setAuthor('Ounar souhaila');
$comment->setDate(new \DateTimeImmutable());
$post->addComment($comment);
$entityManager->persist($post);
$entityManager->persist($comment);
$entityManager->flush();
} 
$posts = $postRepository->findAll();
return $this->render('post/index.html.twig',
['posts' => $posts]);
}
//-----------------------------aficher tt les posts------------------

#[Route('/post/all', name: 'allblogs')]
public function showAll(EntityManagerInterface $entityManager,
PostRepository $postRepository): Response
{ 
$posts = $postRepository->findAll();
return $this->render('post/index.html.twig',
['posts' => $posts]);
}

//----------------------/blog/{id} afficher par id --------------

#[Route('/post/{id}', name: 'Idblog')]
public function showId($id,EntityManagerInterface $entityManager,
PostRepository $postRepository): Response
{ 
$post1 = $postRepository->find($id);
return $this->render('post/id.html.twig',
['post' => $post1]);
}

//------------------- /blog/author/{id}
#[Route('/post/author/{author}', name: 'blogbyauthor')]
public function showbyauthor($author,EntityManagerInterface $entityManager,
PostRepository $postRepository): Response
{ 
$post2 = $postRepository->findByAuthor($author);
return $this->render('post/index.html.twig',
['posts' => $post2]);
}
//---------------------post/{id}/comment

#[Route('/post/{id}/comment', name: 'comment')]
public function showcomment($id,EntityManagerInterface $entityManager,
PostRepository $postRepository): Response
{ 
    
    $post1 = $postRepository->find($id);
    $comment = $post1->getComments();
    return $this->render('post/comment.html.twig',
    ['post' => $post1, 'comments' => $comment]);
    }

// /blog/remove/{id}/{comment}
#[Route('/blog/remove/{id}/{comment}', name: 'removecomment')]
public function rmcomment($id,$comment,EntityManagerInterface $entityManager,
PostRepository $postRepository): Response
{  
   
    $post = $postRepository->find($id);
    $comments = $post->getComments();
    $comment = $entityManager->getRepository(Comment::class)->find($comment);
    $post->removeComment($comment);
    $entityManager->remove($comment);
    $entityManager->flush();
    return $this->render('post/comment.html.twig',
    ['post' => $post, 'comments' => $comments]);
}
//----------------- /blog/removeComments------------
#[Route('/blog/removeComments', name: 'removeallcomment')]
public function rmcommentall(EntityManagerInterface $entityManager,
PostRepository $postRepository): Response
{  
   
    $post = $postRepository->find(1);
    $comments = $post->getComments();
    foreach ($comments as $comment) {
        $post->removeComment($comment);
        $entityManager->remove($comment);
    }
    $entityManager->flush();
    return $this->render('post/comment.html.twig',
    ['post' => $post, 'comments' => $comments]);
}

//--------------- supprimer tous les posts d’un auteur ainsi que les commentaires associés

#[Route('/blog/removepcomment', name: 'removepcomment')]
public function removepcomment(EntityManagerInterface $entityManager,
PostRepository $postRepository): Response
{  
    
    $posts = $postRepository->findByAuthor('souhaila');

    foreach ($posts as $post) {
        $comments = $post->getComments();
        foreach ($comments as $comment) {
            $post->removeComment($comment);
            $entityManager->remove($comment);
        }
        $entityManager->remove($post);
        $entityManager->flush();
    }
    
    $entityManager->flush();
    $ps=$postRepository->findAll();
    return $this->render('post/index.html.twig', ['posts' => $ps]);
}



}
?>